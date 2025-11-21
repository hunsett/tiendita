@extends('layouts.app')

@section('title', 'Detalle de empleado')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-4xl mx-auto px-4 space-y-6">

        {{-- Cabecera --}}
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    {{ $empleado->nombre }} {{ $empleado->apellidos }}
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Detalle del empleado y su información básica en <span class="font-semibold">Tienda Mary</span>.
                </p>
            </div>
            <a href="{{ route('empleados.index') }}"
               class="text-xs text-slate-300 hover:text-white hover:underline mt-1">
                ← Volver al listado
            </a>
        </div>

        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative px-6 py-7 text-slate-50 space-y-6">

                {{-- Estado y acciones --}}
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-semibold
                            @if($empleado->estado === 'ACTIVO')
                                bg-emerald-500/90 text-white
                            @else
                                bg-rose-500/90 text-white
                            @endif">
                            {{ $empleado->estado }}
                        </span>
                        @if($empleado->departamento)
                            <span class="text-xs text-slate-100/90">
                                {{ $empleado->departamento->nombre }}
                            </span>
                        @endif
                        @if($empleado->puesto)
                            <span class="text-xs text-slate-100/90">
                                • {{ $empleado->puesto->nombre }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('empleados.edit', $empleado) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-white/90 text-slate-900 hover:bg-white transition-colors shadow-sm">
                            Editar
                        </a>

                        <form action="{{ route('empleados.toggle-estado', $empleado) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold
                                        @if($empleado->estado === 'ACTIVO')
                                            bg-rose-500/95 text-white hover:bg-rose-400
                                        @else
                                            bg-emerald-500/95 text-white hover:bg-emerald-400
                                        @endif
                                        transition-colors shadow-sm">
                                {{ $empleado->estado === 'ACTIVO' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Info básica --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                    <div class="space-y-2">
                        <h2 class="text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80">
                            Información general
                        </h2>
                        <p><span class="font-semibold">Código:</span> {{ $empleado->codigo ?? '—' }}</p>
                        <p><span class="font-semibold">Correo:</span> {{ $empleado->correo }}</p>
                        <p><span class="font-semibold">Teléfono:</span> {{ $empleado->telefono ?? '—' }}</p>
                        <p><span class="font-semibold">Fecha de nacimiento:</span>
                            {{ $empleado->fecha_nacimiento ? $empleado->fecha_nacimiento->format('d/m/Y') : '—' }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80">
                            Datos laborales
                        </h2>
                        <p><span class="font-semibold">Fecha de ingreso:</span>
                            {{ $empleado->fecha_ingreso ? $empleado->fecha_ingreso->format('d/m/Y') : '—' }}
                        </p>
                        <p><span class="font-semibold">Departamento:</span>
                            {{ $empleado->departamento->nombre ?? 'Sin departamento' }}
                        </p>
                        <p><span class="font-semibold">Puesto:</span>
                            {{ $empleado->puesto->nombre ?? 'Sin puesto' }}
                        </p>
                    </div>
                </div>

                {{-- Documentos --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-sm">
                    <div>
                        <h2 class="text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-1">
                            CURP
                        </h2>
                        <p>{{ $empleado->curp }}</p>
                    </div>
                    <div>
                        <h2 class="text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-1">
                            RFC
                        </h2>
                        <p>{{ $empleado->rfc }}</p>
                    </div>
                    <div>
                        <h2 class="text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-1">
                            NSS
                        </h2>
                        <p>{{ $empleado->nss }}</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
