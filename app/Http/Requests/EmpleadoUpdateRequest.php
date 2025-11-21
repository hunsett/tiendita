<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpleadoUpdateRequest extends FormRequest
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
        $empleadoId = $this->route('empleado')->id_empleado ?? null;

        return [
            'codigo' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('empleados', 'codigo')->ignore($empleadoId, 'id_empleado'),
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'curp' => [
                'required',
                'string',
                'size:18',
                Rule::unique('empleados', 'curp')->ignore($empleadoId, 'id_empleado'),
            ],
            'rfc' => [
                'required',
                'string',
                'max:13',
                Rule::unique('empleados', 'rfc')->ignore($empleadoId, 'id_empleado'),
            ],
            'nss' => [
                'required',
                'string',
                'max:15',
                Rule::unique('empleados', 'nss')->ignore($empleadoId, 'id_empleado'),
            ],
            'correo' => [
                'required',
                'email',
                'max:255',
                Rule::unique('empleados', 'correo')->ignore($empleadoId, 'id_empleado'),
            ],
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
