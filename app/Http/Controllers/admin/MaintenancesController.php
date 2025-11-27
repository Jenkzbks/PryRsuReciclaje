<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Maintenances;
use Yajra\DataTables\Facades\DataTables;

class MaintenancesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenances = Maintenances::all();

        if(request()->ajax()){
            return DataTables::of($maintenances)
                ->addColumn('calendar', function($maintenance){
                        $url = route('admin.maintenance_shedules.index', ['maintenance' => $maintenance->id]);
                    return '<a href="'.$url.'" class="btn btn-light btn-sm" title="Ver horarios"><i class="fas fa-calendar-alt text-danger fa-lg"></i></a>';
                })
                ->addColumn('edit', function($maintenance){
                    return '<button class="btn btn-warning btn-sm btnEditar" id="'.$maintenance->id.'"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn('delete', function($maintenance){
                    return '<form action="'.route('admin.maintenances.destroy', $maintenance->id).'" method="POST" class="frmDelete">'
                        .csrf_field().method_field('DELETE').
                        '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->rawColumns(['calendar', 'edit', 'delete'])
                ->make(true);
        }

        return view('admin.examen03.maintenances.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.examen03.maintenances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:maintenances,name',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'end_date.after_or_equal' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
                'start_date.required' => 'La fecha de inicio es obligatoria.',
                'end_date.required' => 'La fecha de fin es obligatoria.',
                'name.required' => 'El nombre es obligatorio.',
                'name.unique' => 'Ya existe un mantenimiento con ese nombre.'
            ]);

            // Validar que la fecha de inicio no sea igual a la fecha final
            if ($request->start_date === $request->end_date) {
                return response()->json(['message' => 'La fecha de inicio no puede ser igual a la fecha de fin.'], 422);
            }

            // Validar que las fechas no se solapen
            $overlap = Maintenances::where(function($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })->exists();
            if ($overlap) {
                return response()->json(['message' => 'Las fechas se solapan con otro mantenimiento.'], 422);
            }

            Maintenances::create([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            return response()->json(['message' => 'Mantenimiento registrado correctamente'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = $e->validator->errors()->first();
            return response()->json(['message' => $error], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al registrar el mantenimiento.'], 500);
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
        $maintenance = Maintenances::findOrFail($id);
        return view('admin.examen03.maintenances.edit', compact('maintenance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $maintenance = Maintenances::findOrFail($id);
            $request->validate([
                'name' => 'required|string|max:255|unique:maintenances,name,' . $id,
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'end_date.after_or_equal' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
                'start_date.required' => 'La fecha de inicio es obligatoria.',
                'end_date.required' => 'La fecha de fin es obligatoria.',
                'name.required' => 'El nombre es obligatorio.',
                'name.unique' => 'Ya existe un mantenimiento con ese nombre.'
            ]);

            // Validar que la fecha de inicio no sea igual a la fecha final
            if ($request->start_date === $request->end_date) {
                return response()->json(['message' => 'La fecha de inicio no puede ser igual a la fecha de fin.'], 422);
            }

            // Validar que las fechas no se solapen (excluyendo el actual)
            $overlap = Maintenances::where('id', '!=', $id)
                ->where(function($q) use ($request) {
                    $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q2) use ($request) {
                          $q2->where('start_date', '<=', $request->start_date)
                             ->where('end_date', '>=', $request->end_date);
                      });
                })->exists();
            if ($overlap) {
                return response()->json(['message' => 'Las fechas se solapan con otro mantenimiento.'], 422);
            }

            $maintenance->update([
                'name' => $request->name,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            return response()->json(['message' => 'Mantenimiento actualizado correctamente'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $error = $e->validator->errors()->first();
            return response()->json(['message' => $error], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el mantenimiento.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $maintenance = Maintenances::findOrFail($id);
            $maintenance->delete();
            return response()->json(['message' => 'Mantenimiento eliminado correctamente'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    'message' => 'No se puede eliminar el mantenimiento porque tiene registros asociados. Elimine primero los registros dependientes.'
                ], 400);
            }
            return response()->json(['message' => 'Error al eliminar el mantenimiento.'], 500);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el mantenimiento.'], 500);
        }
    }
}
