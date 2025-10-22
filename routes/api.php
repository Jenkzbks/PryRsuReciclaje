<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ===================================
// API MÓDULO DE GESTIÓN DE PERSONAL
// ===================================

// APIs públicas (sin autenticación para kiosco de asistencias)
Route::prefix('attendance')->group(function () {
    Route::post('login', [AttendanceController::class, 'processLogin']);
    Route::get('current-status', [AttendanceController::class, 'getCurrentStatus']);
});

// APIs protegidas
Route::middleware('auth:sanctum')->group(function () {
    
    // ===== EMPLEADOS API =====
    Route::prefix('employees')->group(function () {
        Route::get('search', [EmployeeController::class, 'getActiveEmployees']);
        Route::get('export', [EmployeeController::class, 'export']);
        Route::patch('{employee}/toggle-status', [EmployeeController::class, 'toggleStatus']);
        Route::delete('{employee}/photo', [EmployeeController::class, 'removePhoto']);
    });

    // ===== TIPOS DE EMPLEADO API =====
    Route::prefix('employee-types')->group(function () {
        Route::get('select', [EmployeeTypeController::class, 'getForSelect']);
        Route::get('statistics', [EmployeeTypeController::class, 'getStatistics']);
        Route::patch('order', [EmployeeTypeController::class, 'updateOrder']);
    });

    // ===== CONTRATOS API =====
    Route::prefix('contracts')->group(function () {
        Route::get('expiring', [ContractController::class, 'getExpiringContracts']);
        Route::get('statistics', [ContractController::class, 'getStatistics']);
        Route::post('validate-eventual', [ContractController::class, 'validateEventualContract']);
        Route::patch('{contract}/activate', [ContractController::class, 'activate']);
        Route::patch('{contract}/deactivate', [ContractController::class, 'deactivate']);
        Route::patch('{contract}/finalize', [ContractController::class, 'finalize']);
        Route::post('{contract}/renew', [ContractController::class, 'renew']);
    });

    // ===== VACACIONES API =====
    Route::prefix('vacations')->group(function () {
        Route::get('employee/{employee}/available-days', [VacationController::class, 'getAvailableDays']);
        Route::post('check-availability', [VacationController::class, 'checkAvailability']);
        Route::get('calendar', [VacationController::class, 'getCalendar']);
        Route::get('report', [VacationController::class, 'generateReport']);
        Route::patch('{vacation}/approve', [VacationController::class, 'approve']);
        Route::patch('{vacation}/reject', [VacationController::class, 'reject']);
        Route::patch('{vacation}/cancel', [VacationController::class, 'cancel']);
        Route::patch('{vacation}/complete', [VacationController::class, 'complete']);
    });

    // ===== ASISTENCIAS API =====
    Route::prefix('attendances')->group(function () {
        Route::get('dashboard-data', [AttendanceController::class, 'dashboard']);
        Route::post('clock-in', [AttendanceController::class, 'clockIn']);
        Route::post('clock-out', [AttendanceController::class, 'clockOut']);
        Route::patch('{attendance}/correct', [AttendanceController::class, 'correct']);
        Route::get('employee/{employee}/report', [AttendanceController::class, 'employeeReport']);
        Route::get('general-report', [AttendanceController::class, 'generalReport']);
    });

    // ===== DASHBOARD GENERAL API =====
    Route::prefix('personnel-dashboard')->group(function () {
        Route::get('stats', function () {
            return response()->json([
                'employees' => [
                    'total' => \App\Models\Employee::count(),
                    'active' => \App\Models\Employee::where('status', 'active')->count(),
                    'inactive' => \App\Models\Employee::where('status', 'inactive')->count(),
                ],
                'contracts' => [
                    'active' => \App\Models\Contract::where('status', 'active')->count(),
                    'expiring_soon' => \App\Models\Contract::where('status', 'active')
                        ->whereNotNull('end_date')
                        ->whereDate('end_date', '<=', \Carbon\Carbon::now()->addDays(30))
                        ->count(),
                ],
                'vacations' => [
                    'pending' => \App\Models\Vacation::where('status', 'pending')->count(),
                    'approved_this_month' => \App\Models\Vacation::where('status', 'approved')
                        ->whereMonth('start_date', \Carbon\Carbon::now()->month)
                        ->count(),
                ],
                'attendance_today' => [
                    'present' => \App\Models\Attendance::whereDate('datetime', \Carbon\Carbon::today())
                        ->where('type', 'entry')
                        ->distinct('employee_id')
                        ->count(),
                    'total_employees' => \App\Models\Employee::where('status', 'active')->count(),
                ]
            ]);
        });
    });
});
