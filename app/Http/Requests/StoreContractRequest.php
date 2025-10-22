<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Contract;
use App\Models\Employee;

class StoreContractRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => 'required|exists:employee,id',
            'contrato_type' => 'required|in:nombrado,permanente,eventual',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'salary' => 'required|numeric|min:0|max:999999.99',
            'position_id' => 'required|exists:employeetype,id',
            'departament_id' => 'required|exists:departments,id',
            'vacations_days_per_year' => 'required|integer|min:0|max:365',
            'probation_period_months' => 'required|integer|min:0|max:24',
            'is_active' => 'required|boolean',
            'termination_reason' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Debe seleccionar un empleado.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'contrato_type.required' => 'El tipo de contrato es obligatorio.',
            'contrato_type.in' => 'El tipo de contrato debe ser: nombrado, permanente o eventual.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un número válido.',
            'salary.min' => 'El salario debe ser mayor a 0.',
            'salary.max' => 'El salario no puede exceder 999,999.99.',
            'position_id.required' => 'Debe seleccionar un cargo.',
            'position_id.exists' => 'El cargo seleccionado no existe.',
            'departament_id.required' => 'Debe seleccionar un departamento.',
            'departament_id.exists' => 'El departamento seleccionado no existe.',
            'vacations_days_per_year.required' => 'Los días de vacaciones por año son obligatorios.',
            'vacations_days_per_year.integer' => 'Los días de vacaciones deben ser un número entero.',
            'vacations_days_per_year.min' => 'Los días de vacaciones no pueden ser negativos.',
            'vacations_days_per_year.max' => 'Los días de vacaciones no pueden exceder 365.',
            'probation_period_months.required' => 'El período de prueba es obligatorio.',
            'probation_period_months.integer' => 'El período de prueba debe ser un número entero.',
            'probation_period_months.min' => 'El período de prueba no puede ser negativo.',
            'probation_period_months.max' => 'El período de prueba no puede exceder 24 meses.',
            'is_active.required' => 'Debe especificar si el contrato está activo.',
            'termination_reason.max' => 'El motivo de terminación no puede exceder 255 caracteres.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateContractRules($validator);
        });
    }

    protected function validateContractRules($validator)
    {
        $employeeId = $this->input('employee_id');
        $contractType = $this->input('contrato_type');
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        $isActive = $this->input('is_active');

        // Validar que contratos permanentes no tengan fecha de fin
        if ($contractType === 'permanente' && $endDate) {
            $validator->errors()->add('end_date', 'Los contratos permanentes no pueden tener fecha de fin.');
        }

        // Validar que contratos eventuales tengan fecha de fin
        if ($contractType === 'eventual' && !$endDate) {
            $validator->errors()->add('end_date', 'Los contratos eventuales deben tener fecha de fin.');
        }

        if (!$employeeId || !$startDate) {
            return;
        }

        // Verificar que no haya más de un contrato activo
        if ($isActive) {
            $activeContracts = Contract::where('employee_id', $employeeId)
                ->where('is_active', true)
                ->count();

            if ($activeContracts > 0) {
                $validator->errors()->add('is_active', 'El empleado ya tiene un contrato activo. Desactive el contrato actual antes de crear uno nuevo.');
            }
        }

        // Verificar solapamiento de fechas
        if (Contract::hasOverlappingContract($employeeId, $startDate, $endDate)) {
            $validator->errors()->add('start_date', 'Las fechas del contrato se solapan con un contrato existente.');
        }

        // Validar regla de ventana de 2 meses para eventuales
        if ($contractType === 'eventual') {
            if (!Contract::canCreateEventualContract($employeeId, $startDate)) {
                $validator->errors()->add('start_date', 'No se puede crear un contrato eventual. Debe esperar al menos 2 meses desde el fin del último contrato eventual.');
            }
        }
    }

    protected function prepareForValidation()
    {
        // Si es contrato permanente, limpiar end_date
        if ($this->input('contrato_type') === 'permanente') {
            $this->merge(['end_date' => null]);
        }

        $this->merge([
            'is_active' => $this->has('is_active') ? true : false,
        ]);
    }
}