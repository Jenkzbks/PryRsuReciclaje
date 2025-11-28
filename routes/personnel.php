<?php

/*
|--------------------------------------------------------------------------
| Personal Management Module Routes
|--------------------------------------------------------------------------
|
| Aquí se definen todas las rutas específicas del módulo de Gestión de Personal.
| Estas rutas están organizadas por funcionalidad y incluyen tanto endpoints
| web como API para una gestión completa del personal.
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\AttendanceController;

// ===================================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// ===================================

// Kiosco de asistencias - acceso público para empleados
Route::prefix('attendance-kiosk')->name('attendance-kiosk.')->group(function () {
    Route::get('/', [AttendanceController::class, 'loginPage'])->name('index');
    Route::post('login', [AttendanceController::class, 'processLogin'])->name('login');
});

// ===================================
// RUTAS PROTEGIDAS (AUTENTICACIÓN REQUERIDA)
// ===================================

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // ===== DASHBOARD PRINCIPAL DEL MÓDULO =====
    Route::get('/personnel', function () {
        return view('personnel.dashboard');
    })->name('personnel.dashboard');

    // ===== EMPLEADOS =====
    Route::resource('personnel/employees', EmployeeController::class, [
        'as' => 'personnel'
    ]);

    // Rutas especiales para empleados
    Route::prefix('personnel/employees')->name('personnel.employees.')->group(function () {
        // Gestión de estado
        Route::patch('{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])
            ->name('toggle-status');
        
        // Gestión de fotos
        Route::delete('{employee}/remove-photo', [EmployeeController::class, 'removePhoto'])
            ->name('remove-photo');
        
        // Exportación
        Route::get('export', [EmployeeController::class, 'export'])
            ->name('export');
    });

    // ===== TIPOS DE EMPLEADO =====
    Route::resource('personnel/employee-types', EmployeeTypeController::class, [
        'as' => 'personnel'
    ]);

    // Rutas especiales para tipos de empleado
    Route::prefix('personnel/employee-types')->name('personnel.employee-types.')->group(function () {
        Route::post('{employeeType}/duplicate', [EmployeeTypeController::class, 'duplicate'])
            ->name('duplicate');
        Route::patch('update-order', [EmployeeTypeController::class, 'updateOrder'])
            ->name('update-order');
    });

    // ===== CONTRATOS =====
    Route::resource('personnel/contracts', ContractController::class, [
        'as' => 'personnel'
    ]);

    // Rutas especiales para contratos
    Route::prefix('personnel/contracts')->name('personnel.contracts.')->group(function () {
        // Gestión de estado
        Route::patch('{contract}/activate', [ContractController::class, 'activate'])
            ->name('activate');
        Route::patch('{contract}/deactivate', [ContractController::class, 'deactivate'])
            ->name('deactivate');
        Route::patch('{contract}/finalize', [ContractController::class, 'finalize'])
            ->name('finalize');
        
        // Renovación
        Route::post('{contract}/renew', [ContractController::class, 'renew'])
            ->name('renew');
    });

    // ===== VACACIONES =====
    Route::resource('personnel/vacations', VacationController::class, [
        'as' => 'personnel'
    ]);

    // Rutas especiales para vacaciones
    Route::prefix('personnel/vacations')->name('personnel.vacations.')->group(function () {
        // Flujo de aprobación
        Route::patch('{vacation}/approve', [VacationController::class, 'approve'])
            ->name('approve');
        Route::patch('{vacation}/reject', [VacationController::class, 'reject'])
            ->name('reject');
        Route::patch('{vacation}/cancel', [VacationController::class, 'cancel'])
            ->name('cancel');
        Route::patch('{vacation}/complete', [VacationController::class, 'complete'])
            ->name('complete');
    });

    // ===== ASISTENCIAS =====
    Route::resource('personnel/attendances', AttendanceController::class, [
        'as' => 'personnel',
        'except' => ['create', 'store', 'edit', 'update']
    ]);

    // Rutas especiales para asistencias
    Route::prefix('personnel/attendances')->name('personnel.attendances.')->group(function () {
        // Dashboard y vistas
        Route::get('dashboard', [AttendanceController::class, 'dashboard'])
            ->name('dashboard');
        
        // Gestión manual
        Route::post('clock-in', [AttendanceController::class, 'clockIn'])
            ->name('clock-in');
        Route::post('clock-out', [AttendanceController::class, 'clockOut'])
            ->name('clock-out');
        Route::patch('{attendance}/correct', [AttendanceController::class, 'correct'])
            ->name('correct');
    });

    // ===================================
    // API ENDPOINTS INTERNOS
    // ===================================

    Route::prefix('personnel/api')->name('personnel.api.')->group(function () {
        
        // APIs de empleados
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('active', [EmployeeController::class, 'getActiveEmployees'])->name('active');
            Route::get('search', [EmployeeController::class, 'getActiveEmployees'])->name('search');
        });

        // APIs de tipos de empleado
        Route::prefix('employee-types')->name('employee-types.')->group(function () {
            Route::get('for-select', [EmployeeTypeController::class, 'getForSelect'])->name('for-select');
            Route::get('statistics', [EmployeeTypeController::class, 'getStatistics'])->name('statistics');
        });

        // APIs de contratos
        Route::prefix('contracts')->name('contracts.')->group(function () {
            Route::get('expiring', [ContractController::class, 'getExpiringContracts'])->name('expiring');
            Route::get('statistics', [ContractController::class, 'getStatistics'])->name('statistics');
            Route::post('validate-eventual', [ContractController::class, 'validateEventualContract'])->name('validate-eventual');
        });

        // APIs de vacaciones
        Route::prefix('vacations')->name('vacations.')->group(function () {
            Route::get('employee/{employee}/available-days', [VacationController::class, 'getAvailableDays'])->name('available-days');
            Route::post('check-availability', [VacationController::class, 'checkAvailability'])->name('check-availability');
            Route::get('calendar', [VacationController::class, 'getCalendar'])->name('calendar');
            Route::get('report', [VacationController::class, 'generateReport'])->name('report');
        });

        // APIs de asistencias
        Route::prefix('attendances')->name('attendances.')->group(function () {
            Route::get('employee/{employee}/report', [AttendanceController::class, 'employeeReport'])->name('employee-report');
            Route::get('general-report', [AttendanceController::class, 'generalReport'])->name('general-report');
            Route::get('current-status', [AttendanceController::class, 'getCurrentStatus'])->name('current-status');
        });

        // API de estadísticas generales
        Route::get('dashboard-stats', function () {
            return response()->json([
                'employees' => [
                    'total' => \App\Models\Employee::count(),
                    'active' => \App\Models\Employee::where('status', 'active')->count(),
                    'inactive' => \App\Models\Employee::where('status', 'inactive')->count(),
                    'with_contracts' => \App\Models\Employee::whereHas('activeContract')->count(),
                ],
                'contracts' => [
                    'active' => \App\Models\Contract::where('status', 'active')->count(),
                    'inactive' => \App\Models\Contract::where('status', 'inactive')->count(),
                    'finished' => \App\Models\Contract::where('status', 'finished')->count(),
                    'expiring_soon' => \App\Models\Contract::where('status', 'active')
                        ->whereNotNull('end_date')
                        ->whereDate('end_date', '<=', \Carbon\Carbon::now()->addDays(30))
                        ->count(),
                    'total_payroll' => \App\Models\Contract::where('status', 'active')->sum('salary'),
                ],
                'vacations' => [
                    'pending' => \App\Models\Vacation::where('status', 'pending')->count(),
                    'approved' => \App\Models\Vacation::where('status', 'approved')->count(),
                    'this_month' => \App\Models\Vacation::whereIn('status', ['approved', 'completed'])
                        ->whereMonth('start_date', \Carbon\Carbon::now()->month)
                        ->whereYear('start_date', \Carbon\Carbon::now()->year)
                        ->count(),
                ],
                'attendance_today' => [
                    'entries' => \App\Models\Attendance::whereDate('datetime', \Carbon\Carbon::today())
                        ->where('type', 'entry')
                        ->distinct('employee_id')
                        ->count(),
                    'exits' => \App\Models\Attendance::whereDate('datetime', \Carbon\Carbon::today())
                        ->where('type', 'exit')
                        ->distinct('employee_id')
                        ->count(),
                    'total_active_employees' => \App\Models\Employee::where('status', 'active')->count(),
                ]
            ]);
        })->name('dashboard-stats');
    });
});

// ===================================
// RUTAS DE REPORTES ESPECIALES
// ===================================

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    
    Route::prefix('personnel/reports')->name('personnel.reports.')->group(function () {
        
        // Reporte de nómina
        Route::get('payroll', function () {
            $contracts = \App\Models\Contract::with(['employee', 'contractType'])
                ->where('status', 'active')
                ->orderBy('salary', 'desc')
                ->get();
                
            return view('personnel.reports.payroll', compact('contracts'));
        })->name('payroll');
        
        // Reporte de asistencias mensual
        Route::get('attendance-monthly', function () {
            return view('personnel.reports.attendance-monthly');
        })->name('attendance-monthly');
        
        // Reporte de vacaciones anuales
        Route::get('vacations-annual', function () {
            return view('personnel.reports.vacations-annual');
        })->name('vacations-annual');
        
        // Reporte de contratos por vencer
        Route::get('contracts-expiring', function () {
            $contracts = \App\Models\Contract::with(['employee', 'contractType'])
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->whereDate('end_date', '<=', \Carbon\Carbon::now()->addDays(60))
                ->orderBy('end_date')
                ->get();
                
            return view('personnel.reports.contracts-expiring', compact('contracts'));
        })->name('contracts-expiring');
    });
});