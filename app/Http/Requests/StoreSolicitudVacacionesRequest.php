<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudVacacionesRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cualquier usuario autenticado puede crear su propia solicitud
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'in:VACACIONES,ENFERMEDAD,PERMISO'],
            'fecha_inicio' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'motivo' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'El tipo de solicitud es obligatorio.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
            'motivo.max' => 'El motivo no puede exceder los 1000 caracteres.',
        ];
    }
}
