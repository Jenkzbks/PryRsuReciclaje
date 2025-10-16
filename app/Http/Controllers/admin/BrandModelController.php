<?php

namespace App\Http\Controllers\admin;

use App\Models\BrandModel;
use App\Models\Brand;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brandModels = BrandModel::with('brand')->get();

        if(request()->ajax()){
            return DataTables()->of($brandModels)
            ->addColumn('brand_name', function($brandModel){
                return $brandModel->brand->name;
            })
            ->addColumn("edit",function($brandModel){
                return '<button class="btn btn-warning btn-sm btnEditar" id="' .$brandModel->id .'"><i class="fas fa-pen"></i></button>';
            })
            ->addColumn("delete",function($brandModel){    
                return '<form action="' . route('admin.brandmodels.destroy', $brandModel) .'" method="POST" class="frmDelete">' .
                                    csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button> </form>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);       
        }

        return view('admin.brandmodels.index', compact('brandModels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        return view('admin.brandmodels.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                "name" => "required|unique:brandmodels,name",
                "brand_id" => "required|exists:brands,id"
            ]);

            BrandModel::create([
                "name" => $request->name,
                "description" => $request->description,
                "brand_id" => $request->brand_id
            ]);   
            
            return response()->json(["message"=>"Modelo registrado correctamente"],200);     
        } catch (\Throwable $th) {
            return response()->json(["message"=>"Error al registrar el modelo: " . $th->getMessage()],500);
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
        $brandModel = BrandModel::find($id);
        $brands = Brand::all();
        return view('admin.brandmodels.edit', compact('brandModel', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $brandModel = BrandModel::find($id);

            $request->validate([
                "name" => "required|unique:brandmodels,name," . $id,
                "brand_id" => "required|exists:brands,id"
            ]);

            $brandModel->update([
                'name' => $request->name,
                'description' => $request->description,
                'brand_id' => $request->brand_id
            ]);
            
            return response()->json(["message"=>"Modelo actualizado correctamente"],200);
        } catch (\Throwable $th) {
            return response()->json(["message"=>"Error al actualizar el modelo: " . $th->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brandModel = BrandModel::find($id);
        $brandModel->delete();
        return redirect()->route('admin.brandmodels.index')->with('action', 'Modelo eliminado');
    }
}