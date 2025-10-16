<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteCoord;
use App\Models\Zone;
use App\Models\Department;
use App\Models\Province;
use App\Models\District;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $zone_id = $request->input('zone_id');

        $routesQuery = Route::with(['zone', 'routecoords'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%$search%");
            })
            ->when($zone_id, function ($query, $zone_id) {
                return $query->where('zone_id', $zone_id);
            });

        if ($request->ajax()) {
            $routes = $routesQuery->get();
            $data = [];
            foreach ($routes as $route) {
                $start = $route->routecoords->where('type', 'start')->first();
                $end = $route->routecoords->where('type', 'end')->first();
                $data[] = [
                    'code' => $route->code,
                    'name' => $route->name,
                    'zone' => $route->zone->name ?? '-',
                    'start_point' => $start ? ($start->latitude . ', ' . $start->longitude) : '-',
                    'end_point' => $end ? ($end->latitude . ', ' . $end->longitude) : '-',
                    'distance' => $route->distance ? number_format($route->distance, 2) . ' km' : '-',
                    'actions' => view('admin.routes_zone.partials.actions', compact('route'))->render(),
                ];
            }
            return response()->json(['data' => $data]);
        }

        // Renderizado normal (Blade)
        $zones = Zone::all();
        return view('admin.routes_zone.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $zones = Zone::pluck('name', 'id');
        return view('admin.routes_zone.create', compact('zones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'zone_id' => 'required|exists:zones,id',
            'status' => 'required',
            'start_latitude' => 'required|numeric',
            'start_longitude' => 'required|numeric',
            'end_latitude' => 'required|numeric',
            'end_longitude' => 'required|numeric',
            'distance' => 'required|numeric',
        ]);

    // Guardar la ruta
    $route = new Route();
    $route->name = $request->name;
    $route->description = $request->description;
    $route->zone_id = $request->zone_id;
    $route->status = $request->status;
    $route->distance = $request->distance;
    $route->save();
    // Generar y guardar el código
    $route->code = 'RT' . str_pad($route->id, 3, '0', STR_PAD_LEFT);
    $route->save();

        // Guardar coordenada de inicio
        RouteCoord::create([
            'latitude' => $request->start_latitude,
            'longitude' => $request->start_longitude,
            'type' => 'start',
            'route_id' => $route->id,
        ]);

        // Guardar coordenada de fin
        RouteCoord::create([
            'latitude' => $request->end_latitude,
            'longitude' => $request->end_longitude,
            'type' => 'end',
            'route_id' => $route->id,
        ]);

        return redirect()->route('admin.routes.index')->with('success', 'Ruta registrada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $route = Route::with(['zone', 'routecoords'])->findOrFail($id);
        $start = $route->routecoords->where('type', 'start')->first();
        $end = $route->routecoords->where('type', 'end')->first();
        return view('admin.routes_zone.show', compact('route', 'start', 'end'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $route = Route::with('routecoords')->findOrFail($id);
        $zones = Zone::pluck('name', 'id');
        // Obtener coordenadas
        $start = $route->routecoords->where('type', 'start')->first();
        $end = $route->routecoords->where('type', 'end')->first();
        return view('admin.routes_zone.edit', compact('route', 'zones', 'start', 'end'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'zone_id' => 'required|exists:zones,id',
            'status' => 'required',
            'start_latitude' => 'required|numeric',
            'start_longitude' => 'required|numeric',
            'end_latitude' => 'required|numeric',
            'end_longitude' => 'required|numeric',
            'distance' => 'required|numeric',
        ]);

        $route = Route::findOrFail($id);
        $route->name = $request->name;
        $route->description = $request->description;
        $route->zone_id = $request->zone_id;
        $route->status = $request->status;
        $route->distance = $request->distance;
        $route->save();

        // Actualizar coordenadas de inicio
        $start = $route->routecoords->where('type', 'start')->first();
        if ($start) {
            $start->latitude = $request->start_latitude;
            $start->longitude = $request->start_longitude;
            $start->save();
        }

        // Actualizar coordenadas de fin
        $end = $route->routecoords->where('type', 'end')->first();
        if ($end) {
            $end->latitude = $request->end_latitude;
            $end->longitude = $request->end_longitude;
            $end->save();
        }

        return redirect()->route('admin.routes.index')->with('success', 'Ruta actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $route = Route::findOrFail($id);
        // Eliminar coordenadas asociadas
        $route->routecoords()->delete();
        $route->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Ruta eliminada correctamente.']);
        }
        return redirect()->route('admin.routes.index')->with('success', 'Ruta eliminada correctamente');
    }
}
