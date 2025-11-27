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
use Illuminate\Support\Str;

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
     * CREATE MASIVE (form)
     * ========================= */
    public function createMasive()
    {
        // Cargar también los empleados del grupo para mostrar selects en la vista
        $groups = Employeegroup::with(['shift','vehicle','zone','employees'])->orderBy('name')->get();
        // Además traer listados completos por tipo para poblar selects (no solo miembros del grupo)
        // Usar la misma lógica que EmployeegroupController: filtrar por contrato activo y position_id
        $conductorTypeId = \App\Models\EmployeeType::where('name', 'like', '%conduc%')->orWhere('code', 'like', '%conduc%')->value('id');
        $ayudanteTypeId = \App\Models\EmployeeType::where('name', 'like', '%ayud%')->orWhere('code', 'like', '%ayud%')->value('id');

        $drivers = Employee::where('status', 1)
            ->when($conductorTypeId, function($q) use ($conductorTypeId) {
                $q->whereHas('activeContract', function($q2) use ($conductorTypeId) {
                    $q2->where('position_id', $conductorTypeId);
                });
            })
            ->orderBy('lastnames')
            ->get();

        $assistants = Employee::where('status', 1)
            ->when($ayudanteTypeId, function($q) use ($ayudanteTypeId) {
                $q->whereHas('activeContract', function($q2) use ($ayudanteTypeId) {
                    $q2->where('position_id', $ayudanteTypeId);
                });
            })
            ->orderBy('lastnames')
            ->get();

        // Attach selected member ids per group from pivot table to make selection deterministic
        $configRows = DB::table('configgroups')
            ->whereIn('employeegroup_id', $groups->pluck('id')->all())
            ->get()
            ->groupBy('employeegroup_id');

        foreach ($groups as $g) {
            $rows = $configRows->get($g->id) ?? collect();
            $byPos = $rows->keyBy('posicion')->map(fn($r) => $r->employee_id)->toArray();
            $g->selected_driver_id = $byPos[1] ?? null;
            $g->selected_assistant1_id = $byPos[2] ?? null;
            $g->selected_assistant2_id = $byPos[3] ?? null;
        }

        return view('schedulings.create-masive', compact('groups', 'drivers', 'assistants'));
    }

    /* =========================
     * STORE (genera por rango + reemplazos por fecha)
     * ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'from'      => 'required|date',
            'to'        => 'required|date|after_or_equal:from',
            'notes'     => 'nullable|string|max:120',
        ]);

        $from = Carbon::parse($request->from)->startOfDay();
        $to   = Carbon::parse($request->to)->endOfDay();

        // Collect group_* inputs (each is an array: driver, assistant1, assistant2, removed)
        $all = $request->all();
        $groupInputs = collect($all)->filter(fn($v,$k) => Str::startsWith($k, 'group_'));

        if ($groupInputs->isEmpty()) {
            return back()->withErrors(['groups' => 'No se encontraron grupos para procesar.'])->withInput();
        }

        $planned = []; // list of planned assignments
        $errors = [];

        foreach ($groupInputs as $key => $data) {
            // key: group_{id}
            $id = (int) Str::after($key, 'group_');
            if (!$id) continue;
            $removed = isset($data['removed']) && (string)$data['removed'] === '1';
            if ($removed) continue; // skip trashed groups

            $group = Employeegroup::with(['shift','vehicle','zone','employees'])->find($id);
            if (!$group) {
                $errors[] = "Grupo {$id} no existe.";
                continue;
            }

            // Determine assigned personnel: prefer submitted values, then selected_* from controller, then pivot/type fallbacks
            $driverId = $data['driver'] ?? $group->selected_driver_id ?? optional($group->employees->firstWhere('pivot.posicion',1))->id ?? optional($group->employees->firstWhere('type_id',1))->id;
            $assistant1Id = $data['assistant1'] ?? $group->selected_assistant1_id ?? optional($group->employees->firstWhere('pivot.posicion',2))->id;
            $assistant2Id = $data['assistant2'] ?? $group->selected_assistant2_id ?? optional($group->employees->firstWhere('pivot.posicion',3))->id;

            // Build dates for this group based on its days
            $allowedDays = $this->parseSpanishDays($group->days ?? '');
            $dates = [];
            foreach (CarbonPeriod::create($from, $to) as $d) {
                if (in_array($d->dayOfWeek, $allowedDays)) $dates[] = $d->toDateString();
            }

            if (empty($dates)) {
                $errors[] = "El rango no contiene días válidos para el grupo {$group->name} (ID {$group->id}).";
                continue;
            }

            // For each date, add planned assignment
            foreach ($dates as $date) {
                $planned[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'date' => $date,
                    'shift_id' => $group->shift_id,
                    'vehicle_id' => $group->vehicle_id,
                    'zone_id' => $group->zone_id,
                    'notes' => $request->notes ?? '',
                    'driver' => $driverId ? (int)$driverId : null,
                    'assistant1' => $assistant1Id ? (int)$assistant1Id : null,
                    'assistant2' => $assistant2Id ? (int)$assistant2Id : null,
                ];
            }
        }

        if (!empty($errors)) {
            return back()->withErrors(['validation' => implode(' | ', $errors)])->withInput();
        }

        // Validate conflicts: no empleado puede tener otra programación el mismo día y mismo turno
        $conflicts = [];
        foreach ($planned as $p) {
            foreach (['driver','assistant1','assistant2'] as $role) {
                $empId = $p[$role];
                if (!$empId) continue;
                $exists = Scheduling::join('groupdetails as gd', 'gd.scheduling_id', '=', 'schedulings.id')
                    ->where('gd.emplooyee_id', $empId)
                    ->whereDate('schedulings.date', $p['date'])
                    ->where('schedulings.shift_id', $p['shift_id'])
                    ->exists();
                if ($exists) {
                    $emp = Employee::find($empId);
                    $conflicts[] = "{$emp->lastnames} {$emp->names} ya tiene programación el {$p['date']} en el mismo turno (grupo: {$p['group_name']}).";
                }
            }
        }

        if (!empty($conflicts)) {
            return back()->withErrors(['conflicts' => implode(' | ', array_unique($conflicts))])->withInput();
        }

        // No conflicts -> create schedulings in transaction
        DB::transaction(function () use ($planned) {
            foreach ($planned as $p) {
                // avoid duplicate scheduling same group+date
                $exists = Scheduling::where('group_id', $p['group_id'])->whereDate('date', $p['date'])->exists();
                if ($exists) continue;

                $s = Scheduling::create([
                    'group_id' => $p['group_id'],
                    'shift_id' => $p['shift_id'],
                    'vehicle_id' => $p['vehicle_id'],
                    'zone_id' => $p['zone_id'],
                    'date' => $p['date'],
                    'notes' => $p['notes'],
                    'status' => 1,
                ]);

                collect([$p['driver'],$p['assistant1'],$p['assistant2']])->filter()->unique()->values()->each(function ($empId) use ($s) {
                    Groupdetail::create([
                        'scheduling_id' => $s->id,
                        'emplooyee_id' => $empId,
                    ]);
                });
            }
        });

        return redirect()->route('admin.schedulings.index', ['from' => request('from'), 'to' => request('to')])
            ->with('success', 'Programaciones masivas registradas correctamente.');
    }

    /* =========================
     * EDITAR (si lo usas para cambios manuales)
     * ========================= */
    public function edit(Request $request, Scheduling $scheduling)
    {
        $scheduling = Scheduling::with(['group.shift','group.vehicle','group.zone','details.employee'])->find($scheduling->id);

        $drivers    = Employee::where('status', 1)->where('type_id', 1)->orderBy('lastnames')->get();
        $assistants = Employee::where('status', 1)->where('type_id', 2)->orderBy('lastnames')->get();
        $shifts     = \App\Models\Shift::all();
        $vehicles   = \App\Models\Vehicle::all();
        $employees  = Employee::where('status', 1)->orderBy('lastnames')->get();

        $driverDetail = $scheduling->details->firstWhere('employee.type_id', 1);
        $aDetails     = $scheduling->details->filter(fn($d) => optional($d->employee)->type_id == 2)->values();

        $selectedDriverId = $driverDetail?->employee?->id;
        $selectedA1Id     = $aDetails->get(0)?->employee?->id;
        $selectedA2Id     = $aDetails->get(1)?->employee?->id;

        // Agregar turno y vehículo actuales
        $scheduling->turno_actual = $scheduling->shift->name ?? 'N/A';
        $scheduling->vehiculo_actual = $scheduling->vehicle->plate ?? 'N/A';

        if ($request->ajax()) {
            try {
                return view('admin.edit_modal', compact(
                    'scheduling', 'drivers', 'assistants', 'selectedDriverId', 'selectedA1Id', 'selectedA2Id', 'shifts', 'vehicles', 'employees'
                ))->render();
            } catch (\Exception $e) {
                return 'Error rendering view: ' . $e->getMessage();
            }
        }

        return view('schedulings.edit', compact(
            'scheduling', 'drivers', 'assistants', 'selectedDriverId', 'selectedA1Id', 'selectedA2Id', 'shifts', 'vehicles'
        ));
    }

    public function update(Request $request, Scheduling $scheduling)
    {
        $request->validate([
            'date'          => 'required|date',
            'driver_id'     => 'nullable|exists:employee,id',
            'assistant1_id' => 'nullable|exists:employee,id|different:driver_id|different:assistant2_id',
            'assistant2_id' => 'nullable|exists:employee,id|different:driver_id|different:assistant1_id',
            'shift_id'      => 'nullable|exists:shifts,id',
            'vehicle_id'    => 'nullable|exists:vehicles,id',
            'notes'         => 'nullable|string|max:120',
        ], [
            'different' => 'No puede repetir el mismo trabajador en más de un rol.'
        ]);

        // Actualizar campos básicos
        $scheduling->update([
            'date'       => $request->date,
            'notes'      => $request->notes ?? '',
            'shift_id'   => $request->shift_id ?? $scheduling->shift_id,
            'vehicle_id' => $request->vehicle_id ?? $scheduling->vehicle_id,
        ]);

        // Actualizar personal
        $scheduling->details()->delete();

        foreach (['driver_id', 'assistant1_id', 'assistant2_id'] as $field) {
            if ($request->$field) {
                Groupdetail::create([
                    'scheduling_id' => $scheduling->id,
                    'emplooyee_id'  => $request->$field,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Programación actualizada correctamente.']);
        }

        return redirect()->route('admin.schedulings.index')
            ->with('success', 'Programación actualizada correctamente.');
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
                'id' => $emp->id,
                'full_name' => trim(($emp->lastnames ?? '').' '.($emp->names ?? '')),
                'type_id' => $emp->type_id,
                'type_name' => $emp->type->name ?? ($emp->type_id == 1 ? 'Conductor' : 'Ayudante'),
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

        // 🔥 DEVOLVER TODOS LOS MIEMBROS EN UN ARRAY
        $allMembers = $members->map(function($member, $index) use ($buildPerson) {
            $personData = $buildPerson($member);
            $personData['position'] = $index + 1;
            $personData['role'] = $member->type_id == 1 ? 'Conductor' : 'Ayudante ' . $index;
            return $personData;
        });

        return response()->json([
            'members' => $allMembers
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
            'additional_days' => 'nullable|array',
            'additional_days.*' => 'in:lunes,martes,miércoles,miercoles,jueves,viernes,sábado,sabado,domingo',
        ]);

        $group = Employeegroup::with('employees')->findOrFail($request->group_id);
        $from  = Carbon::parse($request->from)->startOfDay();
        $to    = Carbon::parse($request->to)->endOfDay();
        
        // 🔥 COMBINAR DÍAS DEL GRUPO + DÍAS ADICIONALES
        $baseGroupDays = $this->parseSpanishDays($group->days ?? '');
        $additionalDays = $this->parseSpanishDays(implode(',', $request->input('additional_days', [])));
        $allWorkingDays = array_values(array_unique(array_merge($baseGroupDays, $additionalDays)));
        
        // Obtener fechas del rango que coinciden con los días combinados
        $scheduledDates = [];
        foreach (CarbonPeriod::create($from, $to) as $d) {
            if (in_array($d->dayOfWeek, $allWorkingDays)) {
                $scheduledDates[] = $d->copy();
            }
        }

        $members = $group->employees()->orderBy('configgroups.id')->get();

        $result = [
            'ok'        => true,
            'conflicts' => [],
            'byRole'    => [],
        ];

        // 🔥 VERIFICAR CONFLICTOS PARA CADA MIEMBRO
        foreach ($members as $index => $member) {
            $roleKey = "member_{$index}";
            $conflicts = $this->conflictsForEmployee($member, $scheduledDates);
            
            if (!empty($conflicts)) {
                $result['ok'] = false;
                $result['conflicts'][] = [
                    'employee_id' => $member->id,
                    'name' => trim(($member->names ?? '').' '.($member->lastnames ?? '')),
                    'role' => $roleKey,
                    'items' => $conflicts,
                ];
                $result['byRole'][$roleKey] = $conflicts;
            }
        }

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
            'group_id' => 'required|exists:employeegroups,id',
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
    private function parseSpanishDays($daysInput): array
    {
        if (is_array($daysInput)) {
            $daysArray = $daysInput;
        } else {
            $daysArray = explode(',', $daysInput);
        }

        $map = [
            'domingo'=>0, 'lunes'=>1, 'martes'=>2,
            'miércoles'=>3, 'miercoles'=>3,
            'jueves'=>4, 'viernes'=>5,
            'sábado'=>6, 'sabado'=>6,
        ];

        $out = [];
        foreach ($daysArray as $d) {
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
        foreach ($raw as $role => $data) {
            $empId = Arr::get($data, 'employee_id');
            $dates = Arr::get($data, 'dates', '');
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