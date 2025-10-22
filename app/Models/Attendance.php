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
        'attendance_datetime',
        'type',
        'period',
        'status',
        'notes'
    ];

    protected $casts = [
        'attendance_datetime' => 'datetime',
        'period' => 'integer',
        'status' => 'integer',
    ];

    // Constantes para tipos
    const TYPE_ENTRADA = 'Entrada';
    const TYPE_SALIDA = 'Salida';

    // Constantes para estados
    const STATUS_PRESENTE = 1;
    const STATUS_AUSENTE = 0;
    const STATUS_TARDE = 2;

    public static function getTypes()
    {
        return [
            self::TYPE_ENTRADA => 'Entrada',
            self::TYPE_SALIDA => 'Salida'
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PRESENTE => 'Presente',
            self::STATUS_AUSENTE => 'Ausente',
            self::STATUS_TARDE => 'Tarde'
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_PRESENTE => 'success',
            self::STATUS_AUSENTE => 'danger',
            self::STATUS_TARDE => 'warning'
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
        return $this->attendance_datetime->format('Y-m-d');
    }

    public function getAttendanceTimeAttribute()
    {
        return $this->attendance_datetime->format('H:i:s');
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('attendance_datetime', $date);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_datetime', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENTE);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_datetime', today());
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
            'attendance_datetime' => now(),
            'type' => self::TYPE_ENTRADA,
            'status' => self::STATUS_PRESENTE,
            'period' => 1, // Turno por defecto
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
            ->where('status', self::STATUS_PRESENTE)
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