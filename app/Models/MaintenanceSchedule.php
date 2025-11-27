<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
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
        'recurrence_weeks',
        'start_date',
        'end_date',
        'description',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // Constantes para tipos de mantenimiento
    const TYPE_PREVENTIVE = 'preventive';
    const TYPE_CORRECTIVE = 'corrective';
    const TYPE_PREDICTIVE = 'predictive';

    public static function getMaintenanceTypes()
    {
        return [
            self::TYPE_PREVENTIVE => 'Preventivo',
            self::TYPE_CORRECTIVE => 'Correctivo',
            self::TYPE_PREDICTIVE => 'Predictivo'
        ];
    }

    // Constantes para días de la semana (números)
    const DAYS_OF_WEEK = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado'
    ];

    // Relaciones
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function activities()
    {
        return $this->hasMany(MaintenanceRecord::class, 'schedule_id');
    }

    public function scheduledDays()
    {
        return $this->hasMany(MaintenanceScheduleDay::class, 'schedule_id');
    }

    // Scopes
    public function scopeByDay($query, $day)
    {
        return $query->where('day_of_week', $day);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('maintenance_type', $type);
    }

    // Accesorios
    public function getMaintenanceTypeTextAttribute()
    {
        $types = self::getMaintenanceTypes();
        return $types[$this->maintenance_type] ?? $this->maintenance_type;
    }

    public function getDayOfWeekTextAttribute()
    {
        return self::DAYS_OF_WEEK[$this->day_of_week] ?? $this->day_of_week;
    }

    public function getTimeRangeAttribute()
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }

    // Métodos de validación
    public static function hasScheduleOverlap($vehicleId, $dayOfWeek, $startTime, $endTime, $excludeId = null)
    {
        $query = static::where('vehicle_id', $vehicleId)
                      ->where('day_of_week', $dayOfWeek)
                      ->where(function ($q) use ($startTime, $endTime) {
                          $q->where(function ($sq) use ($startTime, $endTime) {
                              $sq->where('start_time', '<', $endTime)
                                 ->where('end_time', '>', $startTime);
                          });
                      });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function canBeDeleted()
    {
        return $this->activities()->count() === 0;
    }

    // Validar que el horario esté dentro del rango de mantenimiento
    public function isWithinMaintenancePeriod($date)
    {
        return $date >= $this->maintenance->start_date && $date <= $this->maintenance->end_date;
    }
}