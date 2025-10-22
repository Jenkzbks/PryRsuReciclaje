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
            'start_date' => 'required|date|after_or_equal:today',
            'requested_days' => 'required|integer|min:1|max:30',
            'status' => 'required|in:Pending,Approved,Rejected,Cancelled,Completed',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Debe seleccionar un empleado.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'requested_days.required' => 'Los días solicitados son obligatorios.',
            'requested_days.integer' => 'Los días solicitados deben ser un número entero.',
            'requested_days.min' => 'Debe solicitar al menos 1 día.',
            'requested_days.max' => 'No puede solicitar más de 30 días por vez.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser: Pendiente, Aprobado, Rechazado, Cancelado o Completado.',
            'notes.max' => 'Las notas no pueden exceder 500 caracteres.',
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
        // Calcular end_date automáticamente
        if ($this->input('start_date') && $this->input('requested_days')) {
            $startDate = \Carbon\Carbon::parse($this->input('start_date'));
            $endDate = $startDate->copy()->addDays($this->input('requested_days') - 1);
            
            $this->merge([
                'end_date' => $endDate->format('Y-m-d'),
                'request_date' => now()->format('Y-m-d')
            ]);
        }
    }
}