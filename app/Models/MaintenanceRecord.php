<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $table = 'maintenancerecords';
    
    protected $fillable = [
        'schedule_id',
        'maintenance_date',
        'descripcion',
        'image_url',
        'maintenance_id',
        'employee_id',
        'activity_description',
        'activity_date',
        'notes',
        'image_path'
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'activity_date' => 'datetime'
    ];

    // Relaciones
    public function schedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'schedule_id');
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Accesorio para obtener la URL completa de la imagen
    public function getImageFullUrlAttribute()
    {
        if ($this->image_url) {
            return asset('storage/' . $this->image_url);
        }
        return null;
    }

    // Accesorio para verificar si tiene imagen
    public function getHasImageAttribute()
    {
        return !empty($this->image_url) && Storage::disk('public')->exists($this->image_url);
    }

    // Scopes
    public function scopeByDate($query, $date)
    {
        return $query->where('maintenance_date', $date);
    }

    public function scopeBySchedule($query, $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('maintenance_date', '>=', now()->subDays($days)->toDateString());
    }

    // Métodos
    public function deleteImage()
    {
        if ($this->image_url && Storage::disk('public')->exists($this->image_url)) {
            Storage::disk('public')->delete($this->image_url);
        }
    }

    // Hook para eliminar imagen cuando se elimina el registro
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($record) {
            $record->deleteImage();
        });
    }

    // Validar que la fecha de actividad esté dentro del período de mantenimiento
    // y corresponda al día de la semana del horario
    public function isValidDate($date)
    {
        $schedule = $this->schedule;
        $maintenance = $schedule->maintenance;
        
        // Verificar que esté dentro del rango de mantenimiento
        if ($date < $maintenance->start_date || $date > $maintenance->end_date) {
            return false;
        }
        
        // Verificar que el día de la semana coincida
        $dayOfWeek = strtoupper($date->locale('es')->dayName);
        $dayMapping = [
            'LUNES' => 'LUNES',
            'MARTES' => 'MARTES',
            'MIÉRCOLES' => 'MIERCOLES',
            'JUEVES' => 'JUEVES',
            'VIERNES' => 'VIERNES',
            'SÁBADO' => 'SABADO'
        ];
        
        return isset($dayMapping[$dayOfWeek]) && $dayMapping[$dayOfWeek] === $schedule->day_of_week;
    }
}