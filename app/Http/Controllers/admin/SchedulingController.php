<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Scheduling;
use App\Models\Employeegroup;
use App\Models\Groupdetail;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SchedulingController extends Controller
{
    /* =========================
     * LISTADO
     * ========================= */
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

    public function show(Scheduling $scheduling)
    {
        abort(404);
    }

    /* =========================
     * CREATE (form)
     * ========================= */
    public function create()
    {
        $groups = Employeegroup::with(['shift','vehicle','zone'])->orderBy('name')->get();
        return view('schedulings.create', compact('groups'));
    }

    /* =========================
     * STORE (genera por rango + reemplazos por fecha)
     * ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'group_id'  => 'required|exists:employeegroups,id',
            'from'      => 'required|date',
            'to'        => 'required|date|after_or_equal:from',
            'notes'     => 'nullable|string|max:120',
        ]);

        // Grupo + miembros base
        $group = Employeegroup::with(['shift','vehicle','zone','employees'])->findOrFail($request->group_id);

        $members = $group->employees()
            ->select('employee.*')
            ->orderBy('configgroups.id')
            ->get();

        $driver     = $members->firstWhere('type_id', 1);
        $assistants = $members->where('type_id', 2)->values();
        $assistant1 = $assistants->get(0);
        $assistant2 = $assistants->get(1);

        $base = [
            'driver'     => $driver?->id,
            'assistant1' => $assistant1?->id,
            'assistant2' => $assistant2?->id,
        ];

        // Fechas del rango que coinciden con los días configurados del grupo
        $allowedDays = $this->parseSpanishDays($group->days ?? '');
        $period = CarbonPeriod::create(Carbon::parse($request->from), Carbon::parse($request->to));
        $dates  = [];
        foreach ($period as $d) {
            if (in_array($d->dayOfWeek, $allowedDays)) {
                $dates[] = $d->toDateString();
            }
        }
        if (empty($dates)) {
            return back()->withErrors(['range' => 'El rango no contiene días configurados para el grupo.'])->withInput();
        }

        // Reemplazos que vienen del formulario (hidden inputs)
        $replacements = $this->normalizeReplacements($request->input('replacements', []));

        // Validar: si hay conflictos (contrato/vacaciones) NO cubiertos por reemplazo, bloquear
        $uncovered = $this->uncoveredConflicts($group, $base, $dates, $replacements);
        if (!empty($uncovered)) {
            return back()->withErrors(['availability' => implode(' | ', $uncovered)])->withInput();
        }

        // Crear programaciones día a día y snapshot en groupdetails
        DB::transaction(function () use ($group, $dates, $base, $replacements, $request) {
            foreach ($dates as $date) {
                // evita duplicados exactos mismo grupo/fecha
                $exists = Scheduling::where('group_id', $group->id)
                    ->whereDate('date', $date)
                    ->exists();
                if ($exists) continue;

                // quién trabaja ese día (aplica reemplazo si cubre la fecha)
                $assigned = [
                    'driver'     => $this->employeeForDate('driver', $base, $replacements, $date),
                    'assistant1' => $this->employeeForDate('assistant1', $base, $replacements, $date),
                    'assistant2' => $this->employeeForDate('assistant2', $base, $replacements, $date),
                ];

                // scheduling
                $s = Scheduling::create([
                    'group_id'   => $group->id,
                    'shift_id'   => $group->shift_id,
                    'vehicle_id' => $group->vehicle_id,
                    'zone_id'    => $group->zone_id,
                    'date'       => $date,
                    'notes'      => $request->notes ?? '',
                    'status'     => 1,
                ]);

                // snapshot del personal ese día (evita repetir el mismo empleado dos veces)
                collect($assigned)->filter()->unique()->values()->each(function ($empId) use ($s) {
                    Groupdetail::create([
                        'scheduling_id' => $s->id,
                        'emplooyee_id'  => $empId, // (sic) columna
                    ]);
                });
            }
        });

        return redirect()
            ->route('admin.schedulings.index', ['from'=>$request->from,'to'=>$request->to])
            ->with('success', 'Programaciones generadas con reemplazos aplicados cuando correspondía.');
    }

    /* =========================
     * EDITAR (si lo usas para cambios manuales)
     * ========================= */
    public function edit(Scheduling $scheduling)
    {
        $scheduling->load(['group.shift','group.vehicle','group.zone','details.employee']);

        $drivers    = Employee::where('status', 1)->where('type_id', 1)->orderBy('lastnames')->get();
        $assistants = Employee::where('status', 1)->where('type_id', 2)->orderBy('lastnames')->get();

        $driverDetail = $scheduling->details->firstWhere('employee.type_id', 1);
        $aDetails     = $scheduling->details->filter(fn($d) => optional($d->employee)->type_id == 2)->values();

        $selectedDriverId = $driverDetail?->employee?->id;
        $selectedA1Id     = $aDetails->get(0)?->employee?->id;
        $selectedA2Id     = $aDetails->get(1)?->employee?->id;

        return view('schedulings.edit', compact(
            'scheduling', 'drivers', 'assistants', 'selectedDriverId', 'selectedA1Id', 'selectedA2Id'
        ));
    }

    public function update(Request $request, Scheduling $scheduling)
    {
        $request->validate([
            'driver_id'     => 'nullable|exists:employee,id',
            'assistant1_id' => 'nullable|exists:employee,id|different:driver_id|different:assistant2_id',
            'assistant2_id' => 'nullable|exists:employee,id|different:driver_id|different:assistant1_id',
        ], [
            'different' => 'No puede repetir el mismo trabajador en más de un rol.'
        ]);

        $ids = collect([
            'driver'     => $request->driver_id,
            'assistant1' => $request->assistant1_id,
            'assistant2' => $request->assistant2_id,
        ])->filter();

        if ($ids->isNotEmpty()) {
            $emps = Employee::whereIn('id', $ids->values())->get()->keyBy('id');

            if ($request->driver_id && optional($emps[$request->driver_id])->type_id != 1) {
                return back()->withErrors(['driver_id' => 'El seleccionado no es de tipo Conductor'])->withInput();
            }
            if ($request->assistant1_id && optional($emps[$request->assistant1_id])->type_id != 2) {
                return back()->withErrors(['assistant1_id' => 'El seleccionado no es de tipo Ayudante'])->withInput();
            }
            if ($request->assistant2_id && optional($emps[$request->assistant2_id])->type_id != 2) {
                return back()->withErrors(['assistant2_id' => 'El seleccionado no es de tipo Ayudante'])->withInput();
            }
        }

        $scheduling->details()->delete();

        foreach (['driver_id', 'assistant1_id', 'assistant2_id'] as $field) {
            if ($request->$field) {
                Groupdetail::create([
                    'scheduling_id' => $scheduling->id,
                    'emplooyee_id'  => $request->$field,
                ]);
            }
        }

        return redirect()->route('admin.schedulings.index')
            ->with('success', 'Personal actualizado para la programación.');
    }

    public function destroy(Scheduling $scheduling)
    {
        $scheduling->details()->delete();
        $scheduling->delete();
        return back()->with('success', 'Programación eliminada.');
    }

    /* =========================
     * INFO DE GRUPO (para cards)
     * ========================= */
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

    /* =========================
     * CHECK DISPONIBILIDAD (AJAX + backend)
     * ========================= */
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

    /* =========================
     * CANDIDATOS DISPONIBLES (AJAX)
     * ========================= */
   public function availableCandidates(Request $request)
{
    $request->validate([
        'type_id' => 'required|integer',
        'dates'   => 'required|string',
        'group_id' => 'required|exists:employeegroups,id', // 🔥 NUEVO: necesitamos el grupo
    ]);

    $dates = collect(explode(',', $request->dates))
        ->map(fn($d) => trim($d))
        ->filter();

    // 🔥 OBTENER LOS EMPLEADOS ACTUALES DEL GRUPO PARA EXCLUIRLOS
    $group = Employeegroup::with('employees')->findOrFail($request->group_id);
    $currentMemberIds = $group->employees->pluck('id')->toArray();

    $candidates = Employee::where('status', 1)
        ->where('type_id', $request->type_id)
        ->whereNotIn('id', $currentMemberIds) // 🔥 EXCLUIR MIEMBROS ACTUALES DEL GRUPO
        ->whereNotExists(function ($sub) use ($dates) {
            $sub->select(DB::raw(1))
                ->from('groupdetails as gd')
                ->join('schedulings as s', 's.id', '=', 'gd.scheduling_id')
                ->whereColumn('gd.emplooyee_id', 'employee.id')
                ->whereIn('s.date', $dates->all());
        })
        ->orderBy('lastnames')
        ->get(['id', 'names', 'lastnames'])
        ->map(fn($e) => [
            'id'   => $e->id,
            'name' => trim("{$e->lastnames} {$e->names}"),
        ]);

    return response()->json($candidates);
}


    /* =========================
     * HELPERS PRIVADOS
     * ========================= */

    // Convierte "Lunes,Miércoles,..." a números Carbon [0..6]
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

    private function scheduledDatesForGroup(Employeegroup $group, Carbon $from, Carbon $to): array
    {
        $allowed = $this->parseSpanishDays($group->days ?? '');
        $dates = [];
        foreach (CarbonPeriod::create($from, $to) as $d) {
            if (in_array($d->dayOfWeek, $allowed)) $dates[] = $d->copy();
        }
        return $dates;
    }

    // Conflictos de un empleado dado un arreglo de Carbon dates
    private function conflictsForEmployee($employee, array $scheduledDates): array
    {
        $conflicts = [];

        // Contrato
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
            if (!empty($scheduledDates)) {
                $conflicts[] = [
                    'reason' => 'Sin contrato activo',
                    'dates'  => array_map(fn($d)=>$d->toDateString(), $scheduledDates),
                ];
            }
        }

        // Vacaciones
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

    // ---- Helpers para reemplazos / cobertura ----

    /** Normaliza replacements[] del request */
    private function normalizeReplacements(array $raw): array
    {
        $out = [];
        foreach (['driver','assistant1','assistant2'] as $role) {
            $empId = Arr::get($raw, "$role.employee_id");
            $dates = Arr::get($raw, "$role.dates", '');
            if ($empId && $dates) {
                $out[$role] = [
                    'employee_id' => (int) $empId,
                    'dates'       => collect(explode(',', $dates))
                        ->map(fn($d)=>trim($d))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                ];
            }
        }
        return $out;
    }

    /** Empleado asignado para un rol en una fecha dada (aplica reemplazo si cubre) */
    private function employeeForDate(string $role, array $base, array $replacements, string $date): ?int
    {
        $rep = $replacements[$role] ?? null;
        if ($rep && in_array($date, $rep['dates'], true)) {
            return (int) $rep['employee_id'];
        }
        return $base[$role] ? (int) $base[$role] : null;
    }

    /**
     * Devuelve mensajes si existen días de conflicto (contrato/vacaciones)
     * NO cubiertos por el reemplazo elegido. Si todo está cubierto, devuelve [].
     */
    private function uncoveredConflicts(Employeegroup $group, array $base, array $dates, array $replacements): array
    {
        $messages = [];

        $employees = Employee::whereIn('id', collect($base)->filter()->values())->get()->keyBy('id');
        $scheduledDates = array_map(fn($d)=>Carbon::parse($d), $dates);

        foreach (['driver','assistant1','assistant2'] as $role) {
            $empId = $base[$role] ?? null;
            if (!$empId) continue;
            $emp = $employees[$empId] ?? null;
            if (!$emp) continue;

            $items = $this->conflictsForEmployee($emp, $scheduledDates);
            if (empty($items)) continue;

            $conflictDates = collect($items)->flatMap(fn($it) => $it['dates'])->unique()->values();
            $covered = collect($replacements[$role]['dates'] ?? []);
            $notCovered = $conflictDates->diff($covered);

            if ($notCovered->isNotEmpty()) {
                foreach ($items as $it) {
                    $left = collect($it['dates'])->intersect($notCovered)->values();
                    if ($left->isNotEmpty()) {
                        $messages[] = "{$emp->names} {$emp->lastnames} no está disponible por {$it['reason']} en: ".$left->implode(', ');
                    }
                }
            }
        }

        return $messages;
    }
}
