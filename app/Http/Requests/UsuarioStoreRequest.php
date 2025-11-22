<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Lo controlas con middleware de rol
    }

    public function rules(): array
    {
        return [
            'id_empleado' => ['required', 'exists:empleados,id_empleado', 'unique:usuarios,id_empleado'],
            'usuario' => ['required', 'string', 'max:255', 'unique:usuarios,usuario'],
            'correo_sistema' => ['required', 'email', 'max:255', 'unique:usuarios,correo_sistema'],
            'rol' => ['required', 'in:ADMIN,RH,JEFE,EMPLEADO'],
            'estado' => ['required', 'in:ACTIVO,BLOQUEADO'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_empleado' => 'empleado',
            'correo_sistema' => 'correo del sistema',
        ];
    }
}
