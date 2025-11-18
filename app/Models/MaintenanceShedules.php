<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceShedules extends Model
{
    use HasFactory;

    protected $table = 'maintenanceschedules';

    protected $fillable = [
        'maintenance_id',
        'vehicle_id',
        'driver_id',
        'start_time',
        'end_time',
        'day_of_week',
        'maintenance_type',
    ];

    // Relaciones
    public function maintenance()
    {
        return $this->belongsTo(Maintenances::class, 'maintenance_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }
}
