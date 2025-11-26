<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class MaintenanceScheduleController extends Controller
{
    public function index(Maintenance $maintenance)
    {
        $schedules = $maintenance->schedules()
            ->with(['vehicle', 'driver', 'records'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $records = $maintenance->records()
            ->with(['schedule.vehicle', 'schedule.driver'])
            ->orderBy('maintenance_date')
            ->get();

        return view('maintenanceschedules.index', compact('maintenance', 'schedules', 'records'));
    }

    public function create(Maintenance $maintenance)
    {
        $vehicles = Vehicle::orderBy('plate')->get();

        $drivers = Employee::where('status', 1)
            ->where('type_id', 1)
            ->orderBy('lastnames')
            ->get();

        $daysOfWeek = [
            'lunes'     => 'Lunes',
            'martes'    => 'Martes',
            'miércoles' => 'Miércoles',
            'miercoles' => 'Miércoles',
            'jueves'    => 'Jueves',
            'viernes'   => 'Viernes',
            'sábado'    => 'Sábado',
            'sabado'    => 'Sábado',
            'domingo'   => 'Domingo',
        ];

        return view('maintenanceschedules.create', compact(
            'maintenance',
            'vehicles',
            'drivers',
            'daysOfWeek'
        ));
    }

    public function store(Request $request, Maintenance $maintenance)
    {
        $data = $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'driver_id'        => 'required|exists:employee,id',
            'day_of_week'      => 'required|string',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'maintenance_type' => 'required|string|max:255',
        ]);

        $data['maintenance_id'] = $maintenance->id;

        // VALIDAR CRUCE
        $overlapExists = MaintenanceSchedule::where('maintenance_id', $maintenance->id)
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function ($q) use ($data) {
                $q->where('start_time', '<', $data['end_time'])
                  ->where('end_time',   '>', $data['start_time']);
            })
            ->exists();

        if ($overlapExists) {
            return back()
                ->withErrors([
                    'start_time' => 'Ya existe un horario para este vehículo en ese día que se cruza con '
                    . $data['start_time'] . ' - ' . $data['end_time'],
                ])
                ->withInput();
        }

        $schedule = MaintenanceSchedule::create($data);

        $this->generateRecordsForSchedule($maintenance, $schedule);

        return redirect()
            ->route('admin.maintenances.schedules.index', $maintenance)
            ->with('success', 'Horario creado y registros generados correctamente.');
    }

    public function edit(Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        if ($schedule->maintenance_id !== $maintenance->id) {
            abort(404);
        }

        $vehicles = Vehicle::orderBy('plate')->get();

        $drivers = Employee::where('status', 1)
            ->where('type_id', 1)
            ->orderBy('lastnames')
            ->get();

        $daysOfWeek = [
            'lunes'     => 'Lunes',
            'martes'    => 'Martes',
            'miércoles' => 'Miércoles',
            'miercoles' => 'Miércoles',
            'jueves'    => 'Jueves',
            'viernes'   => 'Viernes',
            'sábado'    => 'Sábado',
            'sabado'    => 'Sábado',
            'domingo'   => 'Domingo',
        ];

        return view('maintenanceschedules.edit', compact(
            'maintenance',
            'schedule',
            'vehicles',
            'drivers',
            'daysOfWeek'
        ));
    }

    public function update(Request $request, Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        if ($schedule->maintenance_id !== $maintenance->id) {
            abort(404);
        }

        $data = $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'driver_id'        => 'required|exists:employee,id',
            'day_of_week'      => 'required|string',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'maintenance_type' => 'required|string|max:255',
        ]);

        // VALIDAR CRUCE EN UPDATE
        $overlapExists = MaintenanceSchedule::where('maintenance_id', $maintenance->id)
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($data) {
                $q->where('start_time', '<', $data['end_time'])
                  ->where('end_time',   '>', $data['start_time']);
            })
            ->exists();

        if ($overlapExists) {
            return back()
                ->withErrors([
                    'start_time' => 'Ya existe un horario para este vehículo en ese día que se cruza con '
                    . $data['start_time'] . ' - ' . $data['end_time'],
                ])
                ->withInput();
        }

        $schedule->update($data);

        $schedule->records()->delete();
        $this->generateRecordsForSchedule($maintenance, $schedule);

        return redirect()
            ->route('admin.maintenances.schedules.index', $maintenance)
            ->with('success', 'Horario actualizado.');
    }

    public function destroy(Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        if ($schedule->maintenance_id !== $maintenance->id) {
            abort(404);
        }

        $schedule->records()->delete();
        $schedule->delete();

        return back()->with('success', 'Horario eliminado.');
    }


    /** ===========================================
     *  GENERADOR DE REGISTROS POR HORARIO
     * ============================================ */
    private function generateRecordsForSchedule(Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        if (!$maintenance->start_date || !$maintenance->end_date) {
            return;
        }

        $start = Carbon::parse($maintenance->start_date)->startOfDay();
        $end   = Carbon::parse($maintenance->end_date)->endOfDay();

        $dowMap = [
            'domingo'   => 0,
            'lunes'     => 1,
            'martes'    => 2,
            'miércoles' => 3,
            'miercoles' => 3,
            'jueves'    => 4,
            'viernes'   => 5,
            'sábado'    => 6,
            'sabado'    => 6,
        ];

        $key = mb_strtolower(trim($schedule->day_of_week), 'UTF-8');

        if (!isset($dowMap[$key])) return;

        $targetDow = $dowMap[$key];

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->dayOfWeek === $targetDow) {
                MaintenanceRecord::create([
                    'schedule_id'      => $schedule->id,
                    'maintenance_date' => $date->toDateString(),
                    'descripcion'      => '',
                    'image_url'        => '',
                    'estado'           => 'no realizado', 
                ]);
            }
        }
    }
}
