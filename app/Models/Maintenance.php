<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'maintenances';
    
    protected $fillable = [
        'name',
        'start_date', 
        'end_date',
        'vehicle_id',
        'maintenance_type',
        'description',
        'scheduled_date',
        'completed_date',
        'status',
        'cost',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    // Agregar estos campos calculados a las respuestas JSON
    protected $appends = [
        'status',
        'status_text',
        'duration'
    ];

    // Relaciones
    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class, 'maintenance_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now()->toDateString())
                    ->where('end_date', '>=', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now()->toDateString());
    }

    public function scopeFinished($query)
    {
        return $query->where('end_date', '<', now()->toDateString());
    }

    // Accesorios
    public function getStatusAttribute()
    {
        $today = now()->toDateString();
        
        if ($this->start_date > $today) {
            return 'upcoming';
        } elseif ($this->end_date < $today) {
            return 'finished';
        } else {
            return 'active';
        }
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'upcoming':
                return 'Próximo';
            case 'active':
                return 'Activo';
            case 'finished':
                return 'Finalizado';
            default:
                return 'Desconocido';
        }
    }

    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // Métodos de validación
    public static function hasOverlap($startDate, $endDate, $excludeId = null)
    {
        $query = static::where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($sq) use ($startDate, $endDate) {
                  $sq->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function canBeDeleted()
    {
        return $this->schedules()->count() === 0;
    }
}