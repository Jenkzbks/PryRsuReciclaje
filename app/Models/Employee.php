<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class Employee extends Authenticatable
{
    use HasFactory;

    protected $table = 'employee';

    protected $fillable = [
        'dni',
        'names',
        'lastnames',
        'birthday',
        'license',   // Agregado
        'phone',
        'email',
        'password',  // Agregado
        'photo',
        'address',
        'status',
        'type_id'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'birthday' => 'date',
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'status' => 'integer',  // Cambiado de boolean a integer
        'email_verified_at' => 'datetime',
    ];

    // Definir los estados disponibles
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public const STATUS_SUSPENDED = 2;
    public const STATUS_TERMINATED = 3;

    // Accessor para obtener el estado como string
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'active';
            case self::STATUS_INACTIVE:
                return 'inactive';
            case self::STATUS_SUSPENDED:
                return 'suspended';
            case self::STATUS_TERMINATED:
                return 'terminated';
            default:
                return 'unknown';
        }
    }

    // Mutator para convertir string a integer
    public function setStatusAttribute($value)
    {
        switch ($value) {
            case 'active':
                $this->attributes['status'] = self::STATUS_ACTIVE;
                break;
            case 'inactive':
                $this->attributes['status'] = self::STATUS_INACTIVE;
                break;
            case 'suspended':
                $this->attributes['status'] = self::STATUS_SUSPENDED;
                break;
            case 'terminated':
                $this->attributes['status'] = self::STATUS_TERMINATED;
                break;
            default:
                $this->attributes['status'] = is_numeric($value) ? (int)$value : self::STATUS_ACTIVE;
        }
    }

    // Accessor para nombre completo
    public function getFullNameAttribute()
    {
        return $this->names . ' ' . $this->lastnames;
    }

    // Accessor para foto con URL completa
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('vendor/adminlte/dist/img/user-160x160.jpg'); // Avatar por defecto
    }

    // Mutator para encriptar contraseña
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    // Verificar si es mayor de edad
    public function getIsAdultAttribute()
    {
        return $this->birthday->diffInYears(now()) >= 18;
    }

    // Relaciones
    public function type()
    {
        return $this->belongsTo(EmployeeType::class, 'type_id');
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'type_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'employee_id');
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class, 'employee_id')->where('is_active', true);
    }

    public function vacations()
    {
        return $this->hasMany(Vacation::class, 'employee_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByDni($query, $dni)
    {
        return $query->where('dni', $dni);
    }

    // Método para verificar disponibilidad de vacaciones
    public function getAvailableVacationDaysAttribute()
    {
        $contract = $this->activeContract;
        if (!$contract) return 0;

        $usedDays = $this->vacations()
            ->whereIn('status', ['Approved', 'Completed'])
            ->whereYear('start_date', now()->year)
            ->sum('requested_days');

        return max(0, ($contract->vacations_days_per_year ?? 30) - $usedDays);
    }

    // Verificar si puede solicitar vacaciones
    public function canRequestVacations()
    {
        $contract = $this->activeContract;
        if (!$contract) return false;

        return in_array($contract->contrato_type, ['nombrado', 'permanente']);
    }
}