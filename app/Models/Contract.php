<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contrato';

    protected $fillable = [
        'employee_id',
        'contrato_type',
        'start_date',
        'end_date',
        'salary',
        'position_id',
        'departament_id',
        'vacations_days_per_year',
        'probation_period_months',
        'is_active',
        'termination_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
        'vacations_days_per_year' => 'integer',
        'probation_period_months' => 'integer',
    ];

    // Constantes para tipos de contrato
    const TYPE_NOMBRADO = 'nombrado';
    const TYPE_PERMANENTE = 'permanente';
    const TYPE_EVENTUAL = 'eventual';

    public static function getTypes()
    {
        return [
            self::TYPE_NOMBRADO => 'Nombrado',
            self::TYPE_PERMANENTE => 'Permanente (Indefinido)',
            self::TYPE_EVENTUAL => 'Eventual (Temporal)'
        ];
    }

    // Relaciones
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function position()
    {
        return $this->belongsTo(EmployeeType::class, 'position_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'departament_id');
    }

    // Accessors
    public function getContractTypeNameAttribute()
    {
        $types = self::getTypes();
        return $types[$this->contrato_type] ?? $this->contrato_type;
    }

    public function getIsActiveTextAttribute()
    {
        return $this->is_active ? 'Activo' : 'Inactivo';
    }

    public function getIsInProbationPeriodAttribute()
    {
        if (!$this->probation_period_months) return false;
        
        $probationEnd = $this->start_date->addMonths($this->probation_period_months);
        return now()->lessThan($probationEnd);
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->end_date) return false; // Permanente
        return now()->greaterThan($this->end_date);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('contrato_type', $type);
    }

    public function scopeEventual($query)
    {
        return $query->where('contrato_type', self::TYPE_EVENTUAL);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>', now());
        });
    }

    // Validar regla de ventana de 2 meses para eventuales
    public static function canCreateEventualContract($employeeId, $startDate)
    {
        $lastEventual = self::where('employee_id', $employeeId)
            ->where('contrato_type', self::TYPE_EVENTUAL)
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$lastEventual || !$lastEventual->end_date) {
            return true;
        }

        $twoMonthsAfter = Carbon::parse($lastEventual->end_date)->addMonths(2);
        return Carbon::parse($startDate)->greaterThanOrEqualTo($twoMonthsAfter);
    }

    // Verificar solapamiento de contratos
    public static function hasOverlappingContract($employeeId, $startDate, $endDate, $excludeId = null)
    {
        $query = self::where('employee_id', $employeeId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->where(function($q) use ($startDate, $endDate) {
            if ($endDate) {
                // Contrato temporal
                $q->where(function($subQ) use ($startDate, $endDate) {
                    $subQ->whereBetween('start_date', [$startDate, $endDate])
                         ->orWhereBetween('end_date', [$startDate, $endDate])
                         ->orWhere(function($innerQ) use ($startDate, $endDate) {
                             $innerQ->where('start_date', '<=', $startDate)
                                    ->where(function($dateQ) use ($endDate) {
                                        $dateQ->where('end_date', '>=', $endDate)
                                              ->orWhereNull('end_date');
                                    });
                         });
                });
            } else {
                // Contrato permanente
                $q->where('start_date', '<=', $startDate)
                  ->where(function($dateQ) {
                      $dateQ->where('end_date', '>=', now())
                            ->orWhereNull('end_date');
                  });
            }
        })->exists();
    }
}