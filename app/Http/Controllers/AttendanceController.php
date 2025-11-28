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
                $q->where('names', 'like', "%{$search}%")
                  ->orWhere('lastnames', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%");
            });
        }

        // Filtro por empleado específico
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por fecha
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filtro por mes y año
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('date', $request->month)
                  ->whereYear('date', $request->year);
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortField, ['id', 'date', 'check_in', 'check_out', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Para la vista
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();

        return view('personnel.attendances.index', compact('attendances', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();

        return view('personnel.attendances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,late,absent,half_day',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string|max:500'
        ]);

        // Crear el registro de asistencia
        $attendance = new Attendance();
        $attendance->employee_id = $request->employee_id;
        $attendance->date = $request->date;
        $attendance->period = 1;
        $attendance->check_in = $request->check_in ? Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_in) : null;
        $attendance->check_out = $request->check_out ? Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_out) : null;
        $attendance->status = $request->status;
        $attendance->notes = $request->notes;

        // Calcular horas trabajadas si no se proporcionó
        if (!$request->hours_worked && $attendance->check_in && $attendance->check_out) {
            $attendance->hours_worked = $attendance->check_in->diffInHours($attendance->check_out);
        } else {
            $attendance->hours_worked = $request->hours_worked ?? null;
        }

        $attendance->save();

        return redirect()->route('personnel.attendances.index')
            ->with('success', 'Asistencia registrada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load('employee');
        return view('personnel.attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $employees = Employee::where('status', 1)
            ->orderBy('names')
            ->get();

        $attendance->load('employee');
        return view('personnel.attendances.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,late,absent,half_day',
            'hours_worked' => 'nullable|numeric|min:0|max:24',
            'notes' => 'nullable|string|max:500'
        ]);

        // Actualizar el registro de asistencia
        $attendance->employee_id = $request->employee_id;
        $attendance->date = $request->date;
        $attendance->check_in = $request->check_in ? Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_in) : null;
        $attendance->check_out = $request->check_out ? Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_out) : null;
        $attendance->status = $request->status;
        $attendance->notes = $request->notes;

        // Calcular horas trabajadas si no se proporcionó
        if (!$request->hours_worked && $attendance->check_in && $attendance->check_out) {
            $attendance->hours_worked = $attendance->check_in->diffInHours($attendance->check_out);
        } else {
            $attendance->hours_worked = $request->hours_worked ?? 0;
        }

        $attendance->save();

        return redirect()->route('personnel.attendances.index')
            ->with('success', 'Asistencia actualizada exitosamente.');
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
        try {
            // Log temporal para debug
            \Log::info('Kiosco login attempt', [
                'dni' => $request->get('dni'),
                'password_provided' => !empty($request->get('password')),
                'password_length' => strlen($request->get('password', '')),
                'all_data' => $request->all(),
                'request_method' => $request->method(),
                'request_url' => $request->url()
            ]);

            $data = $request->validated();
            \Log::info('Validation passed', ['validated_data' => $data]);
        
        // Buscar empleado por DNI
        $employee = Employee::where('dni', $data['dni'])
            ->where('status', 1)
            ->first();

        if (!$employee) {
            \Log::warning('Employee not found', ['dni' => $data['dni']]);
            return response()->json([
                'success' => false,
                'message' => 'DNI no encontrado o empleado inactivo.'
            ], 422);
        }

        \Log::info('Employee found', [
            'employee_id' => $employee->id,
            'employee_dni' => $employee->dni,
            'has_password_in_db' => !empty($employee->password)
        ]);

        // Verificar contraseña
        $isValidPassword = false;
        
        if ($employee->password) {
            // Si tiene contraseña configurada, verificarla
            $isValidPassword = Hash::check($data['password'], $employee->password);
            \Log::info('Hash check result', ['valid' => $isValidPassword]);
        } else {
            // Si no tiene contraseña, usar el DNI como contraseña por defecto
            $isValidPassword = ($data['password'] === $employee->dni);
            \Log::info('DNI password check', [
                'input_password' => $data['password'],
                'employee_dni' => $employee->dni,
                'valid' => $isValidPassword
            ]);
            
            // Si la contraseña es correcta, guardarla en la base de datos
            if ($isValidPassword) {
                $employee->update(['password' => Hash::make($employee->dni)]);
                \Log::info('Password saved to database');
            }
        }
        
        if (!$isValidPassword) {
            \Log::warning('Invalid password');
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta.'
            ], 422);
        }

        // Determinar tipo de registro (entrada o salida)
        $today = Carbon::today();
        $lastAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        \Log::info('Attendance check', [
            'employee_id' => $employee->id,
            'today' => $today->format('Y-m-d'),
            'has_attendance_today' => !!$lastAttendance,
            'has_check_out' => $lastAttendance ? !!$lastAttendance->check_out : false
        ]);

        $isEntry = false; // Variable para determinar si es entrada o salida

        // Lógica de entrada/salida
        if (!$lastAttendance) {
            // No hay registro del día, crear entrada
            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'check_in' => now(),
                'status' => 'present'
            ]);
            $message = 'Entrada registrada exitosamente.';
            $isEntry = true;
            \Log::info('Entry created', ['attendance_id' => $attendance->id]);
        } elseif ($lastAttendance && !$lastAttendance->check_out) {
            // Hay entrada pero no salida, registrar salida
            $lastAttendance->update([
                'check_out' => now(),
                'hours_worked' => Carbon::parse($lastAttendance->check_in)->diffInHours(now())
            ]);
            $message = 'Salida registrada exitosamente.';
            $isEntry = false;
            \Log::info('Exit registered', ['attendance_id' => $lastAttendance->id]);
        } else {
            // Ya hay entrada y salida del día
            \Log::warning('Already has complete attendance', ['attendance_id' => $lastAttendance->id]);
            return response()->json([
                'success' => false,
                'message' => 'Ya se registró entrada y salida para el día de hoy.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'employee' => $employee->names . ' ' . $employee->lastnames,
            'type' => $isEntry ? 'entry' : 'exit',
            'datetime' => now()->format('d/m/Y H:i:s')
        ]);
        
        } catch (\Exception $e) {
            \Log::error('Kiosco login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor.'
            ], 500);
        }
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
            ->whereDate('date', $datetime->toDateString())
            ->first();

        if ($existingEntry) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado ya tiene una asistencia registrada para este día.'
            ], 422);
        }

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => $datetime->toDateString(),
            'check_in' => $datetime,
            'status' => 'present'
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
            'employee_id' => 'required|exists:employees,id',
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
        try {
            $today = Carbon::today();
            
            // Obtener todas las asistencias de hoy para el dashboard
            $attendances = Attendance::with(['employee'])
                ->whereDate('date', $today)
                ->get();

            // También obtener asistencias de la semana para gráficos
            $weekAgo = $today->copy()->subDays(7);
            $weeklyAttendances = Attendance::with(['employee'])
                ->whereDate('date', '>=', $weekAgo)
                ->whereDate('date', '<=', $today)
                ->get();

            // Empleados activos
            $employees = Employee::where('status', 1)->get();
            
            // Variables que espera la vista
            $entriesToday = $attendances->whereNotNull('check_in');
            $exitsToday = $attendances->whereNotNull('check_out');
            
            // Empleados que han registrado entrada hoy
            $employeesWithEntry = $attendances->pluck('employee_id')->unique();
            
            // Empleados sin entrada hoy
            $employeesWithoutEntry = $employees->whereNotIn('id', $employeesWithEntry);
            
            // Empleados sin salida (que tienen entrada pero no salida)
            $employeesWithoutExit = $attendances->whereNull('check_out');
            
            // Estadísticas para el dashboard
            $statistics = [
                'entries_today' => $entriesToday->count(),
                'exits_today' => $exitsToday->count(),
                'without_entry' => $employeesWithoutEntry->count(),
                'attendance_rate' => $employees->count() > 0 
                    ? round(($attendances->count() / $employees->count()) * 100, 2)
                    : 0,
                'total_active_employees' => $employees->count(),
                'punctuality_rate' => $attendances->count() > 0
                    ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 2)
                    : 0
            ];
            
            // Estadísticas del día
            $todayStats = [
                'present' => $attendances->where('status', 'present')->count(),
                'on_time' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'half_day' => $attendances->where('status', 'half_day')->count(),
            ];

            return view('personnel.attendances.dashboard', compact(
                'attendances', 
                'todayStats', 
                'statistics', 
                'employees', 
                'weeklyAttendances',
                'employeesWithoutEntry',
                'employeesWithoutExit',
                'entriesToday',
                'exitsToday'
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

        $query = Employee::where('status', 1);

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
        
        $status = Employee::where('status', 1)
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