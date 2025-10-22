<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'hours_worked',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'hours_worked' => 'decimal:2',
    ];

    // Constantes para estados
    const STATUS_PRESENT = 'present';
    const STATUS_LATE = 'late';
    const STATUS_ABSENT = 'absent';
    const STATUS_HALF_DAY = 'half_day';

    public static function getStatuses()
    {
        return [
            self::STATUS_PRESENT => 'Presente',
            self::STATUS_LATE => 'Tarde',
            self::STATUS_ABSENT => 'Ausente',
            self::STATUS_HALF_DAY => 'Medio Día'
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_PRESENT => 'success',
            self::STATUS_LATE => 'warning',
            self::STATUS_ABSENT => 'danger',
            self::STATUS_HALF_DAY => 'info'
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
        return $statuses[$this->status] ?? 'Desconocido';
    }

    public function getStatusColorAttribute()
    {
        $colors = self::getStatusColors();
        return $colors[$this->status] ?? 'secondary';
    }

    public function getAttendanceDateAttribute()
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }

    public function getCheckInTimeAttribute()
    {
        return $this->check_in ? $this->check_in->format('H:i:s') : null;
    }

    public function getCheckOutTimeAttribute()
    {
        return $this->check_out ? $this->check_out->format('H:i:s') : null;
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    // Métodos estáticos de utilidad
    public static function markAttendance($employeeId, $dni, $password)
    {
        $employee = Employee::where('dni', $dni)
            ->where('status', true)
            ->first();

        if (!$employee || !Hash::check($password, $employee->password)) {
            return [
                'success' => false,
                'message' => 'DNI o contraseña incorrectos'
            ];
        }

        // Verificar si ya marcó hoy
        $today = today();
        $existingAttendance = self::byEmployee($employee->id)
            ->byDate($today)
            ->first();

        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Ya ha registrado su asistencia el día de hoy',
                'attendance' => $existingAttendance
            ];
        }

        // Crear nueva asistencia
        $attendance = self::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'check_in' => now(),
            'status' => self::STATUS_PRESENT,
            'notes' => 'Marcación automática'
        ]);

        return [
            'success' => true,
            'message' => 'Asistencia registrada correctamente',
            'attendance' => $attendance,
            'employee' => $employee
        ];
    }

    // Verificar si empleado está presente en una fecha
    public static function isEmployeePresent($employeeId, $date = null)
    {
        $date = $date ?? today();
        
        return self::byEmployee($employeeId)
            ->byDate($date)
            ->where('status', self::STATUS_PRESENT)
            ->exists();
    }

    // Obtener estadísticas de asistencia
    public static function getAttendanceStats($startDate, $endDate)
    {
        $total = self::dateRange($startDate, $endDate)->count();
        $present = self::dateRange($startDate, $endDate)->present()->count();
        $absent = self::dateRange($startDate, $endDate)->where('status', self::STATUS_AUSENTE)->count();
        $late = self::dateRange($startDate, $endDate)->where('status', self::STATUS_TARDE)->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'present_percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0
        ];
    }
}