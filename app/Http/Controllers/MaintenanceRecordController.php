<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MaintenanceRecordController extends Controller
{
    /**
     * Display activities for a specific schedule
     */
    public function index(Request $request, MaintenanceSchedule $schedule = null)
    {
        // Obtener schedule desde parámetro de ruta o query string
        if (!$schedule && $request->has('schedule_id')) {
            $schedule = MaintenanceSchedule::find($request->schedule_id);
            if (!$schedule) {
                return response()->json(['success' => false, 'message' => 'Horario no encontrado'], 404);
            }
        }

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'ID de horario requerido'], 400);
        }

        // Si es una petición AJAX, devolver datos JSON
        if ($request->ajax()) {
            $activities = MaintenanceRecord::with(['schedule.maintenance', 'schedule.vehicle'])
                                         ->where('schedule_id', $schedule->id)
                                         ->orderBy('maintenance_date', 'desc')
                                         ->get();

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        }

        // Si es una petición normal, devolver la vista
        return view('maintenance.activities.index', compact('schedule'));
    }

    /**
     * Store a newly created activity
     */
    public function store(Request $request)
    {
        // Debug: ver qué datos llegan
        \Log::info('MaintenanceRecordController store - Request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:maintenanceschedules,id',
            'maintenance_date' => 'required|date',
            'descripcion' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'schedule_id.required' => 'El horario es obligatorio.',
            'maintenance_date.required' => 'La fecha es obligatoria.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede superar los 1000 caracteres.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'image.max' => 'La imagen no puede superar los 2MB.'
        ]);

        if ($validator->fails()) {
            \Log::warning('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar que la fecha esté dentro del período de mantenimiento y corresponda al día correcto
        $schedule = MaintenanceSchedule::find($request->schedule_id);
        $maintenance = $schedule->maintenance;
        $date = Carbon::parse($request->maintenance_date);

        // Verificar que esté dentro del rango de mantenimiento
        if ($date < $maintenance->start_date || $date > $maintenance->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha debe estar dentro del período del mantenimiento (' . 
                            $maintenance->start_date->format('d/m/Y') . ' - ' . 
                            $maintenance->end_date->format('d/m/Y') . ').'
            ], 422);
        }

        // Verificar que el día de la semana coincida
        $dayOfWeek = $date->dayOfWeek; // Carbon devuelve números: 0=Domingo, 1=Lunes, etc.
        $scheduleDayOfWeek = (int) $schedule->day_of_week;
        
        // Nombres para mostrar en el error
        $dayNames = [
            0 => 'Domingo',
            1 => 'Lunes', 
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado'
        ];

        \Log::info('Day validation:', [
            'date_selected' => $request->maintenance_date,
            'carbon_day_of_week' => $dayOfWeek,
            'schedule_day_of_week' => $scheduleDayOfWeek,
            'expected_day_name' => $dayNames[$scheduleDayOfWeek] ?? 'Desconocido',
            'actual_day_name' => $dayNames[$dayOfWeek] ?? 'Desconocido'
        ]);

        if ($dayOfWeek !== $scheduleDayOfWeek) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha seleccionada no corresponde al día de la semana del horario (' . 
                            ($dayNames[$scheduleDayOfWeek] ?? 'Desconocido') . '). ' .
                            'Fecha seleccionada es un ' . ($dayNames[$dayOfWeek] ?? 'Desconocido') . '.'
            ], 422);
        }

        $data = $request->except(['image']);

        // Manejar subida de imagen
        if ($request->hasFile('image')) {
            \Log::info('Processing image upload');
            $image = $request->file('image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('maintenance_activities', $filename, 'public');
            $data['image_url'] = $path;
            $data['image_path'] = $path; // Usar ambos campos por si acaso
            \Log::info('Image saved to: ' . $path);
        }

        \Log::info('Creating maintenance record with data:', $data);
        $activity = MaintenanceRecord::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Actividad registrada exitosamente.',
            'data' => $activity->load(['schedule.maintenance', 'schedule.vehicle'])
        ]);
    }

    /**
     * Display the specified activity
     */
    public function show(MaintenanceRecord $record)
    {
        return response()->json([
            'success' => true,
            'data' => $record->load(['schedule.maintenance', 'schedule.vehicle', 'schedule.driver'])
        ]);
    }

    /**
     * Update the specified activity
     */
    public function update(Request $request, MaintenanceRecord $record)
    {
        $validator = Validator::make($request->all(), [
            'maintenance_date' => 'required|date',
            'descripcion' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'maintenance_date.required' => 'La fecha es obligatoria.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede superar los 1000 caracteres.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'image.max' => 'La imagen no puede superar los 2MB.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validar fecha igual que en store
        $schedule = $record->schedule;
        $maintenance = $schedule->maintenance;
        $date = Carbon::parse($request->maintenance_date);

        if ($date < $maintenance->start_date || $date > $maintenance->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha debe estar dentro del período del mantenimiento (' . 
                            $maintenance->start_date->format('d/m/Y') . ' - ' . 
                            $maintenance->end_date->format('d/m/Y') . ').'
            ], 422);
        }

        // Verificar que el día de la semana coincida (igual que en store)
        $dayOfWeek = $date->dayOfWeek;
        $scheduleDayOfWeek = (int) $schedule->day_of_week;
        
        $dayNames = [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
        ];

        if ($dayOfWeek !== $scheduleDayOfWeek) {
            return response()->json([
                'success' => false,
                'message' => 'La fecha seleccionada no corresponde al día de la semana del horario (' . 
                            ($dayNames[$scheduleDayOfWeek] ?? 'Desconocido') . ').'
            ], 422);
        }

        $data = $request->except(['image']);

        // Manejar nueva imagen
        if ($request->hasFile('image')) {
            \Log::info('Updating image for record: ' . $record->id);
            // Eliminar imagen anterior si existe
            if ($record->image_url) {
                Storage::disk('public')->delete($record->image_url);
            }
            if ($record->image_path && $record->image_path !== $record->image_url) {
                Storage::disk('public')->delete($record->image_path);
            }

            $image = $request->file('image');
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('maintenance_activities', $filename, 'public');
            $data['image_url'] = $path;
            $data['image_path'] = $path;
            \Log::info('New image saved to: ' . $path);
        }

        \Log::info('Updating record with data:', $data);
        $record->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Actividad actualizada exitosamente.',
            'data' => $record->fresh(['schedule.maintenance', 'schedule.vehicle'])
        ]);
    }

    /**
     * Remove the specified activity
     */
    public function destroy(MaintenanceRecord $record)
    {
        \Log::info('Deleting maintenance record: ' . $record->id);
        
        // Eliminar imagen si existe
        if ($record->image_url) {
            Storage::disk('public')->delete($record->image_url);
            \Log::info('Deleted image: ' . $record->image_url);
        }
        if ($record->image_path && $record->image_path !== $record->image_url) {
            Storage::disk('public')->delete($record->image_path);
            \Log::info('Deleted image path: ' . $record->image_path);
        }
        
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Actividad eliminada exitosamente.'
        ]);
    }

    /**
     * Delete activity image
     */
    public function deleteImage(MaintenanceRecord $record)
    {
        if (!$record->image_url) {
            return response()->json([
                'success' => false,
                'message' => 'No hay imagen para eliminar.'
            ], 404);
        }

        Storage::disk('public')->delete($record->image_url);
        $record->update(['image_url' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Imagen eliminada exitosamente.'
        ]);
    }

    /**
     * Get valid dates for a schedule
     */
    public function getValidDates(Request $request, MaintenanceSchedule $schedule)
    {
        $maintenance = $schedule->maintenance;
        $dayOfWeek = $schedule->day_of_week;
        
        // Mapeo de días
        $dayMapping = [
            'LUNES' => Carbon::MONDAY,
            'MARTES' => Carbon::TUESDAY,
            'MIERCOLES' => Carbon::WEDNESDAY,
            'JUEVES' => Carbon::THURSDAY,
            'VIERNES' => Carbon::FRIDAY,
            'SABADO' => Carbon::SATURDAY
        ];

        $validDates = [];
        $startDate = $maintenance->start_date->copy();
        $endDate = $maintenance->end_date->copy();

        // Encontrar todas las fechas válidas dentro del rango
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if ($current->dayOfWeek === $dayMapping[$dayOfWeek]) {
                $validDates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        return response()->json([
            'success' => true,
            'data' => $validDates
        ]);
    }
}