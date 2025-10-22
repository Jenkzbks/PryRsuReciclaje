<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Http\Requests\AttendanceLoginRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['employee']);

        // Búsqueda por empleado
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filtro por empleado específico
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filtro por tipo (entrada/salida)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por fecha
        if ($request->filled('date')) {
            $query->whereDate('datetime', $request->date);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('datetime', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('datetime', '<=', $request->date_to);
        }

        // Filtro por mes y año
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('datetime', $request->month)
                  ->whereYear('datetime', $request->year);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'datetime');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['datetime', 'type', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('datetime', 'desc');
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Para la vista
        $employees = Employee::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('attendances.index', compact('attendances', 'employees'));
    }

    /**
     * Página de login para empleados (registrar entrada/salida)
     */
    public function loginPage()
    {
        return view('attendances.login');
    }

    /**
     * Procesar login de empleado para registro de asistencia
     */
    public function processLogin(AttendanceLoginRequest $request)
    {
        $data = $request->validated();
        
        // Buscar empleado por DNI
        $employee = Employee::where('dni', $data['dni'])
            ->where('status', 'active')
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'DNI no encontrado o empleado inactivo.'
            ], 422);
        }

        // Verificar contraseña (simple, usando DNI como contraseña por defecto)
        if (!Hash::check($data['password'], $employee->password ?? Hash::make($employee->dni))) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta.'
            ], 422);
        }

        // Determinar tipo de registro (entrada o salida)
        $today = Carbon::today();
        $lastEntry = Attendance::where('employee_id', $employee->id)
            ->whereDate('datetime', $today)
            ->where('type', Attendance::TYPE_ENTRY)
            ->first();

        $lastExit = Attendance::where('employee_id', $employee->id)
            ->whereDate('datetime', $today)
            ->where('type', Attendance::TYPE_EXIT)
            ->first();

        // Lógica de entrada/salida
        if (!$lastEntry) {
            // No hay entrada, registrar entrada
            $type = Attendance::TYPE_ENTRY;
            $message = 'Entrada registrada exitosamente.';
        } elseif (!$lastExit) {
            // Hay entrada pero no salida, registrar salida
            $type = Attendance::TYPE_EXIT;
            $message = 'Salida registrada exitosamente.';
        } else {
            // Ya hay entrada y salida del día
            return response()->json([
                'success' => false,
                'message' => 'Ya se registró entrada y salida para el día de hoy.'
            ], 422);
        }

        // Crear registro de asistencia
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'datetime' => now(),
            'type' => $type
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'employee' => $employee->name . ' ' . $employee->lastname,
            'type' => $type,
            'datetime' => $attendance->datetime->format('d/m/Y H:i:s')
        ]);
    }

    /**
     * Registrar entrada manual (para administradores)
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'datetime' => 'nullable|date'
        ]);

        $employee = Employee::find($request->employee_id);
        $datetime = $request->datetime ? Carbon::parse($request->datetime) : now();

        // Verificar que no tenga entrada del mismo día
        $existingEntry = Attendance::where('employee_id', $employee->id)
            ->whereDate('datetime', $datetime->toDateString())
            ->where('type', Attendance::TYPE_ENTRY)
            ->first();

        if ($existingEntry) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado ya tiene una entrada registrada para este día.'
            ], 422);
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'datetime' => $datetime,
            'type' => Attendance::TYPE_ENTRY
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entrada registrada exitosamente.',
            'attendance' => $attendance
        ]);
    }

    /**
     * Registrar salida manual (para administradores)
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'datetime' => 'nullable|date'
        ]);

        $employee = Employee::find($request->employee_id);
        $datetime = $request->datetime ? Carbon::parse($request->datetime) : now();

        // Verificar que tenga entrada del mismo día
        $entry = Attendance::where('employee_id', $employee->id)
            ->whereDate('datetime', $datetime->toDateString())
            ->where('type', Attendance::TYPE_ENTRY)
            ->first();

        if (!$entry) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado no tiene una entrada registrada para este día.'
            ], 422);
        }

        // Verificar que no tenga salida del mismo día
        $existingExit = Attendance::where('employee_id', $employee->id)
            ->whereDate('datetime', $datetime->toDateString())
            ->where('type', Attendance::TYPE_EXIT)
            ->first();

        if ($existingExit) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado ya tiene una salida registrada para este día.'
            ], 422);
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'datetime' => $datetime,
            'type' => Attendance::TYPE_EXIT
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salida registrada exitosamente.',
            'attendance' => $attendance
        ]);
    }

    /**
     * Corregir asistencia (editar fecha/hora)
     */
    public function correct(Attendance $attendance, Request $request)
    {
        $request->validate([
            'datetime' => 'required|date',
            'reason' => 'nullable|string|max:500'
        ]);

        // Solo permitir corrección de asistencias de los últimos 3 días
        if ($attendance->datetime < Carbon::now()->subDays(3)) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden corregir asistencias de los últimos 3 días.'
            ], 422);
        }

        $attendance->update([
            'datetime' => $request->datetime,
            'corrected' => true,
            'correction_reason' => $request->reason,
            'corrected_by' => auth()->id(),
            'corrected_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asistencia corregida exitosamente.'
        ]);
    }

    /**
     * Eliminar registro de asistencia
     */
    public function destroy(Attendance $attendance)
    {
        // Solo permitir eliminar asistencias del día actual
        if (!$attendance->datetime->isToday()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden eliminar asistencias del día actual.'
            ], 422);
        }

        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de asistencia eliminado exitosamente.'
        ]);
    }

    /**
     * Dashboard de asistencias del día
     */
    public function dashboard()
    {
        $today = Carbon::today();
        
        // Empleados que marcaron entrada hoy
        $entriesToday = Attendance::with(['employee'])
            ->whereDate('datetime', $today)
            ->where('type', Attendance::TYPE_ENTRY)
            ->orderBy('datetime')
            ->get();

        // Empleados que marcaron salida hoy
        $exitsToday = Attendance::with(['employee'])
            ->whereDate('datetime', $today)
            ->where('type', Attendance::TYPE_EXIT)
            ->orderBy('datetime')
            ->get();

        // Empleados activos sin entrada
        $employeesWithoutEntry = Employee::where('status', 'active')
            ->whereNotIn('id', $entriesToday->pluck('employee_id'))
            ->orderBy('name')
            ->get();

        // Empleados con entrada pero sin salida
        $employeesWithoutExit = $entriesToday->whereNotIn('employee_id', $exitsToday->pluck('employee_id'));

        $statistics = [
            'total_active_employees' => Employee::where('status', 'active')->count(),
            'entries_today' => $entriesToday->count(),
            'exits_today' => $exitsToday->count(),
            'without_entry' => $employeesWithoutEntry->count(),
            'without_exit' => $employeesWithoutExit->count(),
            'attendance_rate' => Employee::where('status', 'active')->count() > 0 
                ? round(($entriesToday->count() / Employee::where('status', 'active')->count()) * 100, 2)
                : 0
        ];

        return view('attendances.dashboard', compact(
            'entriesToday',
            'exitsToday', 
            'employeesWithoutEntry',
            'employeesWithoutExit',
            'statistics'
        ));
    }

    /**
     * Reporte de asistencias por empleado
     */
    public function employeeReport(Employee $employee, Request $request)
    {
        $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2030'
        ]);

        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('datetime', $month)
            ->whereYear('datetime', $year)
            ->orderBy('datetime')
            ->get()
            ->groupBy(function($attendance) {
                return $attendance->datetime->format('Y-m-d');
            });

        $workingDays = Carbon::create($year, $month)->daysInMonth;
        $presentDays = $attendances->count();
        $absentDays = $workingDays - $presentDays;

        // Calcular horas trabajadas
        $totalHours = 0;
        foreach ($attendances as $date => $dayAttendances) {
            $entry = $dayAttendances->where('type', Attendance::TYPE_ENTRY)->first();
            $exit = $dayAttendances->where('type', Attendance::TYPE_EXIT)->first();
            
            if ($entry && $exit) {
                $totalHours += $entry->datetime->diffInHours($exit->datetime);
            }
        }

        $report = [
            'employee' => $employee,
            'period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::create($year, $month)->translatedFormat('F Y')
            ],
            'statistics' => [
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'attendance_rate' => round(($presentDays / $workingDays) * 100, 2),
                'total_hours' => $totalHours,
                'average_hours_per_day' => $presentDays > 0 ? round($totalHours / $presentDays, 2) : 0
            ],
            'attendances' => $attendances
        ];

        return response()->json($report);
    }

    /**
     * Reporte general de asistencias
     */
    public function generalReport(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth());

        $query = Employee::where('status', 'active');

        if ($request->filled('department_id')) {
            $query->where('departament_id', $request->department_id);
        }

        $employees = $query->with(['attendances' => function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('datetime', [$dateFrom, $dateTo])
              ->orderBy('datetime');
        }])->get();

        $report = $employees->map(function($employee) use ($dateFrom, $dateTo) {
            $attendances = $employee->attendances->groupBy(function($attendance) {
                return $attendance->datetime->format('Y-m-d');
            });

            $workingDays = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
            $presentDays = $attendances->count();

            // Calcular horas trabajadas
            $totalHours = 0;
            foreach ($attendances as $dayAttendances) {
                $entry = $dayAttendances->where('type', Attendance::TYPE_ENTRY)->first();
                $exit = $dayAttendances->where('type', Attendance::TYPE_EXIT)->first();
                
                if ($entry && $exit) {
                    $totalHours += $entry->datetime->diffInHours($exit->datetime);
                }
            }

            return [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name . ' ' . $employee->lastname,
                    'dni' => $employee->dni,
                    'employee_type' => $employee->employeeType?->name
                ],
                'statistics' => [
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'absent_days' => $workingDays - $presentDays,
                    'attendance_rate' => round(($presentDays / $workingDays) * 100, 2),
                    'total_hours' => $totalHours
                ]
            ];
        });

        return response()->json([
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'employees' => $report,
            'summary' => [
                'total_employees' => $report->count(),
                'average_attendance_rate' => $report->avg('statistics.attendance_rate'),
                'total_hours' => $report->sum('statistics.total_hours')
            ]
        ]);
    }

    /**
     * Obtener estado actual de asistencias
     */
    public function getCurrentStatus()
    {
        $today = Carbon::today();
        
        $status = Employee::where('status', 'active')
            ->with(['attendances' => function($query) use ($today) {
                $query->whereDate('datetime', $today);
            }])
            ->get()
            ->map(function($employee) {
                $todayAttendances = $employee->attendances;
                $hasEntry = $todayAttendances->where('type', Attendance::TYPE_ENTRY)->isNotEmpty();
                $hasExit = $todayAttendances->where('type', Attendance::TYPE_EXIT)->isNotEmpty();

                return [
                    'employee' => [
                        'id' => $employee->id,
                        'name' => $employee->name . ' ' . $employee->lastname,
                        'dni' => $employee->dni
                    ],
                    'status' => $hasEntry && $hasExit ? 'completed' : ($hasEntry ? 'present' : 'absent'),
                    'entry_time' => $hasEntry ? $todayAttendances->where('type', Attendance::TYPE_ENTRY)->first()->datetime->format('H:i') : null,
                    'exit_time' => $hasExit ? $todayAttendances->where('type', Attendance::TYPE_EXIT)->first()->datetime->format('H:i') : null
                ];
            });

        return response()->json($status);
    }
}