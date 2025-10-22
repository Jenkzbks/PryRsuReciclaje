<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $employeeId = $this->route('employee');

        return [
            'dni' => [
                'required',
                'string',
                'size:8',
                'regex:/^[0-9]{8}$/',
                Rule::unique('employee', 'dni')->ignore($employeeId)
            ],
            'names' => 'required|string|max:100',
            'lastnames' => 'required|string|max:200',
            'birthday' => [
                'required',
                'date',
                'before:' . now()->subYears(18)->format('Y-m-d')
            ],
            'phone' => 'nullable|string|size:9|regex:/^[0-9]{9}$/',
            'email' => [
                'nullable',
                'email:rfc',
                Rule::unique('employee', 'email')->ignore($employeeId)->whereNotNull('email')
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'address' => 'required|string|max:200',
            'license' => 'nullable|string|max:20',
            'status' => 'required|boolean',
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
            'phone.size' => 'El teléfono debe tener exactamente 9 dígitos.',
            'phone.regex' => 'El teléfono debe contener solo números.',
            'email.email' => 'El formato del email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La foto debe ser JPG, JPEG o PNG.',
            'photo.max' => 'La foto no puede exceder 2MB.',
            'address.required' => 'La dirección es obligatoria.',
            'address.max' => 'La dirección no puede exceder 200 caracteres.',
            'license.max' => 'La licencia no puede exceder 20 caracteres.',
            'status.required' => 'El estado del empleado es obligatorio.',
            'type_id.required' => 'El tipo de empleado es obligatorio.',
            'type_id.exists' => 'El tipo de empleado seleccionado no es válido.'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->has('status') ? true : false,
        ]);
    }
}