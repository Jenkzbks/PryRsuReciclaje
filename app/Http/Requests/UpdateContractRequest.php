<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Contract;

class UpdateContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $contractId = $this->route('contract')->id;
        
        return [
            'employee_id' => [
                'required',
                'exists:employee,id',
                // Validar que no haya otro contrato activo para este empleado (excepto el actual)
                Rule::unique('contrato', 'employee_id')
                    ->where('is_active', 1)
                    ->ignore($contractId)
            ],
            'contrato_type' => [
                'required',
                'string',
                'in:nombrado,permanente,eventual'
            ],
            'start_date' => [
                'required',
                'date'
            ],
            'end_date' => [
                'nullable',
                'date',
                'after:start_date',
                // Si el tipo es eventual, la fecha de fin es requerida
                Rule::requiredIf(function () {
                    return $this->input('contrato_type') === 'eventual';
                })
            ],
            'salary' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'position_id' => [
                'required',
                'exists:employeetype,id'
            ],
            'departament_id' => [
                'nullable',
                'exists:departments,id'
            ],
            'vacations_days_per_year' => [
                'nullable',
                'integer',
                'min:0',
                'max:365'
            ],
            'probation_period_months' => [
                'nullable',
                'integer',
                'min:0',
                'max:24'
            ],
            'is_active' => [
                'sometimes',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employee_id.required' => 'El empleado es obligatorio.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'employee_id.unique' => 'Este empleado ya tiene un contrato activo.',
            
            'contrato_type.required' => 'El tipo de contrato es obligatorio.',
            'contrato_type.in' => 'El tipo de contrato debe ser: nombrado, permanente o eventual.',
            
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'end_date.required' => 'La fecha de fin es obligatoria para contratos eventuales.',
            
            'salary.required' => 'El salario es obligatorio.',
            'salary.numeric' => 'El salario debe ser un número.',
            'salary.min' => 'El salario debe ser mayor a 0.',
            'salary.max' => 'El salario no puede exceder S/. 999,999.99.',
            
            'position_id.required' => 'El cargo es obligatorio.',
            'position_id.exists' => 'El cargo seleccionado no existe.',
            
            'departament_id.exists' => 'El departamento seleccionado no existe.',
            
            'vacations_days_per_year.integer' => 'Los días de vacaciones deben ser un número entero.',
            'vacations_days_per_year.min' => 'Los días de vacaciones no pueden ser negativos.',
            'vacations_days_per_year.max' => 'Los días de vacaciones no pueden exceder 365.',
            
            'probation_period_months.integer' => 'El período de prueba debe ser un número entero.',
            'probation_period_months.min' => 'El período de prueba no puede ser negativo.',
            'probation_period_months.max' => 'El período de prueba no puede exceder 24 meses.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Si es contrato permanente, limpiar end_date
        if ($this->input('contrato_type') === 'permanente') {
            $this->merge(['end_date' => null]);
        }

        // Convertir checkbox a boolean
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active')
            ]);
        } else {
            $this->merge([
                'is_active' => false
            ]);
        }

        // Limpiar campos opcionales vacíos
        if ($this->filled('departament_id') && empty($this->input('departament_id'))) {
            $this->merge(['departament_id' => null]);
        }

        if ($this->filled('vacations_days_per_year') && empty($this->input('vacations_days_per_year'))) {
            $this->merge(['vacations_days_per_year' => null]);
        }

        if ($this->filled('probation_period_months') && empty($this->input('probation_period_months'))) {
            $this->merge(['probation_period_months' => null]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateContractRules($validator);
        });
    }

    /**
     * Additional contract business rules validation.
     */
    protected function validateContractRules($validator)
    {
        $contractType = $this->input('contrato_type');
        $endDate = $this->input('end_date');

        // Validar que contratos permanentes no tengan fecha de fin
        if ($contractType === 'permanente' && $endDate) {
            $validator->errors()->add('end_date', 'Los contratos permanentes no pueden tener fecha de fin.');
        }

        // Validar que contratos eventuales tengan fecha de fin
        if ($contractType === 'eventual' && !$endDate) {
            $validator->errors()->add('end_date', 'Los contratos eventuales deben tener fecha de fin.');
        }
    }
}
