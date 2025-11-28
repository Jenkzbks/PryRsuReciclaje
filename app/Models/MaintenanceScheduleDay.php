<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceScheduleDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'scheduled_date',
        'observation',
        'image_path',
        'is_completed'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'is_completed' => 'boolean'
    ];

    /**
     * Relación con MaintenanceSchedule
     */
    public function schedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'schedule_id');
    }
}
