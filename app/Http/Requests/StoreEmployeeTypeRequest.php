<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('employeetype', 'name')
            ],
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del tipo de empleado es obligatorio.',
            'name.max' => 'El nombre no puede exceder 100 caracteres.',
            'name.unique' => 'Ya existe un tipo de empleado con este nombre.',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres.',
        ];
    }
}