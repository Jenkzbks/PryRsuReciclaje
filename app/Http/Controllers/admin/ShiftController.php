<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Shift;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shift::all();

        if (request()->ajax()) {
            return DataTables()->of($shifts)
                ->addColumn('edit', function ($shift) {
                    return '<button class="btn btn-warning btn-sm btnEditar" id="' . $shift->id . '"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn('delete', function ($shift) {
                    return '<form action="' . route('admin.shifts.destroy', $shift) . '" method="POST" class="frmDelete">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->rawColumns(['edit', 'delete'])
                ->make(true);
        }

        return view('admin.shifts.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.shifts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:shifts,name'
            ]);

            Shift::create([
                'name' => $request->name,
                'description' => $request->description,
                'hora_in' => $request->hora_in,
                'hora_out' => $request->hora_out,
            ]);

            return response()->json(['message' => 'Turno registrado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al registrar el turno: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // not used for now
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shift = Shift::find($id);
        return view('admin.shifts.edit', compact('shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $shift = Shift::find($id);

            $request->validate([
                'name' => 'required|unique:shifts,name,' . $id,
            ]);

            $shift->update([
                'name' => $request->name,
                'description' => $request->description,
                'hora_in' => $request->hora_in,
                'hora_out' => $request->hora_out,
            ]);

            return response()->json(['message' => 'Turno actualizado correctamente'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el turno: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shift = Shift::find($id);
        $shift->delete();
        return redirect()->route('admin.shifts.index')->with('action', 'Turno eliminado');
    }
}
