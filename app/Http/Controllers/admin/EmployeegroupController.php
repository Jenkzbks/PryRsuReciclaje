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
                ->addColumn('edit', function ($g) {
                    return '<button class="btn btn-warning btn-sm btnEditar" id="' . $g->id . '"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn('delete', function ($g) {
                    return '<form action="' . route('admin.personnel.employeegroups.destroy', $g) . '" method="POST" class="frmDelete">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->rawColumns(['edit', 'delete'])
                ->make(true);
        }

        return view('personnel.employeegroup.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::all();
        $shifts = Shift::all();
        $vehicles = Vehicle::all();
        $employees = Employee::where('status', 1)->get();

        return view('personnel.employeegroup.create', compact('zones', 'shifts', 'vehicles', 'employees'));
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
        ]);

        $days = is_array($request->days) ? implode(',', $request->days) : ($request->days ?? '');

        $group = Employeegroup::create([
            'name'       => $request->name,
            'zone_id'    => $request->zone_id,
            'shift_id'   => $request->shift_id,
            'vehicle_id' => $request->vehicle_id,
            'days'       => $days,
            'status'     => $request->status ?? 1,
        ]);

        // Guardar conductor y ayudantes
        $members = [];
        if ($request->conductor)  $members[] = $request->conductor;
        if ($request->assistant1) $members[] = $request->assistant1;
        if ($request->assistant2) $members[] = $request->assistant2;

        if (!empty($members)) {
            // Adjunta los tres empleados
            $group->employees()->syncWithoutDetaching(array_unique($members));
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
    $group = Employeegroup::with('employees')->findOrFail($id);
    $zones = Zone::all();
    $shifts = Shift::all();
    $vehicles = Vehicle::all();
    $employees = Employee::where('status', 1)->get();

    // obtenemos los empleados que pertenecen al grupo
    $groupEmployees = $group->employees;

    // determinamos conductor y ayudantes
    $conductor = $groupEmployees->firstWhere('type_id', 1)?->id;

    // filtramos solo ayudantes (type_id = 2)
    $ayudantes = $groupEmployees->where('type_id', 2)->pluck('id')->values();

    // según el orden de registro (posición 0, 1)
    $assistant1 = $ayudantes->get(0);
    $assistant2 = $ayudantes->get(1);

    return view('personnel.employeegroup.edit', compact(
        'group',
        'zones',
        'shifts',
        'vehicles',
        'employees',
        'conductor',
        'assistant1',
        'assistant2'
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
        ]);

        $days = is_array($request->days) ? implode(',', $request->days) : ($request->days ?? '');

        $group->update([
            'name'       => $request->name,
            'zone_id'    => $request->zone_id,
            'shift_id'   => $request->shift_id,
            'vehicle_id' => $request->vehicle_id,
            'days'       => $days,
            'status'     => $request->status ?? $group->status,
        ]);

        // Reasignar miembros
        $members = [];
        if ($request->conductor)  $members[] = $request->conductor;
        if ($request->assistant1) $members[] = $request->assistant1;
        if ($request->assistant2) $members[] = $request->assistant2;

        $group->employees()->sync(array_unique($members));

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
        return redirect()->route('personnel.employeegroups.index')->with('action', 'Grupo eliminado');
    }
}
