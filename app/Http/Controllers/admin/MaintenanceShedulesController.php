<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceShedules;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Maintenances;
use App\Models\MaintenanceRecords;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class MaintenanceShedulesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($maintenance_id)
    {
        if(request()->ajax()){
            $shedules = MaintenanceShedules::where('maintenance_id', $maintenance_id)
                ->with(['vehicle', 'driver'])
                ->get();

            return DataTables::of($shedules)
                ->addColumn('vehicle', function($row){
                    return $row->vehicle ? $row->vehicle->name : '';
                })
                ->addColumn('responsable', function($row){
                    if ($row->driver) {
                        return trim(($row->driver->names ?? '') . ' ' . ($row->driver->lastnames ?? ''));
                    }
                    return '';
                })
                    ->addColumn('act', function($row){
                        $url = route('admin.maintenance_records.index', [
                            'maintenance' => $row->maintenance_id,
                            'schedule' => $row->id
                        ]);
                        return '<a href="'.$url.'" class="btn btn-light btn-sm" title="Fechas de ejecución">'
                            .'<i class="fas fa-tools fa-lg"></i>'
                            .'</a>';
                    })
                ->addColumn('edit', function($row){
                    return '<button class="btn btn-warning btn-sm btnEditar" id="'.$row->id.'"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn('delete', function($row){
                    return '<form action="'.route('admin.maintenance_shedules.destroy', [$row->maintenance_id, $row->id]).'" method="POST" class="frmDelete">'
                        .csrf_field().method_field('DELETE').
                        '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->rawColumns(['act','edit','delete'])
                ->make(true);
        }
        // Obtener el nombre del mantenimiento
        $maintenance = Maintenances::find($maintenance_id);
        return view('admin.examen03.maintenance_shedules.index', compact('maintenance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($maintenance_id)
    {
        $vehicles = Vehicle::all();
        $employees = Employee::all();
        return view('admin.examen03.maintenance_shedules.create', [
            'maintenance_id' => $maintenance_id,
            'vehicles' => $vehicles,
            'employees' => $employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $maintenance_id)
    {
        try {
            $request->validate([
                'day_of_week' => 'required',
                'vehicle_id' => 'required|exists:vehicles,id',
                'driver_id' => 'required|exists:employee,id',
                'maintenance_type' => 'required',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
            ], [
                'day_of_week.required' => 'El día de la semana es obligatorio.',
                'vehicle_id.required' => 'El vehículo es obligatorio.',
                'vehicle_id.exists' => 'El vehículo seleccionado no existe.',
                'driver_id.required' => 'El responsable es obligatorio.',
                'driver_id.exists' => 'El responsable seleccionado no existe.',
                'maintenance_type.required' => 'El tipo de mantenimiento es obligatorio.',
                'start_time.required' => 'La hora de inicio es obligatoria.',
                'end_time.required' => 'La hora de fin es obligatoria.',
                'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.'
            ]);

            // Validar solapamiento de horarios para el mismo vehículo, día y traslape de horas
            $overlap = MaintenanceShedules::where('maintenance_id', $maintenance_id)
                ->where('vehicle_id', $request->vehicle_id)
                ->where('day_of_week', $request->day_of_week)
                ->where(function($q) use ($request) {
                    $q->where(function($q2) use ($request) {
                        $q2->where('start_time', '<', $request->end_time)
                            ->where('end_time', '>', $request->start_time);
                    });
                })
                ->exists();
            if ($overlap) {
                return response()->json(['message' => 'Ya existe un horario para este vehículo, día y rango de horas en este mantenimiento.'], 422);
            }

            $data = $request->all();
            $data['maintenance_id'] = $maintenance_id;

            $schedule = MaintenanceShedules::create($data);

            // Generar automáticamente los días del mes según el día de la semana y el rango del mantenimiento
            $maintenance = Maintenances::findOrFail($maintenance_id);
            $start = Carbon::parse($maintenance->start_date);
            $end = Carbon::parse($maintenance->end_date);
            $dias = [
                'Lunes' => 1,
                'Martes' => 2,
                'Miércoles' => 3,
                'Miercoles' => 3,
                'Jueves' => 4,
                'Viernes' => 5,
                'Sábado' => 6,
                'Sabado' => 6,
                'Domingo' => 0
            ];
            $diaSemana = $request->day_of_week;
            $diaEsperado = $dias[$diaSemana] ?? null;
            if ($diaEsperado !== null) {
                $fecha = $start->copy();
                while ($fecha <= $end) {
                    if ($fecha->dayOfWeek === $diaEsperado) {
                        MaintenanceRecords::create([
                            'schedule_id' => $schedule->id,
                            'maintenance_date' => $fecha->toDateString(),
                            'descripcion' => 'Añada la observación del mantenimiento.',
                            'image_url' => ''
                        ]);
                    }
                    $fecha->addDay();
                }
            }

            return response()->json(['message' => 'Horario registrado correctamente']);
        } catch (ValidationException $e) {
            $error = $e->validator->errors()->first();
            return response()->json(['message' => $error], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al registrar el horario.'], 500);
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
    public function edit($maintenance_id, $id)
    {
        $model = MaintenanceShedules::findOrFail($id);
        $vehicles = Vehicle::all();
        $employees = Employee::all();
        return view('admin.examen03.maintenance_shedules.edit', [
            'maintenance_id' => $maintenance_id,
            'vehicles' => $vehicles,
            'employees' => $employees,
            'model' => $model
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $maintenance_id, $id)
    {
        try {
            $request->validate([
                'day_of_week' => 'required',
                'vehicle_id' => 'required|exists:vehicles,id',
                'driver_id' => 'required|exists:employee,id',
                'maintenance_type' => 'required',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
            ], [
                'day_of_week.required' => 'El día de la semana es obligatorio.',
                'vehicle_id.required' => 'El vehículo es obligatorio.',
                'vehicle_id.exists' => 'El vehículo seleccionado no existe.',
                'driver_id.required' => 'El responsable es obligatorio.',
                'driver_id.exists' => 'El responsable seleccionado no existe.',
                'maintenance_type.required' => 'El tipo de mantenimiento es obligatorio.',
                'start_time.required' => 'La hora de inicio es obligatoria.',
                'end_time.required' => 'La hora de fin es obligatoria.',
                'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.'
            ]);

            $model = MaintenanceShedules::findOrFail($id);
            // Validar que no se modifique el día de la semana
            if ($request->day_of_week !== $model->day_of_week) {
                return response()->json(['message' => 'No está permitido modificar el día del mantenimiento.'], 422);
            }

            // Validar solapamiento de horarios para el mismo vehículo, día y traslape de horas (excluyendo el actual)
            $overlap = MaintenanceShedules::where('maintenance_id', $maintenance_id)
                ->where('vehicle_id', $request->vehicle_id)
                ->where('day_of_week', $request->day_of_week)
                ->where('id', '!=', $id)
                ->where(function($q) use ($request) {
                    $q->where(function($q2) use ($request) {
                        $q2->where('start_time', '<', $request->end_time)
                            ->where('end_time', '>', $request->start_time);
                    });
                })
                ->exists();
            if ($overlap) {
                return response()->json(['message' => 'Ya existe un horario para este vehículo, día y rango de horas en este mantenimiento.'], 422);
            }

            $data = $request->all();
            $data['maintenance_id'] = $maintenance_id;
            $model->update($data);

            return response()->json(['message' => 'Horario actualizado correctamente']);
        } catch (ValidationException $e) {
            $error = $e->validator->errors()->first();
            return response()->json(['message' => $error], 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al actualizar el horario.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($maintenance_id, $id)
    {
        try {
            $model = MaintenanceShedules::findOrFail($id);
            // Eliminar en cascada los registros de ejecución asociados
            MaintenanceRecords::where('schedule_id', $model->id)->delete();
            $model->delete();
            return response()->json(['message' => 'Horario eliminado correctamente']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar el horario: ' . $th->getMessage()], 500);
        }
    }
}
