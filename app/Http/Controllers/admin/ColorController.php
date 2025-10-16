<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $colors = Color::all();
            return DataTables::of($colors)
                ->addColumn('preview', function ($color) {
                    return '<div style="width: 50px; height: 30px; background-color: ' . $color->code . '; border: 1px solid #ccc; border-radius: 4px;"></div>';
                })
                ->addColumn("edit", function ($color) {
                    return '<button class="btn btn-warning btn-sm btnEditar" id="' . $color->id . '"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn("delete", function ($color) {
                    return '<form action="' . route('admin.colors.destroy', $color) . '" method="POST" class="frmDelete">' .
                        csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->rawColumns(['preview', 'edit', 'delete'])
                ->make(true);
        }

        return view('admin.colors.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Esta función podría no ser necesaria si usas modales
        // Pero es buena práctica tenerla por si se accede directamente a la URL
        return view('admin.colors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|unique:colors|max:100',
                'code' => 'required|max:200',
            ]);

            Color::create($request->all());

            return response()->json(["message" => "Color registrado correctamente"], 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Error al registrar el color: " . $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     * Devolvemos los datos del color en formato JSON para el modal de edición.
     */
    public function show(Color $color)
    {
        return response()->json($color);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Color $color)
    {
        // Similar a create(), puede no ser necesaria si todo es por modal
        return view('admin.colors.edit', compact('color'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Color $color)
    {
        try {
            $request->validate([
                'name' => 'required|unique:colors,name,' . $color->id . '|max:100',
                'code' => 'required|max:200',
            ]);

            $color->update($request->all());

            return response()->json(["message" => "Color actualizado correctamente"], 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Error al actualizar el color: " . $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color->delete();
        // Redirección para cuando el borrado no es por AJAX
        return redirect()->route('admin.colors.index')->with('action', 'Color eliminado');
    }
}
