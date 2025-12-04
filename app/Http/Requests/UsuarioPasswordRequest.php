<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Controles por middleware / lógica en controlador
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:3', 'confirmed'],
        ];
    }
}
