<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceScheduleController extends Controller
{
    /**
     * Display schedules for a specific maintenance
     */
    public function index(Request $request, Maintenance $maintenance = null)
    {
        \Log::info('MaintenanceScheduleController@index called', [
            'maintenance_passed' => $maintenance ? $maintenance->id : null,
            'request_maintenance_id' => $request->get('maintenance_id'),
            'route_parameters' => $request->route()->parameters(),
            'full_url' => $request->fullUrl()
        ]);

        // Si no se especifica un mantenimiento, obtenerlo del parámetro maintenance_id
        if (!$maintenance && $request->has('maintenance_id')) {
            $maintenance = Maintenance::find($request->maintenance_id);
        }

        if ($request->ajax()) {
            // Determinar el maintenance_id a usar
            $maintenanceId = null;
            if ($maintenance) {
                $maintenanceId = $maintenance->id;
            } else if ($request->has('maintenance_id')) {
                $maintenanceId = $request->maintenance_id;
            }

            // Si solo se requieren estadísticas
            if ($request->has('stats_only')) {
                $statsQuery = MaintenanceSchedule::query();
                if ($maintenanceId) {
                    $statsQuery->where('maintenance_id', $maintenanceId);
                }
                
                $total = $statsQuery->count();
                $scheduled = (clone $statsQuery)->where('status', 'scheduled')->count();
                $in_progress = (clone $statsQuery)->where('status', 'in_progress')->count();
                $completed = (clone $statsQuery)->where('status', 'completed')->count();

                return response()->json([
                    'success' => true,
                    'stats' => [
                        'total' => $total,
                        'scheduled' => $scheduled,
                        'in_progress' => $in_progress,
                        'completed' => $completed
                    ]
                ]);
            }

            // Query principal para obtener schedules
            $query = MaintenanceSchedule::with(['maintenance', 'vehicle', 'driver']);
            
            if ($maintenanceId) {
                $query->where('maintenance_id', $maintenanceId);
            }

            // Aplicar filtros opcionales - IGNORAR VALORES NULL
            if ($request->has('day_of_week') && $request->day_of_week !== '' && $request->day_of_week !== null && $request->day_of_week !== 'null') {
                $query->where('day_of_week', $request->day_of_week);
            }
            
            if ($request->has('maintenance_type') && $request->maintenance_type !== '' && $request->maintenance_type !== null && $request->maintenance_type !== 'null') {
                $query->where('maintenance_type', $request->maintenance_type);
            }
            
            if ($request->has('status') && $request->status !== '' && $request->status !== null && $request->status !== 'null') {
                $query->where('status', $request->status);
            }

            $schedules = $query->orderBy('day_of_week')
                              ->orderBy('start_time')
                              ->get();

            // Debug adicional
            $allSchedules = MaintenanceSchedule::all();
            $maintenanceSchedules = MaintenanceSchedule::where('maintenance_id', $maintenanceId)->get();
            
            // Consulta sin relaciones para comparar
            $schedulesWithoutRelations = MaintenanceSchedule::query();
            if ($maintenanceId) {
                $schedulesWithoutRelations->where('maintenance_id', $maintenanceId);
            }
            
            // Aplicar los mismos filtros sin relaciones
            if ($request->has('day_of_week') && $request->day_of_week !== '') {
                $schedulesWithoutRelations->where('day_of_week', $request->day_of_week);
            }
            if ($request->has('maintenance_type') && $request->maintenance_type !== '') {
                $schedulesWithoutRelations->where('maintenance_type', $request->maintenance_type);
            }
            if ($request->has('status') && $request->status !== '') {
                $schedulesWithoutRelations->where('status', $request->status);
            }
            
            $schedulesWithoutRelationsResult = $schedulesWithoutRelations->get();

            return response()->json([
                'success' => true,
                'data' => $schedules,
                'pagination' => null,
                'debug' => [
                    'maintenance_id' => $maintenanceId,
                    'total_schedules' => $schedules->count(),
                    'schedules_without_relations_count' => $schedulesWithoutRelationsResult->count(),
                    'all_schedules_in_db' => $allSchedules->count(),
                    'schedules_for_maintenance' => $maintenanceSchedules->count(),
                    'schedules_raw' => $maintenanceSchedules->toArray(),
                    'schedules_without_relations' => $schedulesWithoutRelationsResult->toArray(),
                    'maintenance_object' => $maintenance ? $maintenance->toArray() : null,
                    'request_all_params' => $request->all(),
                    'final_query_sql' => $query->toSql(),
                    'final_query_bindings' => $query->getBindings(),
                    'filters' => [
                        'day_of_week' => $request->day_of_week,
                        'maintenance_type' => $request->maintenance_type,
                        'status' => $request->status
                    ]
                ]
            ]);
        }

        // Si es una request normal, mostrar la vista
        if ($maintenance) {
            return view('maintenance.schedules.index', compact('maintenance'));
        }

        // Para el índice general de schedules (fallback)
        return view('maintenance.schedules.general');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'maintenance_id' => 'required|exists:maintenances,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:employee,id',
            'day_of_week' => 'required|in:0,1,2,3,4,5,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'maintenance_type' => 'required|in:preventive,corrective,predictive',
            'recurrence_weeks' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,in_progress,completed',
            'description' => 'nullable|string|max:500'
        ], [
            'maintenance_id.required' => 'El mantenimiento es obligatorio.',
            'vehicle_id.required' => 'El vehículo es obligatorio.',
            'driver_id.required' => 'El responsable es obligatorio.',
            'day_of_week.required' => 'El día de la semana es obligatorio.',
            'day_of_week.in' => 'El día de la semana debe ser válido.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'maintenance_type.required' => 'El tipo de mantenimiento es obligatorio.',
            'maintenance_type.in' => 'El tipo de mantenimiento debe ser válido.',
            'recurrence_weeks.required' => 'La recurrencia es obligatoria.',
            'status.required' => 'El estado es obligatorio.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar solapamiento de horarios
        if (MaintenanceSchedule::hasScheduleOverlap(
            $request->vehicle_id,
            $request->day_of_week,
            $request->start_time,
            $request->end_time
        )) {
            return response()->json([
                'success' => false,
                'message' => 'El horario se solapa con otro existente para este vehículo en el mismo día.'
            ], 422);
        }

        $schedule = MaintenanceSchedule::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Horario creado exitosamente.',
            'data' => $schedule->load(['vehicle', 'driver'])
        ]);
    }

    /**
     * Display the specified schedule
     */
    public function show(MaintenanceSchedule $schedule)
    {
        return response()->json([
            'success' => true,
            'data' => $schedule->load(['maintenance', 'vehicle', 'driver', 'activities'])
        ]);
    }

    public function update(Request $request, MaintenanceSchedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:employee,id',
            'day_of_week' => 'required|in:0,1,2,3,4,5,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'maintenance_type' => 'required|in:preventive,corrective,predictive',
            'recurrence_weeks' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,in_progress,completed',
            'description' => 'nullable|string|max:500'
        ], [
            'vehicle_id.required' => 'El vehículo es obligatorio.',
            'driver_id.required' => 'El responsable es obligatorio.',
            'day_of_week.required' => 'El día de la semana es obligatorio.',
            'day_of_week.in' => 'El día de la semana debe ser válido.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'maintenance_type.required' => 'El tipo de mantenimiento es obligatorio.',
            'maintenance_type.in' => 'El tipo de mantenimiento debe ser válido.',
            'recurrence_weeks.required' => 'La recurrencia es obligatoria.',
            'status.required' => 'El estado es obligatorio.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar solapamiento de horarios (excluyendo el actual)
        if (MaintenanceSchedule::hasScheduleOverlap(
            $request->vehicle_id,
            $request->day_of_week,
            $request->start_time,
            $request->end_time,
            $schedule->id
        )) {
            return response()->json([
                'success' => false,
                'message' => 'El horario se solapa con otro existente para este vehículo en el mismo día.'
            ], 422);
        }

        $schedule->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Horario actualizado exitosamente.',
            'data' => $schedule->fresh(['vehicle', 'driver'])
        ]);
    }

    /**
     * Remove the specified schedule
     */
    public function destroy(MaintenanceSchedule $schedule)
    {
        if (!$schedule->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el horario porque tiene actividades registradas.'
            ], 422);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Horario eliminado exitosamente.'
        ]);
    }

    /**
     * Get available vehicles for scheduling
     */
    public function getAvailableVehicles(Request $request)
    {
        $vehicles = Vehicle::select('id', 'license_plate', 'brand', 'model')
                          ->orderBy('license_plate')
                          ->get()
                          ->map(function($vehicle) {
                              return [
                                  'id' => $vehicle->id,
                                  'text' => $vehicle->license_plate . ' - ' . $vehicle->brand . ' ' . $vehicle->model,
                                  'license_plate' => $vehicle->license_plate
                              ];
                          });

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    /**
     * Get available drivers (employees)
     */
    public function getAvailableDrivers(Request $request)
    {
        $drivers = Employee::where('status', 1)
                          ->select('id', 'names', 'lastnames', 'dni')
                          ->orderBy('names')
                          ->get()
                          ->map(function($driver) {
                              return [
                                  'id' => $driver->id,
                                  'text' => $driver->names . ' ' . $driver->lastnames . ' - ' . $driver->dni,
                                  'name' => $driver->names . ' ' . $driver->lastnames
                              ];
                          });

        return response()->json([
            'success' => true,
            'data' => $drivers
        ]);
    }

    /**
     * Validate schedule overlap
     */
    public function validateScheduleOverlap(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'day_of_week' => 'required|in:LUNES,MARTES,MIERCOLES,JUEVES,VIERNES,SABADO',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_id' => 'nullable|exists:maintenanceschedules,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $hasOverlap = MaintenanceSchedule::hasScheduleOverlap(
            $request->vehicle_id,
            $request->day_of_week,
            $request->start_time,
            $request->end_time,
            $request->exclude_id
        );

        return response()->json([
            'success' => true,
            'has_overlap' => $hasOverlap
        ]);
    }
}