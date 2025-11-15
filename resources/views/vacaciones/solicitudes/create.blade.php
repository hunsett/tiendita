@extends('layouts.app')

@section('title', 'Nueva solicitud de vacaciones')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">
                Nueva solicitud de vacaciones
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Completa la información para enviar tu solicitud a revisión.
            </p>
        </div>

        <a href="{{ route('dashboard') }}"
           class="text-xs text-gray-500 hover:text-gray-700">
            ← Volver al dashboard
        </a>
    </div>

    {{-- Info de empleado y saldo --}}
    <div class="bg-white rounded-xl shadow-sm p-4 space-y-2">
        <p class="text-sm">
            <span class="font-semibold">Empleado:</span>
            {{ $empleado->nombre }} {{ $empleado->apellidos }}
        </p>
        @if($empleado->departamento)
            <p class="text-sm text-gray-600">
                <span class="font-semibold">Departamento:</span>
                {{ $empleado->departamento->nombre }}
            </p>
        @endif
        @if($empleado->puesto)
            <p class="text-sm text-gray-600">
                <span class="font-semibold">Puesto:</span>
                {{ $empleado->puesto->nombre }}
            </p>
        @endif

        <div class="mt-3 border-t pt-3">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                Saldo de vacaciones
            </p>
            @if($saldoActual)
                <div class="flex items-center justify-between mt-1 text-sm">
                    <span>Días disponibles:</span>
                    <span class="font-semibold text-indigo-600">
                        {{ number_format($saldoActual->dias_disponibles, 1) }} días
                    </span>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Periodo: {{ $saldoActual->periodo_inicio->format('d/m/Y') }}
                    – {{ $saldoActual->periodo_fin->format('d/m/Y') }}
                </p>
            @else
                <p class="mt-1 text-xs text-red-600">
                    No hay un saldo de vacaciones registrado. Es posible que RH aún no haya cargado tu información.
                </p>
            @endif
        </div>
    </div>

    {{-- Errores globales --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-lg p-3">
            <p class="font-semibold mb-1">
                Por favor corrige los siguientes errores:
            </p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario --}}
    <form action="{{ route('vacaciones.solicitudes.store') }}" method="POST" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
        @csrf

        {{-- Tipo de solicitud --}}
        <div>
            <label for="tipo" class="block text-sm font-medium text-gray-700">
                Tipo de solicitud
            </label>
            <select name="tipo" id="tipo"
                    class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Selecciona una opción</option>
                <option value="VACACIONES" {{ old('tipo') === 'VACACIONES' ? 'selected' : '' }}>
                    Vacaciones
                </option>
                <option value="ENFERMEDAD" {{ old('tipo') === 'ENFERMEDAD' ? 'selected' : '' }}>
                    Permiso por enfermedad
                </option>
                <option value="PERMISO" {{ old('tipo') === 'PERMISO' ? 'selected' : '' }}>
                    Otro permiso
                </option>
            </select>
            @error('tipo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Fechas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">
                    Fecha de inicio
                </label>
                <input type="date" name="fecha_inicio" id="fecha_inicio"
                       value="{{ old('fecha_inicio') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('fecha_inicio')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700">
                    Fecha de fin
                </label>
                <input type="date" name="fecha_fin" id="fecha_fin"
                       value="{{ old('fecha_fin') }}"
                       class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('fecha_fin')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Motivo --}}
        <div>
            <label for="motivo" class="block text-sm font-medium text-gray-700">
                Motivo (opcional)
            </label>
            <textarea name="motivo" id="motivo" rows="3"
                      class="mt-1 block w-full rounded-lg border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Ejemplo: Vacaciones familiares, cita médica, trámite personal, etc.">{{ old('motivo') }}</textarea>
            @error('motivo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-xs font-medium text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                Enviar solicitud
            </button>
        </div>
    </form>
</div>
@endsection
