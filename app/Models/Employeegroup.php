<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zone;
use App\Models\Shift;
use App\Models\Vehicle;
use App\Models\Employee;

class Employeegroup extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            'configgroups',           // tabla pivote
            'employeegroup_id',       // FK al grupo
            'employee_id'             // FK al empleado
        )->withTimestamps();          // si tu pivote tiene created_at y updated_at
    }


}
