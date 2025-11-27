<?php

use App\Http\Controllers\admin\ColorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ZoneController;
use App\Http\Controllers\admin\RouteController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\ShiftController;
use App\Http\Controllers\admin\ZoneJController;
use App\Http\Controllers\admin\VehicleTypeController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\EmployeegroupController;
use App\Http\Controllers\admin\SchedulingController;
use App\Http\Controllers\admin\MaintenancesController;
use App\Http\Controllers\admin\MaintenanceShedulesController;
use App\Http\Controllers\admin\MaintenanceRecordsController;

// Controladores del módulo de personal
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeTypeController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\AttendanceController;
// Controladores del módulo de mantenimiento
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\MaintenanceRecordController;
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

Route::get('/test', function () {
    return 'Laravel funciona correctamente!';
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware([
    'auth',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


});

Route::post('admin/vehicles/filter', [VehicleController::class, 'filter'])->name('admin.vehicles.filter');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

// Rutas para gestión de zonas
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('zones', ZoneController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('colors', ColorController::class);
    Route::resource('shifts', ShiftController::class);
    
        Route::get('zonesjenkz/map', [App\Http\Controllers\Admin\ZoneJController::class, 'map'])->name('zonesjenkz.map');
    
     Route::get('schedulings/available-candidates', [SchedulingController::class, 'availableCandidates'])
    ->name('schedulings.available-candidates');

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

    // ZONAS JENKZ


    // Validación de polígono (AJAX)
    Route::post('zonesjenkz/validate-polygon', [ZoneJController::class, 'validatePolygon'])->name('zonesjenkz.validatePolygon');

    Route::resource('zonesjenkz', ZoneJController::class);

    // API para selects dependientes de zonas_jenkz
    Route::get('api/provinces', [ZoneJController::class, 'getProvinces'])->name('zonesjenkz.api.provinces');
    Route::get('api/districts', [ZoneJController::class, 'getDistricts'])->name('zonesjenkz.api.districts');
    // Endpoint para datos de distrito (lat/lng/zoom)
    Route::get('api/district-data', [ZoneJController::class, 'getDistrictData']);
    Route::get('zonesjenkz/{zone}/modal', [ZoneJController::class, 'show'])->name('zonesjenkz.show');


    

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
    // Ruta para ver empleados del grupo (modal)
    Route::get('personnel/employeegroups/{group}/employees', [EmployeegroupController::class, 'employees'])->name('personnel.employeegroups.employees');

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

    // ===================================
    // MÓDULO DE MANTENIMIENTO
    // ===================================

    // ===== MANTENIMIENTOS =====
    Route::resource('maintenance', MaintenanceController::class);
    
    // Rutas especiales para mantenimientos
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('statistics', [MaintenanceController::class, 'statistics'])->name('statistics');
        Route::post('validate-overlap', [MaintenanceController::class, 'validateDateOverlap'])->name('validate-overlap');
    });

    // ===== HORARIOS DE MANTENIMIENTO =====
    Route::get('maintenance-schedules', [MaintenanceScheduleController::class, 'index'])->name('maintenance-schedules.index');
    Route::get('maintenance/{maintenance}/schedules', [MaintenanceScheduleController::class, 'index'])->name('maintenance.schedules.index');
    Route::post('maintenance-schedules', [MaintenanceScheduleController::class, 'store'])->name('maintenance-schedules.store');
    Route::get('maintenance-schedules/{schedule}', [MaintenanceScheduleController::class, 'show'])->name('maintenance-schedules.show');
    Route::put('maintenance-schedules/{schedule}', [MaintenanceScheduleController::class, 'update'])->name('maintenance-schedules.update');
    Route::delete('maintenance-schedules/{schedule}', [MaintenanceScheduleController::class, 'destroy'])->name('maintenance-schedules.destroy');
    
    // API para horarios
    Route::prefix('api/maintenance-schedules')->name('api.maintenance-schedules.')->group(function () {
        Route::get('vehicles', [MaintenanceScheduleController::class, 'getAvailableVehicles'])->name('vehicles');
        Route::get('drivers', [MaintenanceScheduleController::class, 'getAvailableDrivers'])->name('drivers');
        Route::post('validate-overlap', [MaintenanceScheduleController::class, 'validateScheduleOverlap'])->name('validate-overlap');
    });

    // ===== ACTIVIDADES DE MANTENIMIENTO =====
    Route::get('maintenance-records', [MaintenanceRecordController::class, 'index'])->name('maintenance-records.index');
    Route::get('maintenance-schedules/{schedule}/activities', [MaintenanceRecordController::class, 'index'])->name('maintenance-activities.index');
    Route::post('maintenance-records', [MaintenanceRecordController::class, 'store'])->name('maintenance-records.store');
    Route::post('maintenance-activities', [MaintenanceRecordController::class, 'store'])->name('maintenance-activities.store');
    Route::get('maintenance-records/{record}', [MaintenanceRecordController::class, 'show'])->name('maintenance-records.show');
    Route::get('maintenance-activities/{record}', [MaintenanceRecordController::class, 'show'])->name('maintenance-activities.show');
    Route::put('maintenance-records/{record}', [MaintenanceRecordController::class, 'update'])->name('maintenance-records.update');
    Route::put('maintenance-activities/{record}', [MaintenanceRecordController::class, 'update'])->name('maintenance-activities.update');
    Route::delete('maintenance-records/{record}', [MaintenanceRecordController::class, 'destroy'])->name('maintenance-records.destroy');
    Route::delete('maintenance-activities/{record}', [MaintenanceRecordController::class, 'destroy'])->name('maintenance-activities.destroy');
    Route::delete('maintenance-activities/{record}/image', [MaintenanceRecordController::class, 'deleteImage'])->name('maintenance-activities.delete-image');
    
    // API para actividades
    Route::get('api/maintenance-schedules/{schedule}/valid-dates', [MaintenanceRecordController::class, 'getValidDates'])->name('api.maintenance-activities.valid-dates');

});

Route::resource('brands', BrandController::class)->names('admin.brands');

Route::resource('brandmodels', BrandModelController::class)->names('admin.brandmodels');

Route::resource('vehicletypes', VehicleTypeController::class)->names('admin.vehicletypes');


// MÓDULO DE MANTENIMIENTOS - EXAMEN 03
Route::resource('admin/examen03/maintenances', MaintenancesController::class)->names('admin.maintenances');
Route::resource('admin/examen03/maintenances/{maintenance}/maintenance_shedules', MaintenanceShedulesController::class)->names('admin.maintenance_shedules');
Route::resource('admin/examen03/maintenances/{maintenance}/maintenance_shedules/{schedule}/maintenance_records', MaintenanceRecordsController::class)->names('admin.maintenance_records');
Route::post('admin/examen03/maintenances/{maintenance}/maintenance_shedules/{schedule}/maintenance_records/{record}/toggle-estado', [\App\Http\Controllers\admin\MaintenanceRecordsController::class, 'toggleEstado'])->name('admin.maintenance_records.toggle_estado');


// ===================================
// MÓDULO DE GESTIÓN DE PERSONAL - RUTAS PÚBLICAS
// ===================================

// Rutas públicas para asistencias (sin autenticación completa)
Route::get('/attendance-kiosk', [AttendanceController::class, 'loginPage'])->name('attendance-kiosk.index');
Route::post('/attendance-kiosk/login', [AttendanceController::class, 'processLogin'])->name('attendance-kiosk.login');
