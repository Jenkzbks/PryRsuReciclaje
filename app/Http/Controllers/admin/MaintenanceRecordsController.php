<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceRecords;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Maintenances;
use App\Models\MaintenanceShedules;
use Carbon\Carbon;

class MaintenanceRecordsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($maintenance_id, $schedule_id)
    {
        $maintenance = Maintenances::findOrFail($maintenance_id);
        $schedule = MaintenanceShedules::findOrFail($schedule_id);
        $vehicle = $schedule->vehicle;

        if(request()->ajax()){
            $records = MaintenanceRecords::where('schedule_id', $schedule_id)->get();
            return DataTables::of($records)
                ->addColumn('edit', function($row) use ($maintenance_id, $schedule_id) {
                    return '<button class="btn btn-warning btn-sm btnEditar" id="'.$row->id.'"><i class="fas fa-pen"></i></button>';
                })
                ->addColumn('delete', function($row) use ($maintenance_id, $schedule_id) {
                    return '<form action="'.route('admin.maintenance_records.destroy', [$maintenance_id, $schedule_id, $row->id]).'" method="POST" class="frmDelete">'
                        .csrf_field().method_field('DELETE').
                        '<button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></form>';
                })
                ->editColumn('image_url', function($row) {
                    return $row->image_url ? asset($row->image_url) : asset('storage/maintenance_records/noimage.jpg');
                })
                ->rawColumns(['edit','delete'])
                ->make(true);
        }
        return view('admin.examen03.maintenance_records.index', compact('maintenance', 'schedule', 'vehicle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($maintenance_id, $schedule_id)
    {
        return view('admin.examen03.maintenance_records.create', [
            'maintenance_id' => $maintenance_id,
            'schedule_id' => $schedule_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $maintenance_id, $schedule_id)
    {
        $request->validate([
            'maintenance_date' => 'required|date',
            'descripcion' => 'required|string',
            'image_url' => 'nullable|image|max:2048',
            'estado' => 'required|boolean',
        ]);

         // Validar que no exista ya un registro para la misma fecha y horario
            $existe = MaintenanceRecords::where('schedule_id', $schedule_id)
                ->where('maintenance_date', $request->maintenance_date)
                ->exists();
            if ($existe) {
                return response()->json(['message' => 'Ya existe un registro para esta fecha en este horario.'], 422);
            }

        // Validar que la fecha esté dentro del rango del mantenimiento y coincida con el día del horario
        $schedule = MaintenanceShedules::findOrFail($schedule_id);
        $maintenance = Maintenances::findOrFail($maintenance_id);
        $date = Carbon::parse($request->maintenance_date);
        $start = Carbon::parse($maintenance->start_date);
        $end = Carbon::parse($maintenance->end_date);
        $dias = [
            'LUNES' => 1,
            'MARTES' => 2,
            'MIERCOLES' => 3,
            'MIÉRCOLES' => 3,
            'JUEVES' => 4,
            'VIERNES' => 5,
            'SABADO' => 6,
            'SÁBADO' => 6,
            'DOMINGO' => 0
        ];
        $diaSemana = strtoupper($schedule->day_of_week);
        $diaEsperado = $dias[$diaSemana] ?? null;
        if ($date->lt($start) || $date->gt($end)) {
            return response()->json(['message' => 'La fecha debe estar dentro del rango del mantenimiento.'], 422);
        }
        if ($diaEsperado === null || $date->dayOfWeek !== $diaEsperado) {
            return response()->json(['message' => 'La fecha no corresponde al día programado en el horario.'], 422);
        }

        $data = $request->only(['maintenance_date', 'descripcion', 'estado']);
        $data['schedule_id'] = $schedule_id;

        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $path = $file->store('maintenance_records', 'public');
            $data['image_url'] = 'storage/' . $path;
        }

        MaintenanceRecords::create($data);
        return response()->json(['message' => 'Fecha de ejecución registrada correctamente']);
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
    public function edit($maintenance_id, $schedule_id, $id)
    {
        $model = MaintenanceRecords::findOrFail($id);
        return view('admin.examen03.maintenance_records.edit', [
            'maintenance_id' => $maintenance_id,
            'schedule_id' => $schedule_id,
            'model' => $model
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $maintenance_id, $schedule_id, $id)
    {
        $request->validate([
            'maintenance_date' => 'required|date',
            'descripcion' => 'required|string',
            'image_url' => 'nullable|image|max:2048',
            'estado' => 'required|boolean',
        ]);

        // Validar que no exista ya un registro para la misma fecha y horario (excluyendo el actual)
            $existe = MaintenanceRecords::where('schedule_id', $schedule_id)
                ->where('maintenance_date', $request->maintenance_date)
                ->where('id', '!=', $id)
                ->exists();
            if ($existe) {
                return response()->json(['message' => 'Ya existe un registro para esta fecha en este horario.'], 422);
            }
        // Validar que la fecha esté dentro del rango del mantenimiento y coincida con el día del horario
        $schedule = MaintenanceShedules::findOrFail($schedule_id);
        $maintenance = Maintenances::findOrFail($maintenance_id);
        $date = Carbon::parse($request->maintenance_date);
        $start = Carbon::parse($maintenance->start_date);
        $end = Carbon::parse($maintenance->end_date);
        $dias = [
            'LUNES' => 1,
            'MARTES' => 2,
            'MIERCOLES' => 3,
            'MIÉRCOLES' => 3,
            'JUEVES' => 4,
            'VIERNES' => 5,
            'SABADO' => 6,
            'SÁBADO' => 6,
            'DOMINGO' => 0
        ];
        $diaSemana = strtoupper($schedule->day_of_week);
        $diaEsperado = $dias[$diaSemana] ?? null;
        if ($date->lt($start) || $date->gt($end)) {
            return response()->json(['message' => 'La fecha debe estar dentro del rango del mantenimiento.'], 422);
        }
        if ($diaEsperado === null || $date->dayOfWeek !== $diaEsperado) {
            return response()->json(['message' => 'La fecha no corresponde al día programado en el horario.'], 422);
        }

        $model = MaintenanceRecords::findOrFail($id);
        $data = $request->only(['maintenance_date', 'descripcion', 'estado']);

        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $path = $file->store('maintenance_records', 'public');
            $data['image_url'] = 'storage/' . $path;
        }

        $model->update($data);
        return response()->json(['message' => 'Fecha de ejecución actualizada correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($maintenance_id, $schedule_id, $id)
    {
        try {
            $model = MaintenanceRecords::findOrFail($id);
            $model->delete();
            return response()->json(['message' => 'Fecha de ejecución eliminada correctamente']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Cambia el estado (realizado/no realizado) de un registro por AJAX.
     */
    public function toggleEstado(Request $request, $maintenance_id, $schedule_id, $id)
    {
        $record = MaintenanceRecords::findOrFail($id);
        $record->estado = $request->input('estado') ? 1 : 0;
        $record->save();
        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
}
