@extends('layouts.app')

@section('title', 'Editar departamento')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-3xl mx-auto px-4">
        {{-- Cabecera --}}
        <div class="mb-6">
            <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                Editar departamento
            </h1>
            <p class="mt-2 text-sm text-slate-300 max-w-xl">
                Actualiza la información del departamento seleccionado. Los cambios se reflejarán
                en todos los empleados asociados.
            </p>
        </div>

        {{-- Card glass con degradado --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            {{-- Degradado de fondo --}}
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            {{-- Oscurecimiento --}}
            <div class="absolute inset-0 bg-black/35"></div>
            {{-- Blur tipo glass --}}
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            {{-- Contenido --}}
            <div class="relative px-6 py-7 text-slate-50">
                <form action="{{ route('departamentos.update', $departamento) }}" method="POST" class="max-w-md">
                    @csrf
                    @method('PUT')

                    <div class="mb-5">
                        <label for="nombre"
                               class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                            Nombre del departamento
                        </label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm
                                   text-white placeholder-slate-300 focus:outline-none focus:ring-2
                                   focus:ring-white/70 focus:border-transparent
                                   @error('nombre') border-rose-400 ring-rose-300/70 @enderror"
                            value="{{ old('nombre', $departamento->nombre) }}"
                            required
                        >

                        @error('nombre')
                            <p class="mt-1 text-xs text-rose-200">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 mt-6">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900
                                       text-xs font-semibold shadow-lg hover:bg-white transition-all">
                            Actualizar
                        </button>

                        <a href="{{ route('departamentos.index') }}"
                           class="inline-flex items-center px-4 py-2 rounded-full bg-black/40 text-slate-100
                                  text-xs font-semibold border border-white/20 hover:bg-black/60 transition-all">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
