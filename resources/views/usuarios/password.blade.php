@extends('layouts.app')

@section('title', $modo === 'admin' ? 'Reset de contraseña' : 'Cambiar contraseña')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-md mx-auto px-4">
        {{-- Cabecera --}}
        <div class="mb-6 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    {{ $modo === 'admin' ? 'Reset de contraseña' : 'Cambiar contraseña' }}
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    @if($modo === 'admin')
                        Establece una nueva contraseña para el usuario
                        <span class="font-semibold">{{ $usuario->usuario }}</span>.
                    @else
                        Establece tu nueva contraseña de acceso al sistema.
                    @endif
                </p>
            </div>
            <a href="{{ $modo === 'admin' ? route('usuarios.index') : route('dashboard') }}"
               class="text-xs text-slate-300 hover:text-white hover:underline mt-1">
                ← Volver
            </a>
        </div>

        {{-- Card --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative px-6 py-7 text-slate-50">
                <form action="{{ $modo === 'admin'
                        ? route('usuarios.password.update', $usuario)
                        : route('mi-cuenta.password.update') }}"
                      method="POST"
                      class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold tracking-[0.18em] uppercase text-slate-100/80 mb-2">
                            Nueva contraseña
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

                    <div class="text-[11px] text-slate-100/80">
                        La contraseña debe tener al menos 8 caracteres.
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ $modo === 'admin' ? route('usuarios.index') : route('dashboard') }}"
                           class="inline-flex items-center px-3 py-2 rounded-full border border-white/30 text-[11px] font-medium text-slate-100 hover:bg-black/40 transition-all">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs md:text-sm font-semibold shadow-lg hover:bg-white transition-all">
                            Guardar contraseña
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
