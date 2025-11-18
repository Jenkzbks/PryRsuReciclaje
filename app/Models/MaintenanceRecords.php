<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecords extends Model
{
    use HasFactory;

    protected $table = 'maintenancerecords';

    protected $fillable = [
        'schedule_id',
        'maintenance_date',
        'descripcion',
        'image_url',
    ];

    public function schedule()
    {
        return $this->belongsTo(\App\Models\MaintenanceShedules::class, 'schedule_id');
    }
}
