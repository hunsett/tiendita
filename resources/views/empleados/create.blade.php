@extends('layouts.app')

@section('title', 'Nuevo empleado')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-4xl mx-auto px-4">
        {{-- Cabecera --}}
        <div class="mb-6 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Nuevo empleado
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Registra un nuevo empleado en <span class="font-semibold">Tienda Mary</span>.
                </p>
            </div>
            <a href="{{ route('empleados.index') }}"
               class="text-xs text-slate-300 hover:text-white hover:underline mt-1">
                ← Volver al listado
            </a>
        </div>

        {{-- Card --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative px-6 py-7 text-slate-50">
                <form action="{{ route('empleados.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Datos básicos --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Código (opcional)
                            </label>
                            <input type="number" name="codigo" value="{{ old('codigo') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            @error('codigo')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Estado
                            </label>
                            <select name="estado"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="ACTIVO" {{ old('estado', 'ACTIVO') === 'ACTIVO' ? 'selected' : '' }} class="bg-slate-900">
                                    ACTIVO
                                </option>
                                <option value="INACTIVO" {{ old('estado') === 'INACTIVO' ? 'selected' : '' }} class="bg-slate-900">
                                    INACTIVO
                                </option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Nombre --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Nombre
                            </label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent" required>
                            @error('nombre')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Apellidos
                            </label>
                            <input type="text" name="apellidos" value="{{ old('apellidos') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent" required>
                            @error('apellidos')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Documentos --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                CURP
                            </label>
                            <input type="text" name="curp" value="{{ old('curp') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent" required>
                            @error('curp')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                RFC
                            </label>
                            <input type="text" name="rfc" value="{{ old('rfc') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent" required>
                            @error('rfc')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                NSS
                            </label>
                            <input type="text" name="nss" value="{{ old('nss') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent" required>
                            @error('nss')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Correo
                            </label>
                            <input type="email" name="correo" value="{{ old('correo') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent" required>
                            @error('correo')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Datos contacto / fechas --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Teléfono
                            </label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            @error('telefono')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Fecha de nacimiento
                            </label>
                            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            @error('fecha_nacimiento')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Fecha de ingreso
                            </label>
                            <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            @error('fecha_ingreso')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Relación organizacional --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Departamento
                            </label>
                            <select name="id_departamento"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="" class="bg-slate-900">Sin departamento</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id_departamento }}" @selected(old('id_departamento') == $depto->id_departamento) class="bg-slate-900">
                                        {{ $depto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_departamento')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Puesto
                            </label>
                            <select name="id_puesto"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="" class="bg-slate-900">Sin puesto</option>
                                @foreach($puestos as $p)
                                    <option value="{{ $p->id_puesto }}" @selected(old('id_puesto') == $p->id_puesto) class="bg-slate-900">
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_puesto')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('empleados.index') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full border border-white/30 text-[11px] font-medium text-slate-100 hover:bg-black/40 transition-all">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs md:text-sm font-semibold shadow-lg hover:bg-white transition-all">
                            Guardar empleado
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
