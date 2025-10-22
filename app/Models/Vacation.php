<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Vacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'request_date',
        'start_date',
        'end_date',
        'requested_days',
        'status',
        'notes'
    ];

    protected $casts = [
        'request_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'requested_days' => 'integer',
    ];

    // Constantes para estados
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_COMPLETED = 'Completed';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_APPROVED => 'Aprobado',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_COMPLETED => 'Completado'
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_COMPLETED => 'info'
        ];
    }

    // Relaciones
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Accessors
    public function getStatusNameAttribute()
    {
        $statuses = self::getStatuses();
        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = self::getStatusColors();
        return $colors[$this->status] ?? 'secondary';
    }

    // Mutators - Calcular end_date automáticamente
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = $value;
        
        if ($value && $this->requested_days) {
            $startDate = Carbon::parse($value);
            $this->attributes['end_date'] = $startDate->addDays($this->requested_days - 1)->format('Y-m-d');
        }
    }

    public function setRequestedDaysAttribute($value)
    {
        $this->attributes['requested_days'] = $value;
        
        if ($this->start_date && $value) {
            $startDate = Carbon::parse($this->start_date);
            $this->attributes['end_date'] = $startDate->addDays($value - 1)->format('Y-m-d');
        }
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeInYear($query, $year = null)
    {
        $year = $year ?? now()->year;
        return $query->whereYear('start_date', $year);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    // Validar máximo de días por año
    public static function validateMaxDaysPerYear($employeeId, $requestedDays, $year = null, $excludeId = null)
    {
        $year = $year ?? now()->year;
        
        $employee = Employee::find($employeeId);
        if (!$employee || !$employee->activeContract) {
            return false;
        }

        $maxDays = $employee->activeContract->vacations_days_per_year ?? 30;
        
        $usedDays = self::where('employee_id', $employeeId)
            ->whereIn('status', [self::STATUS_APPROVED, self::STATUS_COMPLETED])
            ->whereYear('start_date', $year);
            
        if ($excludeId) {
            $usedDays->where('id', '!=', $excludeId);
        }
        
        $usedDays = $usedDays->sum('requested_days');
        
        return ($usedDays + $requestedDays) <= $maxDays;
    }

    // Verificar disponibilidad de fechas
    public static function hasConflictingDates($employeeId, $startDate, $endDate, $excludeId = null)
    {
        $query = self::where('employee_id', $employeeId)
            ->whereIn('status', [self::STATUS_APPROVED, self::STATUS_COMPLETED])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($subQ) use ($startDate, $endDate) {
                      $subQ->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                  });
            });
            
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}