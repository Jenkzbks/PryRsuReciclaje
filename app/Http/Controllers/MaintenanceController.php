<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Maintenance::with(['schedules.vehicle', 'schedules.driver'])
                               ->orderBy('id', 'asc');

            // Filtros
            if ($request->filled('status')) {
                switch ($request->status) {
                    case 'active':
                        $query->active();
                        break;
                    case 'upcoming':
                        $query->upcoming();
                        break;
                    case 'finished':
                        $query->finished();
                        break;
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('name', 'like', "%{$search}%");
            }

            $maintenances = $query->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $maintenances->items(),
                'pagination' => [
                    'current_page' => $maintenances->currentPage(),
                    'last_page' => $maintenances->lastPage(),
                    'per_page' => $maintenances->perPage(),
                    'total' => $maintenances->total()
                ]
            ]);
        }

        return view('maintenance.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de fin no puede ser menor que la fecha de inicio.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar solapamiento de fechas
        if (Maintenance::hasOverlap($request->start_date, $request->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'Las fechas se solapan con otro mantenimiento existente.'
            ], 422);
        }

        $maintenance = Maintenance::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento creado exitosamente.',
            'data' => $maintenance->load(['schedules.vehicle', 'schedules.driver'])
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Maintenance $maintenance)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $maintenance->load(['schedules.vehicle', 'schedules.driver', 'schedules.activities'])
            ]);
        }

        return view('maintenance.show', compact('maintenance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de fin no puede ser menor que la fecha de inicio.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar solapamiento de fechas (excluyendo el actual)
        if (Maintenance::hasOverlap($request->start_date, $request->end_date, $maintenance->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Las fechas se solapan con otro mantenimiento existente.'
            ], 422);
        }

        $maintenance->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento actualizado exitosamente.',
            'data' => $maintenance->fresh(['schedules.vehicle', 'schedules.driver'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maintenance $maintenance)
    {
        if (!$maintenance->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el mantenimiento porque tiene horarios asociados.'
            ], 422);
        }

        $maintenance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mantenimiento eliminado exitosamente.'
        ]);
    }

    /**
     * Get maintenance statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Maintenance::count(),
            'active' => Maintenance::active()->count(),
            'upcoming' => Maintenance::upcoming()->count(),
            'finished' => Maintenance::finished()->count(),
            'total_schedules' => MaintenanceSchedule::count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Validate date overlap
     */
    public function validateDateOverlap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'exclude_id' => 'nullable|exists:maintenances,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $hasOverlap = Maintenance::hasOverlap(
            $request->start_date, 
            $request->end_date, 
            $request->exclude_id
        );

        return response()->json([
            'success' => true,
            'has_overlap' => $hasOverlap
        ]);
    }
}