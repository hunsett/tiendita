@extends('layouts.app')

@section('title', 'Nueva solicitud de vacaciones')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-4xl mx-auto px-4 space-y-6">

        {{-- Encabezado --}}
        <div class="flex items-start md:items-center justify-between gap-3">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Nueva solicitud de vacaciones
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Completa la información para enviar tu solicitud a revisión.
                </p>
            </div>

            <a href="{{ route('dashboard') }}"
               class="text-xs text-slate-300 hover:text-white hover:underline mt-1">
                ← Volver al dashboard
            </a>
        </div>

        {{-- Card principal glass con degradado --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            {{-- Degradado de fondo (oscuro → claro → azul) --}}
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            {{-- Oscurecimiento --}}
            <div class="absolute inset-0 bg-black/35"></div>
            {{-- Blur tipo glass --}}
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            {{-- Contenido real --}}
            <div class="relative px-6 py-7 text-slate-50 space-y-5">

                {{-- Info de empleado y saldo --}}
                <div class="rounded-2xl border border-white/20 bg-black/20 px-4 py-4 space-y-2">
                    <p class="text-sm">
                        <span class="font-semibold">Empleado:</span>
                        {{ $empleado->nombre }} {{ $empleado->apellidos }}
                    </p>

                    @if($empleado->departamento)
                        <p class="text-sm text-slate-100/90">
                            <span class="font-semibold">Departamento:</span>
                            {{ $empleado->departamento->nombre }}
                        </p>
                    @endif

                    @if($empleado->puesto)
                        <p class="text-sm text-slate-100/90">
                            <span class="font-semibold">Puesto:</span>
                            {{ $empleado->puesto->nombre }}
                        </p>
                    @endif

                    <div class="mt-3 pt-3 border-t border-white/15">
                        <p class="text-[11px] font-semibold text-slate-200 uppercase tracking-[0.18em]">
                            Saldo de vacaciones
                        </p>

                        @if($saldoActual)
                            <div class="flex items-center justify-between mt-1 text-sm">
                                <span>Días disponibles:</span>
                                <span class="font-semibold text-emerald-300">
                                    {{ number_format($saldoActual->dias_disponibles, 1) }} días
                                </span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-200/80">
                                Periodo:
                                {{ $saldoActual->periodo_inicio->format('d/m/Y') }}
                                – {{ $saldoActual->periodo_fin->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="mt-2 text-xs text-rose-200">
                                No hay un saldo de vacaciones registrado. Es posible que RH aún no haya cargado tu información.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Errores globales --}}
                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-300/60 text-rose-50 text-sm rounded-2xl px-4 py-3">
                        <p class="font-semibold mb-1">
                            Por favor corrige los siguientes errores:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 text-[13px]">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulario --}}
                <form action="{{ route('vacaciones.solicitudes.store') }}"
                      method="POST"
                      class="rounded-2xl border border-white/20 bg-black/20 px-4 py-5 space-y-4">
                    @csrf

                    {{-- Tipo de solicitud --}}
                    <div>
                        <label for="tipo" class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/90 mb-2">
                            Tipo de solicitud
                        </label>
                        <select name="tipo" id="tipo"
                                class="block w-full rounded-2xl border border-white/30 bg-white/10 text-sm text-white
                                       px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            <option value="" class="bg-slate-800 text-slate-100">
                                Selecciona una opción
                            </option>
                            <option value="VACACIONES" {{ old('tipo') === 'VACACIONES' ? 'selected' : '' }} class="bg-slate-800 text-slate-100">
                                Vacaciones
                            </option>
                            <option value="ENFERMEDAD" {{ old('tipo') === 'ENFERMEDAD' ? 'selected' : '' }} class="bg-slate-800 text-slate-100">
                                Permiso por enfermedad
                            </option>
                            <option value="PERMISO" {{ old('tipo') === 'PERMISO' ? 'selected' : '' }} class="bg-slate-800 text-slate-100">
                                Otro permiso
                            </option>
                        </select>
                        @error('tipo')
                            <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fechas --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="fecha_inicio" class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/90 mb-2">
                                Fecha de inicio
                            </label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                   value="{{ old('fecha_inicio') }}"
                                   class="block w-full rounded-2xl border border-white/30 bg-white/10 text-sm text-white
                                          px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            @error('fecha_inicio')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fecha_fin" class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/90 mb-2">
                                Fecha de fin
                            </label>
                            <input type="date" name="fecha_fin" id="fecha_fin"
                                   value="{{ old('fecha_fin') }}"
                                   class="block w-full rounded-2xl border border-white/30 bg-white/10 text-sm text-white
                                          px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            @error('fecha_fin')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Motivo --}}
                    <div>
                        <label for="motivo" class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/90 mb-2">
                            Motivo (opcional)
                        </label>
                        <textarea name="motivo" id="motivo" rows="3"
                                  class="block w-full rounded-2xl border border-white/30 bg-white/10 text-sm text-white
                                         px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent"
                                  placeholder="Ejemplo: Vacaciones familiares, cita médica, trámite personal, etc.">{{ old('motivo') }}</textarea>
                        @error('motivo')
                            <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full border border-white/30
                                  text-[11px] font-medium text-slate-100 hover:bg-black/40 transition-all">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900
                                       text-xs md:text-sm font-semibold shadow-lg hover:bg-white transition-all">
                            Enviar solicitud
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection
