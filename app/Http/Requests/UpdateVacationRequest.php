<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vacation;
use App\Models\Employee;

class UpdateVacationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $vacationId = $this->route('vacation') ? $this->route('vacation')->id : null;
        
        return [
            'employee_id' => 'required|exists:employee,id',
            'vacation_type' => 'required|in:annual,personal,sick,maternity,paternity,emergency',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_taken' => 'required|integer|min:1|max:365',
            'replacement_employee_id' => 'nullable|exists:employee,id|different:employee_id',
            'reason' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,rejected,cancelled,completed',
            'approved_by' => 'nullable|exists:employee,id|different:employee_id',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Debe seleccionar un empleado.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'vacation_type.required' => 'El tipo de vacaciones es obligatorio.',
            'vacation_type.in' => 'El tipo de vacaciones debe ser: annual, personal, sick, maternity, paternity o emergency.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'days_taken.required' => 'Los días solicitados son obligatorios.',
            'days_taken.integer' => 'Los días solicitados deben ser un número entero.',
            'days_taken.min' => 'Debe solicitar al menos 1 día.',
            'days_taken.max' => 'No puede solicitar más de 365 días.',
            'replacement_employee_id.exists' => 'El empleado de reemplazo seleccionado no existe.',
            'replacement_employee_id.different' => 'El empleado de reemplazo debe ser diferente al empleado solicitante.',
            'reason.max' => 'El motivo no puede exceder 1000 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser: pending, approved, rejected, cancelled o completed.',
            'approved_by.exists' => 'El supervisor seleccionado no existe.',
            'approved_by.different' => 'El supervisor debe ser diferente al empleado solicitante.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateVacationRules($validator);
        });
    }

    protected function validateVacationRules($validator)
    {
        $employeeId = $this->input('employee_id');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $status = $this->input('status');
        $approvedBy = $this->input('approved_by');
        $vacationId = $this->route('vacation') ? $this->route('vacation')->id : null;

        if (!$employeeId || !$startDate || !$endDate) {
            return;
        }

        $employee = Employee::find($employeeId);
        
        // Verificar que el empleado existe y puede solicitar vacaciones
        if ($employee && !$this->employeeCanRequestVacations($employee)) {
            $validator->errors()->add('employee_id', 'El empleado debe tener un contrato activo para solicitar vacaciones.');
        }

        // Verificar que se proporcione aprovador si el estado es aprobado
        if ($status === 'approved' && !$approvedBy) {
            $validator->errors()->add('approved_by', 'Debe seleccionar quién aprobó las vacaciones cuando el estado es "aprobado".');
        }

        // Verificar conflictos de fechas (excluyendo la vacación actual)
        if ($this->hasConflictingDates($employeeId, $startDate, $endDate, $vacationId)) {
            $validator->errors()->add('start_date', 'Las fechas seleccionadas se solapan con otras vacaciones ya programadas.');
        }

        // Validar que las fechas no sean en el pasado (solo para vacaciones pendientes)
        if ($status === 'pending' && \Carbon\Carbon::parse($startDate)->isPast()) {
            $validator->errors()->add('start_date', 'No se pueden programar vacaciones en fechas pasadas para solicitudes pendientes.');
        }

        // Calcular días automáticamente si no coincide
        $calculatedDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
        if ($this->input('days_taken') != $calculatedDays) {
            $this->merge(['days_taken' => $calculatedDays]);
        }
    }

    protected function employeeCanRequestVacations($employee)
    {
        // Verificar que el empleado tenga contrato activo
        return $employee->status == 1; // Activo
    }

    protected function hasConflictingDates($employeeId, $startDate, $endDate, $excludeVacationId = null)
    {
        $query = Vacation::where('employee_id', $employeeId)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($query) use ($startDate, $endDate) {
                          $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                      });
            });

        if ($excludeVacationId) {
            $query->where('id', '!=', $excludeVacationId);
        }

        return $query->exists();
    }

    protected function prepareForValidation()
    {
        // Calcular days_taken automáticamente si se proporcionan las fechas
        if ($this->input('start_date') && $this->input('end_date')) {
            $startDate = \Carbon\Carbon::parse($this->input('start_date'));
            $endDate = \Carbon\Carbon::parse($this->input('end_date'));
            
            $daysTaken = $startDate->diffInDays($endDate) + 1;
            
            $this->merge([
                'days_taken' => $daysTaken
            ]);
        }

        // Si el estado es aprobado, establecer fecha de aprobación
        if ($this->input('status') === 'approved' && !$this->route('vacation')->approved_at) {
            $this->merge([
                'approved_at' => now()
            ]);
        }

        // Si el estado cambia de aprobado a otro, limpiar datos de aprobación
        if ($this->input('status') !== 'approved') {
            $this->merge([
                'approved_at' => null
            ]);
        }
    }
}