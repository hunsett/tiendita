<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpleadoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['nullable', 'integer', 'min:1', 'max:65535', 'unique:empleados,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'curp' => ['required', 'string', 'size:18', 'unique:empleados,curp'],
            'rfc' => ['required', 'string', 'max:13', 'unique:empleados,rfc'],
            'nss' => ['required', 'string', 'max:15', 'unique:empleados,nss'],
            'correo' => ['required', 'email', 'max:255', 'unique:empleados,correo'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'fecha_ingreso' => ['nullable', 'date'],
            'estado' => ['required', 'in:ACTIVO,INACTIVO'],
            'id_departamento' => ['nullable', 'exists:departamentos,id_departamento'],
            'id_puesto' => ['nullable', 'exists:puestos,id_puesto'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_departamento' => 'departamento',
            'id_puesto' => 'puesto',
        ];
    }
}
