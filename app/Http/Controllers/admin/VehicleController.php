<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle; 
use Illuminate\Http\Request;

class VehicleController extends Controller
{

    public function index()
    {

        $vehicles = Vehicle::with(['model.brand', 'type'])
                           ->latest()
                           ->paginate(10); // Muestra 10 vehículos por página

        return view('admin.vehicles.index', compact('vehicles'));
    }
    public function create()
{
    // Obtenemos los datos para los selectores del formulario
    $brands = Brand::all();
    $models = BrandModel::all();
    $types = VehicleType::all();
    $colors = Color::all();

    // Devolvemos la vista create con todas las variables necesarias
    return view('admin.vehicles.create', compact('brands', 'models', 'types', 'colors'));
}


public function edit(Vehicle $vehicle)
{
    // Hacemos lo mismo que en create
    $brands = Brand::all();
    $models = BrandModel::all();
    $types = VehicleType::all();
    $colors = Color::all();
    
    // Devolvemos la vista edit, pasándole el vehículo a editar y las colecciones
    return view('admin.vehicles.edit', compact('vehicle', 'brands', 'models', 'types', 'colors'));
}

}