<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\Zonecoord;
use App\Models\Department;
use App\Models\Province;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $query = Zone::with(['district.department', 'district.province', 'province.department']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department_id')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('district.department', function($subQ) use ($request) {
                    $subQ->where('id', $request->department_id);
                })
                ->orWhereHas('province.department', function($subQ) use ($request) {
                    $subQ->where('id', $request->department_id);
                });
            });
        }

        if ($request->filled('province_id')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('district.province', function($subQ) use ($request) {
                    $subQ->where('id', $request->province_id);
                })
                ->orWhere('province_id', $request->province_id);
            });
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        $zones = $query->paginate(10);

        // Datos para los filtros
        $departments = Department::all();
        $provinces = collect();
        $districts = collect();

        if ($request->filled('department_id')) {
            $provinces = Province::where('department_id', $request->department_id)->get();
        }

        if ($request->filled('province_id')) {
            $districts = District::where('province_id', $request->province_id)->get();
        }

        return view('admin.zones.index', compact('zones', 'departments', 'provinces', 'districts'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('admin.zones.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'nullable|exists:districts,id',
            'description' => 'nullable|string',
            'polygon_coordinates' => 'nullable|json',
            'area' => 'nullable|numeric|min:0',
        ]);

        // Validación adicional: si se selecciona distrito, debe pertenecer a la provincia
        $validator->after(function ($validator) use ($request) {
            if ($request->district_id && $request->province_id) {
                $district = District::find($request->district_id);
                if ($district && $district->province_id != $request->province_id) {
                    $validator->errors()->add('district_id', 'El distrito seleccionado no pertenece a la provincia.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $polygonCoords = $request->polygon_coordinates ? json_decode($request->polygon_coordinates, true) : null;
        
        $zone = Zone::create([
            'name' => $request->name,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'description' => $request->description,
            'polygon_coordinates' => $polygonCoords,
            'area' => $request->area,
        ]);

        if ($polygonCoords && is_array($polygonCoords)) {
            foreach ($polygonCoords as $coord) {
                if (isset($coord['lat']) && isset($coord['lng'])) {
                    $zone->zonecoords()->create([
                        'latitude' => $coord['lat'],
                        'longitude' => $coord['lng'],
                    ]);
                } elseif (isset($coord[0]) && isset($coord[1])) {
                    $zone->zonecoords()->create([
                        'latitude' => $coord[0],
                        'longitude' => $coord[1],
                    ]);
                }
            }
        }

        return redirect()->route('admin.zones.index')->with('success', 'Zona creada exitosamente.');
    }

 public function show(\App\Models\Zone $zone, \Illuminate\Http\Request $request)
{
    // Renderiza la vista completa en memoria para extraer secciones
    $sections = view('admin.zones.show', compact('zone'))->renderSections();

    if ($request->ajax()) {
        // Devuelve solo el contenido de la sección "content" para el modal
        return $sections['content'];
    }

    // Acceso directo por URL: página completa normal
    return view('admin.zones.show', compact('zone'));
}




    public function edit(Zone $zone)
    {
        $zone->load(['district.department', 'district.province', 'province.department']);
        
        $departments = Department::all();
        
        $departmentId = null;
        if ($zone->district) {
            $departmentId = $zone->district->department_id;
        } elseif ($zone->province) {
            $departmentId = $zone->province->department_id;
        }
        
        $provinces = $departmentId ? Province::where('department_id', $departmentId)->get() : collect();
        
        // Obtener distritos de la provincia seleccionada
        $provinceId = $zone->district ? $zone->district->province_id : $zone->province_id;
        $districts = $provinceId ? District::where('province_id', $provinceId)->get() : collect();
        
        return view('admin.zones.edit', compact('zone', 'departments', 'provinces', 'districts'));
    }

    public function update(Request $request, Zone $zone)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'nullable|exists:districts,id',
            'description' => 'nullable|string',
            'polygon_coordinates' => 'nullable|json',
            'area' => 'nullable|numeric|min:0',
        ]);

        // Validación adicional: si se selecciona distrito, debe pertenecer a la provincia
        $validator->after(function ($validator) use ($request) {
            if ($request->district_id && $request->province_id) {
                $district = District::find($request->district_id);
                if ($district && $district->province_id != $request->province_id) {
                    $validator->errors()->add('district_id', 'El distrito seleccionado no pertenece a la provincia.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $polygonCoords = $request->polygon_coordinates ? json_decode($request->polygon_coordinates, true) : null;
        
        $zone->update([
            'name' => $request->name,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'description' => $request->description,
            'polygon_coordinates' => $polygonCoords,
            'area' => $request->area,
        ]);

        // Actualizar coordenadas individuales en zonecoords
        // Eliminar coordenadas existentes
        $zone->zonecoords()->delete();
        
        // Crear nuevas coordenadas
        if ($polygonCoords && is_array($polygonCoords)) {
            foreach ($polygonCoords as $coord) {
                if (isset($coord['lat']) && isset($coord['lng'])) {
                    $zone->zonecoords()->create([
                        'latitude' => $coord['lat'],
                        'longitude' => $coord['lng'],
                    ]);
                } elseif (isset($coord[0]) && isset($coord[1])) {
                    // Formato alternativo [lat, lng]
                    $zone->zonecoords()->create([
                        'latitude' => $coord[0],
                        'longitude' => $coord[1],
                    ]);
                }
            }
        }

        return redirect()->route('admin.zones.index')->with('success', 'Zona actualizada exitosamente.');
    }

    public function destroy(Zone $zone)
    {
        // Eliminar coordenadas relacionadas
        $zone->zonecoords()->delete();
        
        // Eliminar la zona
        $zone->delete();
        return redirect()->route('admin.zones.index')->with('success', 'Zona eliminada exitosamente.');
    }

    // API endpoints para selects dependientes
    public function getProvinces(Request $request)
    {
        $provinces = Province::where('department_id', $request->department_id)->get();
        return response()->json($provinces);
    }

    public function getDistricts(Request $request)
    {
        $districts = District::where('province_id', $request->province_id)->get();
        return response()->json($districts);
    }

    public function getDepartmentCoordinates(Request $request)
    {
        $department = Department::find($request->department_id);
        
        if ($department && $department->latitude && $department->longitude) {
            return response()->json([
                'latitude' => $department->latitude,
                'longitude' => $department->longitude,
                'zoom_level' => $department->zoom_level,
                'name' => $department->name
            ]);
        }
        
        return response()->json(['error' => 'Coordinates not found'], 404);
    }

    public function getZonesPolygons()
    {
        $zones = Zone::with(['district.province.department', 'province.department'])
            ->whereNotNull('polygon_coordinates')
            ->get()
            ->map(function ($zone) {
                $locationName = '';
                if ($zone->district) {
                    $locationName = $zone->district->name . ', ' . 
                                  $zone->district->province->name . ', ' . 
                                  $zone->district->province->department->name;
                } elseif ($zone->province) {
                    $locationName = $zone->province->name . ', ' . 
                                  $zone->province->department->name;
                }

                return [
                    'id' => $zone->id,
                    'name' => $zone->name,
                    'location' => $locationName,
                    'polygon_coordinates' => $zone->polygon_coordinates,
                    'area' => $zone->area,
                    'description' => $zone->description
                ];
            });

        return response()->json($zones);
    }
}