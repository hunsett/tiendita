@extends('layouts.app')

@section('title', 'Nuevo usuario')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-4xl mx-auto px-4">
        {{-- Cabecera --}}
        <div class="mb-6 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Nuevo usuario
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Crea un usuario del sistema ligado a un empleado de <span class="font-semibold">Tienda Mary</span>.
                </p>
            </div>
            <a href="{{ route('usuarios.index') }}"
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
                <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Empleado ligado --}}
                    <div>
                        <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                            Empleado
                        </label>
                        <select name="id_empleado"
                                class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                            <option value="" class="bg-slate-900">Selecciona un empleado</option>
                            @foreach($empleadosSinUsuario as $empleado)
                                <option value="{{ $empleado->id_empleado }}"
                                        @selected(old('id_empleado') == $empleado->id_empleado)
                                        class="bg-slate-900">
                                    {{ $empleado->nombre }} {{ $empleado->apellidos }} ({{ $empleado->correo }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_empleado')
                            <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Usuario + correo --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Usuario
                            </label>
                            <input type="text"
                                   name="usuario"
                                   value="{{ old('usuario') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent"
                                   required>
                            @error('usuario')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Correo del sistema
                            </label>
                            <input type="email"
                                   name="correo_sistema"
                                   value="{{ old('correo_sistema') }}"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent"
                                   required>
                            @error('correo_sistema')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Rol + estado --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Rol
                            </label>
                            <select name="rol"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                @foreach(['ADMIN','RH','JEFE','EMPLEADO'] as $r)
                                    <option value="{{ $r }}"
                                        @selected(old('rol', 'EMPLEADO') === $r)
                                        class="bg-slate-900">
                                        {{ $r }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rol')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Estado
                            </label>
                            <select name="estado"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="ACTIVO" @selected(old('estado', 'ACTIVO') === 'ACTIVO') class="bg-slate-900">
                                    ACTIVO
                                </option>
                                <option value="BLOQUEADO" @selected(old('estado') === 'BLOQUEADO') class="bg-slate-900">
                                    BLOQUEADO
                                </option>
                            </select>
                            @error('estado')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Contraseña
                            </label>
                            <input type="password"
                                   name="password"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent"
                                   required>
                            @error('password')
                                <p class="mt-1 text-[11px] text-rose-200">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                                Confirmar contraseña
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent"
                                   required>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('usuarios.index') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full border border-white/30 text-[11px] font-medium text-slate-100 hover:bg-black/40 transition-all">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs md:text-sm font-semibold shadow-lg hover:bg-white transition-all">
                            Guardar usuario
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
