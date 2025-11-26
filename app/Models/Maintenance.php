<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    
    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class, 'maintenance_id');
    }

   
    public function records()
    {
        return $this->hasManyThrough(
            MaintenanceRecord::class,
            MaintenanceSchedule::class,
            'maintenance_id',   
            'schedule_id',     
            'id',               
            'id'                
        );
    }

    public function getRangeTextAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return '-';
        }
        return $this->start_date->format('Y-m-d').' al '.$this->end_date->format('Y-m-d');
    }
}
