<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'login' => 'required|string|max:255',   // puede ser usuario o correo_sistema
            'password' => 'required|string|min:3|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Ingresa tu usuario o correo.',
            'password.required' => 'Ingresa tu contraseña.',
        ];
    }
}
