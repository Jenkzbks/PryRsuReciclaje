<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vacation;
use App\Models\Employee;

class StoreVacationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employee,id',
            'vacation_type' => 'required|in:annual,personal,sick,maternity,paternity,emergency',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_taken' => 'required|integer|min:1|max:365',
            'replacement_employee_id' => 'nullable|exists:employee,id|different:employee_id',
            'reason' => 'nullable|string|max:1000',
            'status' => 'nullable|in:pending,approved,rejected,cancelled,completed',
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
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'days_taken.required' => 'Los días solicitados son obligatorios.',
            'days_taken.integer' => 'Los días solicitados deben ser un número entero.',
            'days_taken.min' => 'Debe solicitar al menos 1 día.',
            'days_taken.max' => 'No puede solicitar más de 365 días.',
            'replacement_employee_id.exists' => 'El empleado de reemplazo seleccionado no existe.',
            'replacement_employee_id.different' => 'El empleado de reemplazo debe ser diferente al empleado solicitante.',
            'reason.max' => 'El motivo no puede exceder 1000 caracteres.',
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
        $requestedDays = $this->input('requested_days');

        if (!$employeeId || !$startDate || !$requestedDays) {
            return;
        }

        $employee = Employee::find($employeeId);
        
        // Verificar que el empleado puede solicitar vacaciones
        if (!$employee->canRequestVacations()) {
            $validator->errors()->add('employee_id', 'Solo empleados con contrato nombrado o permanente pueden solicitar vacaciones.');
            return;
        }

        // Verificar días disponibles por año
        if (!Vacation::validateMaxDaysPerYear($employeeId, $requestedDays)) {
            $available = $employee->available_vacation_days;
            $validator->errors()->add('requested_days', "No puede solicitar más días de los disponibles. Disponibles: {$available} días.");
        }

        // Calcular fecha de fin
        $endDate = \Carbon\Carbon::parse($startDate)->addDays($requestedDays - 1)->format('Y-m-d');

        // Verificar conflictos de fechas
        if (Vacation::hasConflictingDates($employeeId, $startDate, $endDate)) {
            $validator->errors()->add('start_date', 'Las fechas solicitadas se solapan con vacaciones ya aprobadas.');
        }
    }

    protected function prepareForValidation()
    {
        // Calcular days_taken automáticamente si se proporcionan las fechas
        if ($this->input('start_date') && $this->input('end_date')) {
            $startDate = \Carbon\Carbon::parse($this->input('start_date'));
            $endDate = \Carbon\Carbon::parse($this->input('end_date'));
            
            $daysTaken = $startDate->diffInDays($endDate) + 1;
            
            $this->merge([
                'days_taken' => $daysTaken,
                'requested_days' => $daysTaken, // Para compatibilidad
                'request_date' => now()->format('Y-m-d')
            ]);
        }

        // Establecer estado por defecto si no se proporciona
        if (!$this->input('status')) {
            $this->merge(['status' => 'pending']);
        }
    }
}