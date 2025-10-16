<?php
namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\VehicleType;
use App\Models\Color;
use App\Models\VehicleImage;
use App\Http\Controllers\Controller;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['brand', 'model', 'type', 'color', 'images'])->paginate(20);
        $brands = Brand::all();
        $models = BrandModel::all();
        $types = VehicleType::all();
        $colors = Color::all();
        return view('admin.vehicles.index', compact('vehicles', 'brands', 'models', 'types', 'colors'));
    }

    public function create()
    {
        $brands = Brand::all();
        $models = BrandModel::all();
        $types = VehicleType::all();
        $colors = Color::all();
        return view('admin.vehicles.create', compact('brands', 'models', 'types', 'colors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'plate' => 'required|string|max:20',
            'year' => 'nullable|integer',
            'load_capacity' => 'nullable|numeric',
            'description' => 'nullable|string',
            'status' => 'required|in:1,0',
            'brand_id' => 'required|exists:brands,id',
            'model_id' => 'required|exists:brandmodels,id',
            'type_id' => 'required|exists:vehicletypes,id',
            'color_id' => 'required|exists:colors,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $vehicle = Vehicle::create($data);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vehicles', 'public');
            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image' => $imagePath,
                'profile' => 1,
            ]);
        }
        return response()->json(['message' => 'Vehículo registrado correctamente']);
    }

    public function filter(Request $request)
    {
        $query = Vehicle::with(['brand', 'model', 'type', 'color']);
        if ($request->filled('plate')) {
            $query->where('plate', 'like', '%' . $request->plate . '%');
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('model_id')) {
            $query->where('model_id', $request->model_id);
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $vehicles = $query->with(['brand', 'model', 'type', 'color', 'images'])->paginate(20);
        return view('admin.vehicles.partials.grid', compact('vehicles'))->render();
    }

    public function edit($id)
    {
        $vehicle = Vehicle::with('images')->findOrFail($id);
        $brands = Brand::all();
        $models = BrandModel::all();
        $types = VehicleType::all();
        $colors = Color::all();
        return view('admin.vehicles.edit', compact('vehicle', 'brands', 'models', 'types', 'colors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'plate' => 'required|string|max:20',
            'year' => 'nullable|integer',
            'load_capacity' => 'nullable|numeric',
            'description' => 'nullable|string',
            'status' => 'required|in:1,0',
            'brand_id' => 'required|exists:brands,id',
            'model_id' => 'required|exists:brandmodels,id',
            'type_id' => 'required|exists:vehicletypes,id',
            'color_id' => 'required|exists:colors,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $vehicle = Vehicle::findOrFail($id);
        $data = $request->all();
        $vehicle->update($data);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vehicles', 'public');
            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image' => $imagePath,
                'profile' => 1,
            ]);
        }
        return response()->json(['message' => 'Vehículo actualizado correctamente']);
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        // Delete associated images
        $vehicle->images()->delete();
        $vehicle->delete();
        return response()->json(['message' => 'Vehículo eliminado correctamente']);
    }
}
