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

        // Prepare lightweight data for the mass-change modal so the view can be included safely
        $zones = \App\Models\Zone::orderBy('name')->get();
        $drivers = \App\Models\Employee::where('status',1)->orderBy('lastnames')->get()->map(function($e){
            return (object)[
                'id' => $e->id,
                'name' => trim(($e->lastnames ?? '').' '.($e->names ?? '')),
                'document' => $e->document ?? '',
                'contract_status' => $e->activeContract()->first()?->is_active ? 'Activo' : 'Sin contrato',
            ];
        });

        $massiveChange = (object) [
            'from' => old('from') ?? now()->toDateString(),
            'to' => old('to') ?? now()->toDateString(),
            'zones' => old('zones', []),
            'type' => old('type', 'Cambio de Conductor'),
            'old_driver' => old('old_driver', null),
            'new_driver' => old('new_driver', null),
            'reason' => old('reason', ''),
        ];

        return view('schedulings.index', compact('schedulings','from','to','zones','drivers','massiveChange'));
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
    public function editMassive(\Illuminate\Http\Request $request)
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
            ->get()
            ->map(function($e){
                return (object)[
                    'id' => $e->id,
                    'name' => trim(($e->lastnames ?? '').' '.($e->names ?? '')),
                    'document' => $e->document ?? '',
                    'contract_status' => $e->activeContract()->first()?->is_active ? 'Activo' : 'Sin contrato',
                ];
            });

        $assistants = Employee::where('status', 1)
            ->when($ayudanteTypeId, function($q) use ($ayudanteTypeId) {
                $q->whereHas('activeContract', function($q2) use ($ayudanteTypeId) {
                    $q2->where('position_id', $ayudanteTypeId);
                });
            })
            ->orderBy('lastnames')
            ->get();

        // Zones + a lightweight massiveChange object (so edit view works when loaded via AJAX)
        $zones = \App\Models\Zone::orderBy('name')->get();
        $shifts = \App\Models\Shift::orderBy('name')->get();
        $vehicles = \App\Models\Vehicle::orderBy('plate')->get();

        $massiveChange = (object) [
            'from' => now()->toDateString(),
            'to' => now()->toDateString(),
            'zones' => [],
            'type' => 'Cambio de Conductor',
            'old_driver' => null,
            'new_driver' => null,
            'reason' => '',
        ];

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

        // Motivos activos
        $reasons = \App\Models\Reason::where('active', 1)->orderBy('name')->get();
        // If it's an AJAX request, return only the form fragment so it can be injected
        if ($request->ajax()) {
            return view('schedulings._edit_massive_form', compact('groups','drivers','assistants','zones','shifts','vehicles','massiveChange','reasons'));
        }

        return view('schedulings.edit-massive', compact('groups', 'drivers', 'assistants','zones','shifts','vehicles','massiveChange','reasons'));
    }
    /**
     * Return only the massive form fragment (for AJAX modal)
     */
    public function massiveForm()
    {
        $groups = Employeegroup::with(['shift','vehicle','zone','employees'])->orderBy('name')->get();

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

        return view('schedulings._massive_form', compact('groups','drivers','assistants'));
    }

    /**
     * Aplica cambios masivos (update) a programaciones en un rango de fechas.
     * Se soportan: Cambio de Conductor, Cambio de Ayudante, Cambio de Turno, Cambio de Vehiculo.
     */
    public function massiveUpdate(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
            'zones' => 'nullable|array',
            'zones.*' => 'exists:zones,id',
            'type' => 'required|string|in:Cambio de Conductor,Cambio de Ayudante,Cambio de Turno,Cambio de Vehiculo',
            'reason_id' => 'required|exists:reasons,id',
            'notes' => 'nullable|string|max:500',

            'old_driver' => 'nullable|exists:employee,id',
            'new_driver' => 'nullable|exists:employee,id',

            'old_assistant' => 'nullable|exists:employee,id',
            'new_assistant' => 'nullable|exists:employee,id',

            'old_shift' => 'nullable|exists:shifts,id',
            'new_shift' => 'nullable|exists:shifts,id',

            'old_vehicle' => 'nullable|exists:vehicles,id',
            'new_vehicle' => 'nullable|exists:vehicles,id',
        ]);

        $from = $request->from;
        $to = $request->to;

        $query = Scheduling::with(['details.employee','shift','vehicle'])
            ->whereDate('date','>=',$from)
            ->whereDate('date','<=',$to);

        if ($request->filled('zones')) {
            $query->whereIn('zone_id', $request->zones);
        }

        $schedulings = $query->get();

        $affected = 0;

        DB::transaction(function() use (&$affected, $schedulings, $request) {
            $userId = auth()->id();
            $reasonId = $request->input('reason_id');
            $notes = $request->input('notes');
            foreach ($schedulings as $s) {
                $changed = false;

                switch ($request->type) {
                    case 'Cambio de Conductor':
                        $driverDetail = $s->details->first(function($d){
                            return optional($d->employee)->type_id == 1;
                        });
                        if ($driverDetail) {
                            $apply = true;
                            if ($request->filled('old_driver')) {
                                $apply = ($driverDetail->emplooyee_id == (int)$request->old_driver);
                            }
                            if ($apply && $request->filled('new_driver')) {
                                $oldName = optional($driverDetail->employee)->lastnames . ' ' . optional($driverDetail->employee)->names;
                                $driverDetail->update(['emplooyee_id' => $request->new_driver]);
                                $newEmp = \App\Models\Employee::find($request->new_driver);
                                $newName = $newEmp ? ($newEmp->lastnames . ' ' . $newEmp->names) : null;
                                \App\Models\SchedulingChange::create([
                                    'scheduling_id' => $s->id,
                                    'reason_id' => $reasonId,
                                    'notes' => $notes,
                                    'change_type' => 'personal',
                                    'old_value' => $oldName,
                                    'new_value' => $newName,
                                    'user_id' => $userId,
                                ]);
                                $changed = true;
                            }
                        }
                        break;

                    case 'Cambio de Ayudante':
                        $assistantDetails = $s->details->filter(function($d){
                            return optional($d->employee)->type_id == 2;
                        });
                        foreach ($assistantDetails as $ad) {
                            $apply = true;
                            if ($request->filled('old_assistant')) {
                                $apply = ($ad->emplooyee_id == (int)$request->old_assistant);
                            }
                            if ($apply && $request->filled('new_assistant')) {
                                $oldName = optional($ad->employee)->lastnames . ' ' . optional($ad->employee)->names;
                                $ad->update(['emplooyee_id' => $request->new_assistant]);
                                $newEmp = \App\Models\Employee::find($request->new_assistant);
                                $newName = $newEmp ? ($newEmp->lastnames . ' ' . $newEmp->names) : null;
                                \App\Models\SchedulingChange::create([
                                    'scheduling_id' => $s->id,
                                    'reason_id' => $reasonId,
                                    'notes' => $notes,
                                    'change_type' => 'personal',
                                    'old_value' => $oldName,
                                    'new_value' => $newName,
                                    'user_id' => $userId,
                                ]);
                                $changed = true;
                            }
                        }
                        break;

                    case 'Cambio de Turno':
                        $oldShift = $s->shift_id;
                        if ($request->filled('old_shift')) {
                            if ($oldShift != (int)$request->old_shift) break;
                        }
                        if ($request->filled('new_shift')) {
                            $oldShift = $oldShift ?? $s->shift_id;
                            $s->update(['shift_id' => $request->new_shift]);
                            \App\Models\SchedulingChange::create([
                                'scheduling_id' => $s->id,
                                'reason_id' => $reasonId,
                                'notes' => $notes,
                                'change_type' => 'turno',
                                'old_value' => optional(\App\Models\Shift::find($oldShift))->name ?? null,
                                'new_value' => optional(\App\Models\Shift::find($request->new_shift))->name ?? null,
                                'user_id' => $userId,
                            ]);
                            $changed = true;
                        }
                        break;

                    case 'Cambio de Vehiculo':
                        $oldVehicle = $s->vehicle_id;
                        if ($request->filled('old_vehicle')) {
                            if ($oldVehicle != (int)$request->old_vehicle) break;
                        }
                        if ($request->filled('new_vehicle')) {
                            $oldVehicle = $oldVehicle ?? $s->vehicle_id;
                            $s->update(['vehicle_id' => $request->new_vehicle]);
                            \App\Models\SchedulingChange::create([
                                'scheduling_id' => $s->id,
                                'reason_id' => $reasonId,
                                'notes' => $notes,
                                'change_type' => 'vehículo',
                                'old_value' => optional(\App\Models\Vehicle::find($oldVehicle))->plate ?? null,
                                'new_value' => optional(\App\Models\Vehicle::find($request->new_vehicle))->plate ?? null,
                                'user_id' => $userId,
                            ]);
                            $changed = true;
                        }
                        break;
                }

                if ($changed) {
                    // marca como reprogramado si estaba programado
                    if ($s->status == 1) $s->update(['status' => 2]);
                    $affected++;
                }
            }
        });

        $message = "Cambios masivos aplicados correctamente. Programaciones afectadas: {$affected}.";
        return redirect()->back()->with('success', $message);
    }

    /* =========================
     * STORE (genera por rango + reemplazos por fecha)
     * ========================= */
  public function store(Request $request)
    {
        $request->validate([
            'group_ids' => 'required_without:group_id|array',
            'group_ids.*' => 'exists:employeegroups,id',
            'group_id'  => 'required_without:group_ids|exists:employeegroups,id',
            'from'      => 'required|date',
            'to'        => 'required|date|after_or_equal:from',
            'notes'     => 'nullable|string|max:120',
            'additional_days' => 'nullable|array',
            'additional_days.*' => 'in:lunes,martes,miércoles,miercoles,jueves,viernes,sábado,sabado,domingo',
        ]);

        // Determine list of group ids to process
        $groupIds = $request->input('group_ids') ?? ($request->filled('group_id') ? [$request->input('group_id')] : []);
        if (empty($groupIds)) {
            return back()->withErrors(['group_id' => 'Seleccione al menos un grupo'])->withInput();
        }

        $totalAffected = 0;
        $totalAdditionalDays = 0;

        // Replacements common for the request
        $replacements = $this->normalizeReplacements($request->input('replacements', []));

        DB::transaction(function () use ($groupIds, $request, &$totalAffected, &$totalAdditionalDays, $replacements) {
            foreach ($groupIds as $gid) {
                // Grupo + miembros base
                $group = Employeegroup::with(['shift','vehicle','zone','employees'])->findOrFail($gid);

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

                // Combinar días del grupo + adicionales
                $baseGroupDays = $this->parseSpanishDays($group->days ?? '');
                $additionalDays = $this->parseSpanishDays($request->input('additional_days_processed', ''));
                $allWorkingDays = array_values(array_unique(array_merge($baseGroupDays, $additionalDays)));

                // Fechas del rango que coinciden con los días combinados
                $period = CarbonPeriod::create(Carbon::parse($request->from), Carbon::parse($request->to));
                $dates  = [];
                foreach ($period as $d) {
                    if (in_array($d->dayOfWeek, $allWorkingDays)) {
                        $dates[] = $d->toDateString();
                    }
                }

                if (empty($dates)) {
                    // No crear nada para este grupo si no hay fechas
                    continue;
                }

                // Validar conflictos uncovered solo para este grupo
                $uncovered = $this->uncoveredConflicts($group, $base, $dates, $replacements);
                if (!empty($uncovered)) {
                    // Si hay conflictos abortamos toda la transacción y devolvemos mensaje
                    throw new \Illuminate\Validation\ValidationException(\Illuminate\Support\Facades\Validator::make([], []), response()->json(['errors' => ['availability' => implode(' | ', $uncovered)]], 422));
                }

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

                    $totalAffected++;
                }

                if (!empty($additionalDays)) {
                    $totalAdditionalDays = max($totalAdditionalDays, count($additionalDays));
                }
            }
        });

        $message = "Programaciones generadas con reemplazos aplicados cuando correspondía. Programaciones afectadas: {$totalAffected}.";
        if ($totalAdditionalDays > 0) {
            $message .= " Se incluyeron {$totalAdditionalDays} día(s) adicional(es).";
        }

        return redirect()
            ->route('admin.schedulings.index', ['from'=>$request->from,'to'=>$request->to])
            ->with('success', $message);
    }


    /* =========================
     * EDITAR (si lo usas para cambios manuales)
     * ========================= */
       public function edit(Scheduling $scheduling)
    {
        $scheduling->load(['group.shift','group.vehicle','group.zone','details.employee.type']);

        $drivers = Employee::where('status', 1)
            ->whereHas('type', function($q) {
                $q->where('name', 'Conductor');
            })
            ->orderBy('lastnames')
            ->get();

        $assistants = Employee::where('status', 1)
            ->whereHas('type', function($q) {
                $q->where('name', 'Ayudante');
            })
            ->orderBy('lastnames')
            ->get();

        $shifts   = \App\Models\Shift::all();
        $vehicles = \App\Models\Vehicle::all();

        $driverDetail = $scheduling->details->first(function($d) {
            return optional($d->employee->type)->name === 'Conductor';
        });
        $aDetails = $scheduling->details->filter(function($d) {
            return optional($d->employee->type)->name === 'Ayudante';
        })->values();

        $selectedDriverId = $driverDetail?->employee?->id;
        $selectedA1Id     = $aDetails->get(0)?->employee?->id;
        $selectedA2Id     = $aDetails->get(1)?->employee?->id;

        // Motivos activos
        $reasons = \App\Models\Reason::where('active', 1)->orderBy('name')->get();

        return view('schedulings.edit', compact(
            'scheduling', 'drivers', 'assistants', 'selectedDriverId', 'selectedA1Id', 'selectedA2Id', 'shifts', 'vehicles', 'reasons'
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

        // Detectar cambios y registrar en scheduling_changes
        $userId = auth()->id();
        $oldShift = $scheduling->shift_id;
        $oldVehicle = $scheduling->vehicle_id;
        $oldPersonnel = $scheduling->details->pluck('emplooyee_id')->sort()->values()->toArray();

        $newStatus = $scheduling->status;
        if ($scheduling->status == 1) {
            $newStatus = 2; // Si estaba programado, pasa a reprogramado
        }
        $scheduling->update([
            'date'       => $request->date,
            'notes'      => $request->notes ?? '',
            'shift_id'   => $request->shift_id ?? $scheduling->shift_id,
            'vehicle_id' => $request->vehicle_id ?? $scheduling->vehicle_id,
            'status'     => $request->status ?? $newStatus,
        ]);

        // Actualizar personal
        $scheduling->details()->delete();
        $newPersonnel = [];
        foreach (['driver_id', 'assistant1_id', 'assistant2_id'] as $field) {
            if ($request->$field) {
                Groupdetail::create([
                    'scheduling_id' => $scheduling->id,
                    'emplooyee_id'  => $request->$field,
                ]);
                $newPersonnel[] = $request->$field;
            }
        }
        sort($newPersonnel);

        $motivos = $request->input('motivos', []);
        $notas   = $request->input('notas', []);

        // Cambios de turno
        if ($request->shift_id && $request->shift_id != $oldShift) {
            \App\Models\SchedulingChange::create([
                'scheduling_id' => $scheduling->id,
                'reason_id'     => $motivos['turno'] ?? null,
                'notes'         => $notas['turno'] ?? null,
                'change_type'   => 'turno',
                'old_value'     => optional(\App\Models\Shift::find($oldShift))->name,
                'new_value'     => optional(\App\Models\Shift::find($request->shift_id))->name,
                'user_id'       => $userId,
            ]);
        }
        // Cambios de vehículo
        if ($request->vehicle_id && $request->vehicle_id != $oldVehicle) {
            \App\Models\SchedulingChange::create([
                'scheduling_id' => $scheduling->id,
                'reason_id'     => $motivos['vehiculo'] ?? null,
                'notes'         => $notas['vehiculo'] ?? null,
                'change_type'   => 'vehículo',
                'old_value'     => optional(\App\Models\Vehicle::find($oldVehicle))->plate,
                'new_value'     => optional(\App\Models\Vehicle::find($request->vehicle_id))->plate,
                'user_id'       => $userId,
            ]);
        }
        // Cambios de personal: registrar un cambio por cada empleado modificado
        $roles = ['driver_id' => 'Conductor', 'assistant1_id' => 'Ayudante 1', 'assistant2_id' => 'Ayudante 2'];
        $oldIds = array_combine(array_keys($roles), array_values($oldPersonnel));
        foreach ($roles as $field => $label) {
            $oldId = $oldIds[$field] ?? null;
            $newId = $request->$field ?? null;
            if ($oldId != $newId) {
                $oldEmp = $oldId ? \App\Models\Employee::find($oldId) : null;
                $newEmp = $newId ? \App\Models\Employee::find($newId) : null;
                $motivoKey = 'personal-' . $field;
                \App\Models\SchedulingChange::create([
                    'scheduling_id' => $scheduling->id,
                    'reason_id'     => $motivos[$motivoKey] ?? null,
                    'notes'         => $notas[$motivoKey] ?? null,
                    'change_type'   => 'personal',
                    'old_value'     => $oldEmp ? ($label.': '.$oldEmp->lastnames.' '.$oldEmp->names) : $label.': -',
                    'new_value'     => $newEmp ? ($label.': '.$newEmp->lastnames.' '.$newEmp->names) : $label.': -',
                    'user_id'       => $userId,
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
                'type_name' => $emp->type->name ?? ($emp->type_id == 2 ? 'Conductor' : 'Ayudante'),
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
            $personData['role'] = $member->type_id == 2 ? 'Conductor' : 'Ayudante ' . $index;
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

    /**
     * Muestra el detalle del día programado e historial de cambios (AJAX)
     */
    public function detalle(Scheduling $scheduling)
    {
        $scheduling->load(['group.zone', 'shift', 'vehicle', 'details.employee']);
        $changes = $scheduling->changes()->with('reason', 'user')->orderBy('created_at', 'desc')->get();
        return view('schedulings.partials.detalle', compact('scheduling', 'changes'));
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