<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'dni' => 'required|string|size:8|regex:/^[0-9]{8}$/',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.size' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.regex' => 'El DNI debe contener solo números.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }
}