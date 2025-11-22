<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsuarioUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario')->id_usuario ?? null;

        return [
            // No permitimos cambiar el empleado ligado aquí
            'usuario' => [
                'required',
                'string',
                'max:255',
                Rule::unique('usuarios', 'usuario')->ignore($usuarioId, 'id_usuario'),
            ],
            'correo_sistema' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios', 'correo_sistema')->ignore($usuarioId, 'id_usuario'),
            ],
            'rol' => ['required', 'in:ADMIN,RH,JEFE,EMPLEADO'],
            'estado' => ['required', 'in:ACTIVO,BLOQUEADO'],
        ];
    }

    public function attributes(): array
    {
        return [
            'correo_sistema' => 'correo del sistema',
        ];
    }
}
