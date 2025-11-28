<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employeegroup;
use App\Models\Zone;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Employee;
use Yajra\DataTables\Facades\DataTables;

class EmployeegroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Employeegroup::with(['zone', 'shift', 'vehicle'])->get();

        if (request()->ajax()) {
            return DataTables()->of($groups)
                ->addColumn('zone', function ($g) {
                    return optional($g->zone)->name;
                })
                ->addColumn('shift', function ($g) {
                    return optional($g->shift)->name;
                })
                ->addColumn('vehicle', function ($g) {
                    return optional($g->vehicle)->plate ?? '';
                })
                ->addColumn('view_employees', function ($g) {
                    return '<button class="btn btn-info btn-sm btnViewEmployees" data-id="' . $g->id . '"><i class="fas fa-eye"></i></button>';
                })
                ->addColumn('edit', function ($g) {
                    return '<button class="btn btn-warning btn-sm btnEditar" id="' . $g->id . '"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn('delete', function ($g) {
                    return '<form action="' . route('admin.personnel.employeegroups.destroy', $g) . '" method="POST" class="frmDelete">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->rawColumns(['edit', 'delete', 'view_employees'])
                ->make(true);
        }

        return view('personnel.employeegroup.index', compact('groups'));

    }

    // NUEVO: método para mostrar empleados del grupo
    public function employees($id)
    {
        $group = Employeegroup::with(['employees' => function($q) {
            $q->withPivot('posicion');
        }])->findOrFail($id);

        $empleados = $group->employees->sortBy('pivot.posicion');
        $html = '<ul class="list-group">';
        foreach ($empleados as $emp) {
            $rol = $emp->pivot->posicion == 1 ? 'Conductor' : 'Ayudante ' . ($emp->pivot->posicion - 1);
            $html .= '<li class="list-group-item"><strong>' . $rol . ':</strong> ' . $emp->full_name . '</li>';
        }
        $html .= '</ul>';

        return response()->json(['html' => $html]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        // Obtener los IDs de los tipos de empleado para conductor y ayudante
        $conductorTypeId = \App\Models\EmployeeType::where('name', 'like', '%conduc%')->orWhere('code', 'like', '%conduc%')->value('id');
        $ayudanteTypeId = \App\Models\EmployeeType::where('name', 'like', '%ayud%')->orWhere('code', 'like', '%ayud%')->value('id');

        // Buscar empleados con contrato activo y position_id correspondiente
        $conductores = \App\Models\Employee::where('status', 1)
            ->whereHas('activeContract', function($q) use ($conductorTypeId) {
                $q->where('position_id', $conductorTypeId);
            })
            ->get();

        $ayudantes = \App\Models\Employee::where('status', 1)
            ->whereHas('activeContract', function($q) use ($ayudanteTypeId) {
                $q->where('position_id', $ayudanteTypeId);
            })
            ->get();

        return view('personnel.employeegroup.create', compact('zones', 'shifts', 'vehicles', 'conductores', 'ayudantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    try {
        $request->validate([
            'name' => 'required|unique:employeegroups,name',
            'zone_id' => 'required',
            'shift_id' => 'required',
            'vehicle_id' => 'required',
            'days' => 'required|array|min:1',
        ]);

        // Validación de empleados con contrato activo
        $crew = $request->input('crew', []);
        if (!empty($crew)) {
            $invalidEmployees = [];
            foreach ($crew as $employeeId) {
                if (!$employeeId) continue;
                $employee = Employee::find($employeeId);
                if (!$employee || !$employee->activeContract) {
                    $invalidEmployees[] = $employeeId;
                }
            }
            if (count($invalidEmployees) > 0) {
                return response()->json([
                    'message' => 'Uno o más empleados seleccionados no tienen contrato activo. Por favor, revise la selección.'
                ], 422);
            }
        }

        $days = implode(',', $request->days);

        // Recolectar IDs de empleados de forma dinámica
        $members = [];
        if ($request->has('crew')) {
            foreach ($request->crew as $pos => $empId) {
                if ($empId) {
                    $members[$empId] = ['posicion' => $pos];
                }
            }
        }

        // Validar conflictos antes de registrar
        if (!empty($members)) {
            $conflicts = $this->hasScheduleConflict(array_keys($members), $request->shift_id, $days);
            if ($conflicts && count($conflicts) > 0) {
                $messages = collect($conflicts)->map(function($c) {
                    return "<li>El empleado <strong>{$c['employee']}</strong> ya está asignado al grupo <strong>{$c['group']}</strong> en los días: <strong>{$c['days']}</strong> (turno actual).</li>";
                })->implode("");
                $messages = "<ul style='margin-bottom:0'>" . $messages . "</ul>";
                return response()->json([
                    'message' => $messages
                ], 422);
            }
        }

        $group = Employeegroup::create([
            'name'       => $request->name,
            'zone_id'    => $request->zone_id,
            'shift_id'   => $request->shift_id,
            'vehicle_id' => $request->vehicle_id,
            'days'       => $days,
            'status'     => $request->status ?? 1,
        ]);

        if (!empty($members)) {
            $group->employees()->sync($members);
        }

        return response()->json(['message' => 'Grupo registrado correctamente'], 200);
    } catch (\Throwable $th) {
        return response()->json(['message' => 'Error al registrar el grupo: ' . $th->getMessage()], 500);
    }
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
{
    $group = Employeegroup::with(['employees' => function($q) {
        $q->withPivot('posicion');
    }])->findOrFail($id);
    $zones = Zone::all();
    $shifts = Shift::all();
    $vehicles = Vehicle::all();

    // Obtener los IDs de los tipos de empleado para conductor y ayudante
    $conductorTypeId = \App\Models\EmployeeType::where('name', 'like', '%conduc%')->orWhere('code', 'like', '%conduc%')->value('id');
    $ayudanteTypeId = \App\Models\EmployeeType::where('name', 'like', '%ayud%')->orWhere('code', 'like', '%ayud%')->value('id');

    $conductores = \App\Models\Employee::where('status', 1)
        ->whereHas('activeContract', function($q) use ($conductorTypeId) {
            $q->where('position_id', $conductorTypeId);
        })
        ->get();

    $ayudantes = \App\Models\Employee::where('status', 1)
        ->whereHas('activeContract', function($q) use ($ayudanteTypeId) {
            $q->where('position_id', $ayudanteTypeId);
        })
        ->get();

    // Obtener la configuración actual del grupo (empleados y su posición)
    $crewConfig = $group->employees->mapWithKeys(function($emp) {
        return [$emp->pivot->posicion => $emp->id];
    })->toArray();

    return view('personnel.employeegroup.edit', compact(
        'group',
        'zones',
        'shifts',
        'vehicles',
        'conductores',
        'ayudantes',
        'crewConfig'
    ));
}


    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
{
    try {
        $group = Employeegroup::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:employeegroups,name,' . $id,
            'zone_id' => 'required',
        ]);
        $days = implode(',', $request->days);

        // Recolectar IDs de empleados de forma dinámica
        $members = [];
        if ($request->has('crew')) {
            foreach ($request->crew as $pos => $empId) {
                if ($empId) {
                    $members[$empId] = ['posicion' => $pos];
                }
            }
        }

        // validar conflicto antes de actualizar
        if (!empty($members)) {
            $conflicts = $this->hasScheduleConflict(array_keys($members), $request->shift_id, $days, $id);
            if ($conflicts && count($conflicts) > 0) {
                $messages = collect($conflicts)->map(function($c) {
                    return "<li>El empleado <strong>{$c['employee']}</strong> ya está asignado al grupo <strong>{$c['group']}</strong> en los días: <strong>{$c['days']}</strong> (turno actual).</li>";
                })->implode("");
                $messages = "<ul style='margin-bottom:0'>" . $messages . "</ul>";
                return response()->json([
                    'message' => $messages
                ], 422);
            }
        }

        $group->update([
            'name'       => $request->name,
            'zone_id'    => $request->zone_id,
            'shift_id'   => $request->shift_id,
            'vehicle_id' => $request->vehicle_id,
            'days'       => $days,
            'status'     => $request->status ?? $group->status,
        ]);

        if (!empty($members)) {
            $group->employees()->sync($members);
        } else {
            $group->employees()->detach();
        }

        return response()->json(['message' => 'Grupo actualizado correctamente'], 200);
    } catch (\Throwable $th) {
        return response()->json(['message' => 'Error al actualizar el grupo: ' . $th->getMessage()], 500);
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Employeegroup::find($id);
        $group->employees()->detach();
        $group->delete();
        return redirect()->route('admin.personnel.employeegroups.index')->with('action', 'Grupo eliminado');
    }


    /**
 * Verifica si alguno de los empleados ya tiene conflicto de turno/días
 */
private function hasScheduleConflict($employeeIds, $shiftId, $days, $excludeGroupId = null)
{
    // convertir días actuales a array limpio
    $currentDays = array_map('trim', explode(',', $days));

    // obtener todos los grupos del turno indicado
    $query = \App\Models\Employeegroup::with('employees')
        ->where('shift_id', $shiftId);

    // si estamos editando, excluir el grupo actual
    if ($excludeGroupId) {
        $query->where('id', '!=', $excludeGroupId);
    }

    $groups = $query->get();
    $conflicts = [];

    foreach ($groups as $group) {
        $groupDays = array_map('trim', explode(',', $group->days));

        // Si hay al menos un día en común
        $dayOverlap = count(array_intersect($currentDays, $groupDays)) > 0;

        if (!$dayOverlap) continue;

        foreach ($group->employees as $emp) {
            if (in_array($emp->id, $employeeIds)) {
                $conflicts[] = [
                    'employee' => $emp->names . ' ' . $emp->lastnames,
                    'group'    => $group->name,
                    'days'     => implode(', ', array_intersect($currentDays, $groupDays))
                ];
            }
        }
    }

    return $conflicts;
}

}
