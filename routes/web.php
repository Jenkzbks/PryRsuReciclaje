<?php

use App\Http\Controllers\admin\ColorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ZoneController;
use App\Http\Controllers\admin\RouteController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\ShiftController;
use App\Http\Controllers\admin\VehicleTypeController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\EmployeegroupController;
use App\Http\Controllers\admin\SchedulingController;
// Controladores del módulo de personal
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\AttendanceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});


Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

// Rutas para gestión de zonas
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('zones', ZoneController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('shifts', ShiftController::class);
    Route::resource('schedulings', SchedulingController::class);
    Route::get('schedulings/group-info/{group}', [\App\Http\Controllers\admin\SchedulingController::class, 'groupInfo'])
    ->name('schedulings.group-info');
    Route::post('schedulings/check-availability', [\App\Http\Controllers\admin\SchedulingController::class, 'checkAvailability'])
    ->name('schedulings.check-availability');


    // API routes para selects dependientes
    Route::get('api/provinces/{department_id}', [ZoneController::class, 'getProvinces'])->name('api.provinces');
    Route::get('api/districts/{province_id}', [ZoneController::class, 'getDistricts'])->name('api.districts');
    Route::get('api/department-coordinates/{department_id}', [ZoneController::class, 'getDepartmentCoordinates'])->name('api.department.coordinates');
    Route::get('api/zones-polygons', [ZoneController::class, 'getZonesPolygons'])->name('api.zones.polygons');
    // API para obtener modelos por marca (select dependiente)
    Route::get('api/models/{brand_id}', [VehicleController::class, 'modelsByBrand'])->name('api.models');

    // ===================================
    // MÓDULO DE GESTIÓN DE PERSONAL
    // ===================================
    
    // ===== DASHBOARD PERSONAL =====
    Route::get('/personnel', function () {
        return view('personnel.dashboard');
    })->name('personnel.dashboard');
    
    // ===== EMPLEADOS =====
    Route::resource('personnel/employees', EmployeeController::class, [
        'as' => 'personnel'
    ]);

    // ===== GRUPOS DE PERSONAL =====
    Route::resource('personnel/employeegroups', EmployeegroupController::class, [
        'as' => 'personnel'
    ]);
    
    // Rutas especiales para empleados
    Route::prefix('personnel/employees')->name('personnel.employees.')->group(function () {
        Route::patch('{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('{employee}/remove-photo', [EmployeeController::class, 'removePhoto'])->name('remove-photo');
        Route::get('export', [EmployeeController::class, 'export'])->name('export');
        
        // API endpoints
        Route::get('api/active', [EmployeeController::class, 'getActiveEmployees'])->name('api.active');
    });

    // ===== TIPOS DE EMPLEADO =====
    Route::resource('personnel/employee-types', EmployeeTypeController::class, [
        'as' => 'personnel'
    ]);

    
    
    // Rutas especiales para tipos de empleado
    Route::prefix('personnel/employee-types')->name('personnel.employee-types.')->group(function () {
        Route::post('{employeeType}/duplicate', [EmployeeTypeController::class, 'duplicate'])->name('duplicate');
        Route::patch('update-order', [EmployeeTypeController::class, 'updateOrder'])->name('update-order');
        
        // API endpoints
        Route::get('api/for-select', [EmployeeTypeController::class, 'getForSelect'])->name('api.for-select');
        Route::get('api/statistics', [EmployeeTypeController::class, 'getStatistics'])->name('api.statistics');
    });

    // ===== CONTRATOS =====
    Route::resource('personnel/contracts', ContractController::class, [
        'as' => 'personnel'
    ]);
    
    // Rutas especiales para contratos
    Route::prefix('personnel/contracts')->name('personnel.contracts.')->group(function () {
        Route::patch('{contract}/activate', [ContractController::class, 'activate'])->name('activate');
        Route::patch('{contract}/deactivate', [ContractController::class, 'deactivate'])->name('deactivate');
        Route::get('employee/{employee}', [ContractController::class, 'getByEmployee'])->name('by-employee');
        
        // API endpoints
        Route::get('api/active', [ContractController::class, 'getActiveContracts'])->name('api.active');
        Route::get('api/expiring', [ContractController::class, 'getExpiringContracts'])->name('api.expiring');
    });

    // ===== VACACIONES =====
    Route::resource('personnel/vacations', VacationController::class, [
        'as' => 'personnel'
    ]);
    
    // Rutas especiales para vacaciones
    Route::prefix('personnel/vacations')->name('personnel.vacations.')->group(function () {
        Route::patch('{vacation}/approve', [VacationController::class, 'approve'])->name('approve');
        Route::patch('{vacation}/reject', [VacationController::class, 'reject'])->name('reject');
        Route::patch('{vacation}/cancel', [VacationController::class, 'cancel'])->name('cancel');
        
        // API endpoints
        Route::get('api/pending', [VacationController::class, 'getPendingVacations'])->name('api.pending');
        Route::get('api/calendar', [VacationController::class, 'getCalendarData'])->name('api.calendar');
        Route::get('api/employee/{employee}', [VacationController::class, 'getByEmployee'])->name('api.by-employee');
    });

    // ===== ASISTENCIAS =====
    // Rutas especiales ANTES del resource para evitar conflictos
    Route::prefix('personnel/attendances')->name('personnel.attendances.')->group(function () {
        Route::get('dashboard', [AttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('bulk-import', [AttendanceController::class, 'bulkImport'])->name('bulk-import');
        Route::post('clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
        Route::post('clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
        
        // API endpoints para reportes
        Route::get('api/daily/{date}', [AttendanceController::class, 'getDailyAttendance'])->name('api.daily');
        Route::get('api/employee/{employee}/month/{month}', [AttendanceController::class, 'getMonthlyByEmployee'])->name('api.monthly');
        Route::get('api/reports/summary', [AttendanceController::class, 'getSummaryReport'])->name('api.summary');
        Route::get('api/late-arrivals', [AttendanceController::class, 'getLateArrivals'])->name('api.late-arrivals');
        Route::get('api/current-status', [AttendanceController::class, 'getCurrentStatus'])->name('api.current-status');
        Route::get('api/employee/{employee}/report', [AttendanceController::class, 'employeeReport'])->name('api.employee-report');
        Route::get('api/general-report', [AttendanceController::class, 'generalReport'])->name('api.general-report');
    });
    
    // Resource DESPUÉS de las rutas específicas
    Route::resource('personnel/attendances', AttendanceController::class, [
        'as' => 'personnel'
    ]);
    
    // Rutas especiales adicionales para asistencias (que requieren {attendance})
    Route::prefix('personnel/attendances')->name('personnel.attendances.')->group(function () {
        Route::patch('{attendance}/approve', [AttendanceController::class, 'approve'])->name('approve');
        Route::patch('{attendance}/reject', [AttendanceController::class, 'reject'])->name('reject');
        Route::patch('{attendance}/correct', [AttendanceController::class, 'correct'])->name('correct');
    });
});

Route::resource('brands', BrandController::class)->names('admin.brands');

Route::resource('brandmodels', BrandModelController::class)->names('admin.brandmodels');

Route::resource('vehicletypes', VehicleTypeController::class)->names('admin.vehicletypes');
    


// ===================================
// MÓDULO DE GESTIÓN DE PERSONAL - RUTAS PÚBLICAS
// ===================================

// Rutas públicas para asistencias (sin autenticación completa)
Route::get('/attendance-kiosk', [AttendanceController::class, 'loginPage'])->name('attendance-kiosk.index');
Route::post('/attendance-kiosk/login', [AttendanceController::class, 'processLogin'])->name('attendance-kiosk.login');





