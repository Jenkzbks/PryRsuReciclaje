<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Zone_J;
use App\Models\Province;
use App\Models\District;
use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class ZoneJController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $zones = Zone_J::with(['district.province.department']);

            return DataTables::of($zones)
                ->addColumn('code', function ($zone) {
                    return 'ZN' . str_pad($zone->id, 3, '0', STR_PAD_LEFT);
                })
                ->addColumn('full_location', function ($zone) {
                    return $zone->full_location;
                })
                ->addColumn('has_polygon', function ($zone) {
                    return $zone->has_polygon ? 'Sí' : 'No';
                })
                ->addColumn('show', function ($zone) {
                    return '<a href="#" data-id="' . $zone->id . '" class="btn btn-info btn-sm btn-ver-detalle-zona" title="Ver Detalle"><i class="fas fa-eye"></i></a>';
                })
                ->addColumn('edit', function ($zone) {
                    return '<a href="' . route('admin.zonesjenkz.edit', $zone->id) . '" class="btn btn-warning btn-sm" title="Editar"><i class="fas fa-pen"></i></a>';
                })
                ->addColumn('delete', function ($zone) {
                    return '<form action="' . route('admin.zonesjenkz.destroy', $zone->id) . '" method="POST" class="d-inline frmDelete">'
                        . csrf_field() . method_field('DELETE') .
                        '<button type="submit" class="btn btn-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>' .
                        '</form>';
                })
                ->rawColumns(['show', 'edit', 'delete'])
                ->make(true);
        }

        return view('admin.zones_jenkz.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::pluck('name', 'id');
        // Obtener todas las zonas con sus coords
        $zones = Zone_J::with('coords')->get();
        // Formatear para el frontend: array de arrays de lat/lng
        $zonesPolygons = $zones->map(function($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'coords' => $zone->coords->map(function($c) {
                    return ['lat' => $c->latitude, 'lng' => $c->longitude];
                })->toArray()
            ];
        })->filter(function($z) { return count($z['coords']) > 2; })->values();
        return view('admin.zones_jenkz.create', compact('departments', 'zonesPolygons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'average_waste' => 'nullable|numeric',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'district_id' => 'required|exists:districts,id',
            'coords' => 'required|array|min:3',
            'coords.*.lat' => 'required|numeric',
            'coords.*.lng' => 'required|numeric',
        ]);

        // Validar que el polígono no esté completamente dentro de otro ni se solape/intersecte
        $allZones = Zone_J::with('coords')->get();
        $newPolygon = $request->coords;
        foreach ($allZones as $zone) {
            $existing = $zone->coords->map(function($c) {
                return ['lat' => $c->latitude, 'lng' => $c->longitude];
            })->toArray();
            if (count($existing) > 2) {
                // Validar intersección
                if ($this->polygonsIntersect($newPolygon, $existing)) {
                    return response()->json([
                        'errors' => ['coords' => ['El polígono se solapa/intersecta con la zona existente (ID: ' . $zone->id . ').']],
                    ], 422);
                }
                // Validar que esté completamente dentro
                $allInside = true;
                foreach ($newPolygon as $pt) {
                    if (!$this->pointInPolygon($pt, $existing)) {
                        $allInside = false;
                        break;
                    }
                }
                if ($allInside) {
                    return response()->json([
                        'errors' => ['coords' => ['El polígono está completamente dentro de otra zona existente (ID: ' . $zone->id . ').']],
                    ], 422);
                }
            }
        }

        $zone = new Zone_J();
        $zone->name = $request->name;
        $zone->average_waste = $request->average_waste;
        $zone->description = $request->description;
        $zone->status = $request->status;
        $zone->district_id = $request->district_id;
        $zone->save();

        foreach ($request->coords as $index => $coord) {
            $zone->coords()->create([
                'coord_index' => $index,
                'latitude' => $coord['lat'],
                'longitude' => $coord['lng'],
            ]);
        }

        return redirect()->route('admin.zonesjenkz.index')->with('success', 'Zona registrada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $zone = Zone_J::with(['district.province.department', 'coords'])->findOrFail($id);
        return view('admin.zones_jenkz.template.info_show', compact('zone'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $zone = Zone_J::with(['district.province.department', 'coords'])->findOrFail($id);
        $departments = Department::pluck('name', 'id');
        // Obtener todas las demás zonas con sus coords (menos la actual)
        $zones = Zone_J::with('coords')->where('id', '!=', $id)->get();
        $zonesPolygons = $zones->map(function($z) {
            return [
                'id' => $z->id,
                'name' => $z->name,
                'coords' => $z->coords->map(function($c) {
                    return ['lat' => $c->latitude, 'lng' => $c->longitude];
                })->toArray()
            ];
        })->filter(function($z) { return count($z['coords']) > 2; })->values();
        return view('admin.zones_jenkz.edit', compact('zone', 'departments', 'zonesPolygons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'average_waste' => 'nullable|numeric',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'district_id' => 'required|exists:districts,id',
            'coords' => 'required|array|min:3',
            'coords.*.lat' => 'required|numeric',
            'coords.*.lng' => 'required|numeric',
        ]);

        // Validar que el polígono no esté completamente dentro de otro ni se solape/intersecte (excluyendo la propia zona)
        $allZones = Zone_J::with('coords')->where('id', '!=', $id)->get();
        $newPolygon = $request->coords;
        foreach ($allZones as $zone) {
            $existing = $zone->coords->map(function($c) {
                return ['lat' => $c->latitude, 'lng' => $c->longitude];
            })->toArray();
            if (count($existing) > 2) {
                // Validar intersección
                if ($this->polygonsIntersect($newPolygon, $existing)) {
                    return response()->json([
                        'errors' => ['coords' => ['El polígono se solapa/intersecta con la zona existente (Zona: ' . $zone->name . ').']],
                    ], 422);
                }
                // Validar que esté completamente dentro
                $allInside = true;
                foreach ($newPolygon as $pt) {
                    if (!$this->pointInPolygon($pt, $existing)) {
                        $allInside = false;
                        break;
                    }
                }
                if ($allInside) {
                    return response()->json([
                        'errors' => ['coords' => ['El polígono está completamente dentro de otra zona existente (Zona: ' . $zone->name . ').']],
                    ], 422);
                }
            }
        }

        $zone = Zone_J::findOrFail($id);
        $zone->name = $request->name;
        $zone->average_waste = $request->average_waste;
        $zone->description = $request->description;
        $zone->status = $request->status;
        $zone->district_id = $request->district_id;
        $zone->save();

        // Eliminar coords anteriores
        $zone->coords()->delete();
        // Guardar nuevas coords
        foreach ($request->coords as $index => $coord) {
            $zone->coords()->create([
                'coord_index' => $index,
                'latitude' => $coord['lat'],
                'longitude' => $coord['lng'],
            ]);
        }

        return redirect()->route('admin.zones.index')->with('success', 'Zona actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $zone = Zone_J::findOrFail($id);
        // Eliminar coordenadas asociadas (por si no hay onDelete cascade)
        $zone->coords()->delete();
        $zone->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Zona eliminada correctamente.']);
        }
        return redirect()->route('admin.zonesjenkz.index')->with('success', 'Zona eliminada correctamente');
    }

    /**
     * Obtener provincias por departamento (AJAX)
     */
    public function getProvinces(Request $request)
    {
        $departmentId = $request->query('department_id');
        $provinces = Province::where('department_id', $departmentId)->pluck('name', 'id');
        return response()->json($provinces);
    }

    /**
     * Obtener distritos por provincia (AJAX)
     */
    public function getDistricts(Request $request)
    {
        $provinceId = $request->query('province_id');
        $districts = District::where('province_id', $provinceId)->pluck('name', 'id');
        return response()->json($districts);
    }

    // Algoritmo ray-casting para saber si un punto está dentro de un polígono
    private function pointInPolygon($point, $polygon)
    {
        $x = $point['lat'];
        $y = $point['lng'];
        $inside = false;
        $n = count($polygon);
        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];
            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-10) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }

    // Verifica si dos polígonos se intersectan (algoritmo de intersección de segmentos)
    private function polygonsIntersect($poly1, $poly2)
    {
        $count1 = count($poly1);
        $count2 = count($poly2);
        for ($i = 0; $i < $count1; $i++) {
            $a1 = $poly1[$i];
            $a2 = $poly1[($i + 1) % $count1];
            for ($j = 0; $j < $count2; $j++) {
                $b1 = $poly2[$j];
                $b2 = $poly2[($j + 1) % $count2];
                if ($this->segmentsIntersect($a1, $a2, $b1, $b2)) {
                    return true;
                }
            }
        }
        // También verificar si uno está completamente dentro del otro (ya cubierto en la validación principal)
        return false;
    }

    // Verifica si dos segmentos (a1-a2 y b1-b2) se intersectan
    private function segmentsIntersect($a1, $a2, $b1, $b2)
    {
        $det = function($a, $b, $c, $d) {
            return $a * $d - $b * $c;
        };
        $x1 = $a1['lat']; $y1 = $a1['lng'];
        $x2 = $a2['lat']; $y2 = $a2['lng'];
        $x3 = $b1['lat']; $y3 = $b1['lng'];
        $x4 = $b2['lat']; $y4 = $b2['lng'];

        $den = $det($x1 - $x2, $y1 - $y2, $x3 - $x4, $y3 - $y4);
        if ($den == 0) return false; // paralelos

        $t = $det($x1 - $x3, $y1 - $y3, $x3 - $x4, $y3 - $y4) / $den;
        $u = -$det($x1 - $x2, $y1 - $y2, $x1 - $x3, $y1 - $y3) / $den;

        return $t >= 0 && $t <= 1 && $u >= 0 && $u <= 1;
    }

    /**
     * Validar polígono contra zonas existentes (AJAX)
     */
    public function validatePolygon(Request $request)
    {
        $request->validate([
            'coords' => 'required|array|min:3',
            'coords.*.lat' => 'required|numeric',
            'coords.*.lng' => 'required|numeric',
        ]);
        $newPolygon = $request->coords;
        $excludeId = $request->input('exclude_id');
        $allZones = Zone_J::with('coords');
        if ($excludeId) {
            $allZones = $allZones->where('id', '!=', $excludeId);
        }
        $allZones = $allZones->get();
        foreach ($allZones as $zone) {
            $existing = $zone->coords->map(function($c) {
                return ['lat' => $c->latitude, 'lng' => $c->longitude];
            })->toArray();
            if (count($existing) > 2) {
                if ($this->polygonsIntersect($newPolygon, $existing)) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'El polígono se solapa/intersecta con la zona existente (Zona: ' . $zone->name . ').'
                    ], 200);
                }
                $allInside = true;
                foreach ($newPolygon as $pt) {
                    if (!$this->pointInPolygon($pt, $existing)) {
                        $allInside = false;
                        break;
                    }
                }
                if ($allInside) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'El polígono está completamente dentro de otra zona existente (Zona: ' . $zone->name . ').'
                    ], 200);
                }
            }
        }
        return response()->json(['valid' => true]);
        
        
    }

        /**
     * Endpoint para devolver todas las zonas con coords (para el mapa del modal)
     */
    public function polygonsJson()
    {
        $zones = Zone_J::with(['coords', 'district.province.department'])
            ->where('status', 1)
            ->get();
        $result = $zones->map(function($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'description' => $zone->description,
                'full_location' => $zone->full_location,
                'coords' => $zone->coords->map(function($c) {
                    return ['lat' => $c->latitude, 'lng' => $c->longitude];
                })->toArray(),
            ];
        })->filter(function($z) { return count($z['coords']) > 2; })->values();
        return response()->json($result);
    }
        /**
     * Endpoint para obtener lat/lng/zoom de un distrito
     */
    public function getDistrictData(Request $request)
    {
        $district = District::find($request->query('district_id'));
        if ($district && $district->latitude && $district->longitude) {
            return response()->json([
                'lat' => $district->latitude,
                'lng' => $district->longitude,
                'zoom' => $district->zoom ?? 15
            ]);
        }
        return response()->json(null);
    }
    /**
     * Muestra la vista de mapa de zonas activas.
     */
    public function map()
    {
        // Obtener todas las zonas activas con sus coords
        $zones = Zone_J::with('coords')->where('status', 1)->get();
        $zonesPolygons = $zones->map(function($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'coords' => $zone->coords->map(function($c) {
                    return ['lat' => $c->latitude, 'lng' => $c->longitude];
                })->toArray(),
                'description' => $zone->description,
                'full_location' => $zone->full_location ?? null,
            ];
        })->filter(function($z) { return count($z['coords']) > 2; })->values();
        return view('admin.zones_jenkz.view_map', compact('zonesPolygons'));
    }
}
