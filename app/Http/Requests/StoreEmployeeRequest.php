<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dni' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]{8}$/',
                Rule::unique('employee', 'dni')
            ],
            'names' => 'required|string|max:100',
            'lastnames' => 'required|string|max:200',
            'birthday' => [
                'required',
                'date',
                'before:' . now()->subYears(18)->format('Y-m-d')
            ],
            'license' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email:rfc',
                'max:100',
                Rule::unique('employee', 'email')->whereNotNull('email')
            ],
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'address' => 'required|string|max:200',
            'password' => 'nullable|string|min:6|confirmed',  // Opcional para crear empleados
            'status' => 'nullable|string|in:active,inactive,suspended,terminated',
            'type_id' => 'required|exists:employeetype,id'
        ];
    }

    public function messages()
    {
        return [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.regex' => 'El DNI debe contener solo números.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'names.required' => 'Los nombres son obligatorios.',
            'names.max' => 'Los nombres no pueden exceder 100 caracteres.',
            'lastnames.required' => 'Los apellidos son obligatorios.',
            'lastnames.max' => 'Los apellidos no pueden exceder 200 caracteres.',
            'birthday.required' => 'La fecha de nacimiento es obligatoria.',
            'birthday.before' => 'El empleado debe ser mayor de 18 años.',
            'gender.in' => 'El género debe ser Masculino, Femenino u Otro.',
            'phone.max' => 'El teléfono no puede exceder 20 caracteres.',
            'email.email' => 'El formato del email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La foto debe ser JPG, JPEG o PNG.',
            'photo.max' => 'La foto no puede exceder 2MB.',
            'address.max' => 'La dirección no puede exceder 200 caracteres.',
            'hire_date.date' => 'La fecha de contratación debe ser válida.',
            'salary.numeric' => 'El salario debe ser un número.',
            'salary.min' => 'El salario no puede ser negativo.',
            'status.in' => 'El estado debe ser activo, inactivo, suspendido o terminado.',
            'type_id.required' => 'El tipo de empleado es obligatorio.',
            'type_id.exists' => 'El tipo de empleado seleccionado no es válido.'
        ];
    }

    protected function prepareForValidation()
    {
        // Establecer valores por defecto
        $this->merge([
            'status' => $this->get('status', 'active'),
            'license' => $this->get('license', ''),
            'photo' => $this->get('photo', ''),
            'password' => $this->get('password', bcrypt('password123')) // Password por defecto
        ]);
    }
}