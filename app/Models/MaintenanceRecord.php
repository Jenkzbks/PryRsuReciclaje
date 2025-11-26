<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $table = 'maintenancerecords';

    protected $fillable = [
        'schedule_id',
        'maintenance_date',
        'descripcion',
        'image_url',
        'estado',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
    ];

    public function schedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'schedule_id');
    }
}
