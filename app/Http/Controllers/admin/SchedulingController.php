<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Scheduling;
use App\Models\Employeegroup;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SchedulingController extends Controller
{
    // ====================================================
    // LISTADO DE PROGRAMACIONES
    // ====================================================
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        $query = Scheduling::with(['group','shift','vehicle','zone'])
            ->orderBy('date','asc')
            ->orderBy('group_id','asc');

        if ($from) $query->whereDate('date','>=',$from);
        if ($to)   $query->whereDate('date','<=',$to);

        $schedulings = $query->paginate(25);

        return view('schedulings.index', compact('schedulings','from','to'));
    }

    // ====================================================
    // FORMULARIO CREAR
    // ====================================================
    public function create()
    {
        $groups = Employeegroup::with(['shift','vehicle','zone'])->orderBy('name')->get();
        return view('schedulings.create', compact('groups'));
    }

    // ====================================================
    // STORE - GENERACIÓN MASIVA DE PROGRAMACIONES
    // ====================================================
    public function store(Request $request)
    {
        $request->validate([
            'group_id'  => 'required|exists:employeegroups,id',
            'from'      => 'required|date',
            'to'        => 'required|date|after_or_equal:from',
            'notes'     => 'nullable|string|max:120',
        ]);

        // --- Verificar disponibilidad antes de crear ---
        $availability = $this->checkAvailability($request)->getData(true);
        if (!$availability['ok']) {
            $msgs = [];
            foreach ($availability['conflicts'] as $c) {
                foreach ($c['items'] as $it) {
                    $msgs[] = "{$c['name']} no está disponible por {$it['reason']} en: ".implode(', ', $it['dates']);
                }
            }
            return back()->withErrors(['availability' => implode(' | ', $msgs)])->withInput();
        }

        // --- Crear programaciones ---
        $group = Employeegroup::with(['shift','vehicle','zone'])->findOrFail($request->group_id);
        $allowedDays = $this->parseSpanishDays($group->days);
        $period = CarbonPeriod::create(Carbon::parse($request->from), Carbon::parse($request->to));

        $created = 0;
        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $allowedDays)) continue;

            $exists = Scheduling::where('group_id', $group->id)
                ->whereDate('date', $date->toDateString())
                ->exists();

            if ($exists) continue;

            Scheduling::create([
                'group_id'   => $group->id,
                'shift_id'   => $group->shift_id,
                'vehicle_id' => $group->vehicle_id,
                'zone_id'    => $group->zone_id,
                'date'       => $date->toDateString(),
                'notes'      => $request->notes ?? '',
                'status'     => 1,
            ]);

            $created++;
        }

        return redirect()
            ->route('admin.schedulings.index', ['from'=>$request->from,'to'=>$request->to])
            ->with('success', "Se generaron {$created} programaciones.");
    }

    // ====================================================
    // DELETE
    // ====================================================
    public function destroy(Scheduling $scheduling)
    {
        $scheduling->delete();
        return back()->with('success', 'Programación eliminada.');
    }

    // ====================================================
    // UTILIDAD: PARSEAR DÍAS
    // ====================================================
    private function parseSpanishDays(string $daysCsv): array
    {
        $map = [
            'domingo'=>0, 'lunes'=>1, 'martes'=>2,
            'miércoles'=>3, 'miercoles'=>3,
            'jueves'=>4, 'viernes'=>5,
            'sábado'=>6, 'sabado'=>6,
        ];

        $out = [];
        foreach (explode(',', $daysCsv) as $d) {
            $key = trim(mb_strtolower($d,'UTF-8'));
            if (isset($map[$key])) $out[] = $map[$key];
        }
        return array_values(array_unique($out));
    }

    // ====================================================
    // INFO DE GRUPO (para las cards)
    // ====================================================
    public function groupInfo(Employeegroup $group)
    {
        $members = $group->employees()
            ->select('employee.*')
            ->orderBy('configgroups.id')
            ->get();

        $driver     = $members->firstWhere('type_id', 1);
        $assistants = $members->where('type_id', 2)->values();
        $assistant1 = $assistants->get(0);
        $assistant2 = $assistants->get(1);

        $today = Carbon::today();

        $buildPerson = function ($emp) use ($today) {
            if (!$emp) return null;

            $contract = $emp->activeContract()->first();

            $vacation = $emp->vacations()
                ->whereIn('status', ['approved', 'Approved', 'APPROVED'])
                ->where(function($q) use ($today) {
                    $q->where(function($qq) use ($today) {
                        $qq->whereNotNull('start_date')
                           ->whereDate('start_date','<=',$today)
                           ->whereDate('end_date','>=',$today);
                    })
                    ->orWhere(function($qq) use ($today) {
                        $qq->whereNotNull('request_date')
                           ->whereDate('request_date','<=',$today)
                           ->whereDate('end_date','>=',$today);
                    });
                })
                ->orderByDesc('id')
                ->first();

            $contractStart = $contract?->start_date?->format('Y-m-d');
            $contractEnd   = $contract?->end_date?->format('Y-m-d');
            $vacStart = $vacation?->start_date ?? $vacation?->request_date;
            $vacStart = $vacStart ? Carbon::parse($vacStart)->format('Y-m-d') : null;
            $vacEnd   = $vacation?->end_date ? Carbon::parse($vacation->end_date)->format('Y-m-d') : null;

            return [
                'full_name' => trim(($emp->lastnames ?? '').' '.($emp->names ?? '')),
                'contract'  => $contract ? [
                    'start_date' => $contractStart,
                    'end_date'   => $contractEnd,
                    'is_active'  => (bool) $contract->is_active,
                ] : null,
                'vacation'  => $vacation ? [
                    'start_date' => $vacStart,
                    'end_date'   => $vacEnd,
                    'status'     => $vacation->status,
                ] : null,
            ];
        };

        return response()->json([
            'driver'     => $buildPerson($driver),
            'assistant1' => $buildPerson($assistant1),
            'assistant2' => $buildPerson($assistant2),
        ]);
    }

    // ====================================================
    // NUEVO: CHECK AVAILABILITY (contrato / vacaciones)
    // ====================================================

    // Helper: días válidos dentro del rango
    private function scheduledDatesForGroup(Employeegroup $group, Carbon $from, Carbon $to): array
    {
        $allowed = $this->parseSpanishDays($group->days ?? '');
        $dates = [];
        foreach (CarbonPeriod::create($from, $to) as $d) {
            if (in_array($d->dayOfWeek, $allowed)) $dates[] = $d->copy();
        }
        return $dates;
    }

    // Helper: conflictos de un empleado
    private function conflictsForEmployee($employee, array $scheduledDates): array
    {
        $conflicts = [];

        $contract = $employee->activeContract()->first();
        if ($contract && $contract->end_date) {
            $end = Carbon::parse($contract->end_date)->endOfDay();
            $datesPastEnd = array_values(array_filter($scheduledDates, fn($d) => $d->greaterThan($end)));
            if (!empty($datesPastEnd)) {
                $conflicts[] = [
                    'reason' => 'Contrato expira',
                    'dates'  => array_map(fn($d)=>$d->toDateString(), $datesPastEnd),
                ];
            }
        } elseif (!$contract) {
            $conflicts[] = [
                'reason' => 'Sin contrato activo',
                'dates'  => array_map(fn($d)=>$d->toDateString(), $scheduledDates),
            ];
        }

        if (!empty($scheduledDates)) {
            $min = collect($scheduledDates)->min()->toDateString();
            $max = collect($scheduledDates)->max()->toDateString();

            $vacations = $employee->vacations()
                ->whereIn('status', ['Approved','approved','APPROVED'])
                ->where(function($q) use ($min, $max) {
                    $q->where(function($qq) use ($min,$max){
                        $qq->whereNotNull('request_date')
                           ->whereDate('request_date','<=',$max)
                           ->whereDate('end_date','>=',$min);
                    })->orWhere(function($qq) use ($min,$max){
                        $qq->whereNotNull('start_date')
                           ->whereDate('start_date','<=',$max)
                           ->whereDate('end_date','>=',$min);
                    });
                })
                ->get();

            foreach ($vacations as $vac) {
                $vStart = $vac->start_date ?? $vac->request_date;
                $vEnd   = $vac->end_date;
                if (!$vStart || !$vEnd) continue;

                $vs = Carbon::parse($vStart)->startOfDay();
                $ve = Carbon::parse($vEnd)->endOfDay();

                $datesOnVacation = array_values(array_filter($scheduledDates, fn($d) => $d->betweenIncluded($vs, $ve)));
                if (!empty($datesOnVacation)) {
                    $conflicts[] = [
                        'reason' => 'Vacaciones',
                        'dates'  => array_map(fn($d)=>$d->toDateString(), $datesOnVacation),
                    ];
                }
            }
        }

        return $conflicts;
    }

    // Endpoint AJAX: check disponibilidad
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:employeegroups,id',
            'from'     => 'required|date',
            'to'       => 'required|date|after_or_equal:from',
        ]);

        $group = Employeegroup::with('employees')->findOrFail($request->group_id);
        $from  = Carbon::parse($request->from)->startOfDay();
        $to    = Carbon::parse($request->to)->endOfDay();
        $scheduledDates = $this->scheduledDatesForGroup($group, $from, $to);

        $members = $group->employees()->orderBy('configgroups.id')->get();
        $driver     = $members->firstWhere('type_id', 1);
        $assistants = $members->where('type_id', 2)->values();
        $assistant1 = $assistants->get(0);
        $assistant2 = $assistants->get(1);

        $result = [
            'ok'        => true,
            'conflicts' => [],
            'byRole'    => ['driver'=>[],'assistant1'=>[],'assistant2'=>[]],
        ];

        $check = function($emp, $role) use (&$result, $scheduledDates) {
            if (!$emp) return;
            $c = $this->conflictsForEmployee($emp, $scheduledDates);
            if (!empty($c)) {
                $result['ok'] = false;
                $result['conflicts'][] = [
                    'employee_id'=>$emp->id,
                    'name'=>trim(($emp->names ?? '').' '.($emp->lastnames ?? '')),
                    'role'=>$role,
                    'items'=>$c,
                ];
                $result['byRole'][$role] = $c;
            }
        };

        $check($driver, 'driver');
        $check($assistant1, 'assistant1');
        $check($assistant2, 'assistant2');

        return response()->json($result);
    }
}
