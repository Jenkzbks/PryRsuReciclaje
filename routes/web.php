<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ZoneController;
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
    
    // API routes para selects dependientes
    Route::get('api/provinces/{department_id}', [ZoneController::class, 'getProvinces'])->name('api.provinces');
    Route::get('api/districts/{province_id}', [ZoneController::class, 'getDistricts'])->name('api.districts');
    Route::get('api/department-coordinates/{department_id}', [ZoneController::class, 'getDepartmentCoordinates'])->name('api.department.coordinates');
    Route::get('api/zones-polygons', [ZoneController::class, 'getZonesPolygons'])->name('api.zones.polygons');
});
