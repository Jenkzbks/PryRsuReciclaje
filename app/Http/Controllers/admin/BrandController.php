<?php

namespace App\Http\Controllers\admin;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::all();

        if(request()->ajax()){
            return DataTables()->of($brands)
            ->addColumn('logo', function($brand){
                return '<img src="' . ($brand->logo == '' ? asset('/storage/brand_logo/noimage.jpg') : asset($brand->logo)) . '"
                                    width="70px" height="50px">';
            })
            ->addColumn("edit",function($brand){
                return '<button class="btn btn-warning btn-sm btnEditar" id="' .$brand->id .'"><i
                                        class="fas fa-pen"></i></button>';
            })
            ->addColumn("delete",function($brand){    
                return '<form action="' . route('admin.brands.destroy', $brand) .'" method="POST" class="frmDelete">' .
                                    csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm"><i
                                            class="fas fa-trash"></i></button> </form>';
            })
            ->rawColumns(['logo', 'edit', 'delete'])
            ->make(true);       
        }



        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('admin.brands.create');
}
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Brand::create($request->all());
        try {
            $logo = "";

                    $request->validate([
                        "name" => "unique:brands",
                    ]);
                    if($request->logo != "" ){
                        $image = $request->file("logo")-> store("public/brand_logo");
                        $logo = Storage::url($image);
                    }

                    Brand::create([
                        "name" => $request->name,
                        "logo" => $logo,
                        "description" => $request->description
                    ]);   
                    return response ()-> json(["message"=>"Marca registrada correctamente"],200);     
        } catch (\Throwable $th) {
            return response()-> json(["message"=>"Error al registrar la marca: " .$th->getMessage()],500);
        }
        
        
        //return redirect()->route('admin.brands.index')->with('action', 'Marca registrada');
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
        $brand = Brand::find($id);
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            $brand = Brand::find($id);
            //$brand->update($request->all());

            $logo = "";

            $request->validate([
                "name" => "unique:brands,name," . $id
            ]);
                    
            if($request->logo != ""){
                $image = $request->file('logo')->store('public/brand_logo');
                $logo = Storage::url($image);

                $brand->update([
                'name' => $request->name,
                'description' => $request->description,
                'logo' => $logo,
                ]);
            } else {
                $brand->update([
                'name' => $request->name,
                'description' => $request->description,
                ]);
            }
            return response ()-> json(["message"=>"Marca actualizada correctamente"],200);
        } catch (\Throwable $th) {
            return response()-> json(["message"=>"Error al actualizar la marca: " .$th->getMessage()],500);
        }

        return redirect()->route('admin.brands.index')->with('action', 'Marca actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('action', 'Marca eliminada');
    }
}
