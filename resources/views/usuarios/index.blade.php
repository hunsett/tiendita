@extends('layouts.app')

@section('title', 'Usuarios del sistema')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-7xl mx-auto px-4 space-y-6">

        {{-- Cabecera --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Usuarios del sistema
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Administra los usuarios que pueden acceder a <span class="font-semibold">Tienda Mary</span>,
                    sus roles y su estado de acceso.
                </p>
            </div>

            <a href="{{ route('usuarios.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-full bg-white/90 text-slate-900 text-sm font-semibold shadow-lg hover:bg-white hover:-translate-y-0.5 transition-all">
                + Nuevo usuario
            </a>
        </div>

        {{-- Card principal --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            {{-- Fondo gradient + glass --}}
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative text-slate-50">

                {{-- Encabezado + filtros --}}
                <div class="px-6 py-4 border-b border-white/20 space-y-4">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-white/80">
                                Listado de usuarios
                            </h2>
                            <p class="mt-1 text-xs text-slate-200/80">
                                Visualiza, filtra y gestiona los usuarios que tienen acceso al sistema.
                            </p>
                        </div>

                        @if($usuarios->count())
                            <div class="flex items-center gap-2 text-[11px] text-slate-100/90">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-black/30 text-white font-semibold">
                                    {{ $usuarios->total() }} en total
                                </span>
                                <span class="hidden sm:inline">
                                    Página {{ $usuarios->currentPage() }} de {{ $usuarios->lastPage() }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Filtros --}}
                    <form method="GET" action="{{ route('usuarios.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 text-[11px]">
                        <div>
                            <label class="block mb-1 text-slate-100/80 uppercase tracking-[0.18em]">
                                Búsqueda
                            </label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Usuario, correo, nombre empleado..."
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-xs text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block mb-1 text-slate-100/80 uppercase tracking-[0.18em]">
                                Rol
                            </label>
                            <select name="rol"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="" class="bg-slate-900">Todos</option>
                                @foreach(['ADMIN','RH','JEFE','EMPLEADO'] as $r)
                                    <option value="{{ $r }}" @selected($rol === $r) class="bg-slate-900">
                                        {{ $r }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-slate-100/80 uppercase tracking-[0.18em]">
                                Estado
                            </label>
                            <select name="estado"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="" class="bg-slate-900">Todos</option>
                                <option value="ACTIVO" @selected($estado === 'ACTIVO') class="bg-slate-900">
                                    ACTIVO
                                </option>
                                <option value="BLOQUEADO" @selected($estado === 'BLOQUEADO') class="bg-slate-900">
                                    BLOQUEADO
                                </option>
                            </select>
                        </div>

                        <div class="md:col-span-1 flex items-end justify-end gap-2">
                            <a href="{{ route('usuarios.index') }}"
                               class="inline-flex items-center px-3 py-2 rounded-full border border-white/30 text-[11px] font-medium text-slate-100 hover:bg-black/40 transition-all">
                                Limpiar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs font-semibold shadow-lg hover:bg-white transition-all">
                                Filtrar
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tabla --}}
                @if($usuarios->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-black/25 text-[11px] uppercase tracking-wide text-slate-100/80">
                                    <th class="px-6 py-3 text-left font-semibold">Usuario</th>
                                    <th class="px-6 py-3 text-left font-semibold">Empleado</th>
                                    <th class="px-6 py-3 text-left font-semibold">Rol</th>
                                    <th class="px-6 py-3 text-left font-semibold">Estado</th>
                                    <th class="px-6 py-3 text-left font-semibold">Último acceso</th>
                                    <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($usuarios as $usuario)
                                    <tr class="hover:bg-black/20 transition-colors">
                                        <td class="px-6 py-3 text-sm font-medium text-white">
                                            {{ $usuario->usuario }}
                                            <div class="text-[11px] text-slate-200/80">
                                                {{ $usuario->correo_sistema }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            @if($usuario->empleado)
                                                {{ $usuario->empleado->nombre }} {{ $usuario->empleado->apellidos }}
                                            @else
                                                <span class="italic text-slate-200/70">Sin empleado</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $usuario->rol }}
                                        </td>
                                        <td class="px-6 py-3 text-xs">
                                            <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-semibold
                                                @if($usuario->estado === 'ACTIVO')
                                                    bg-emerald-500/90 text-white
                                                @else
                                                    bg-rose-500/90 text-white
                                                @endif">
                                                {{ $usuario->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            @if($usuario->ultimo_acceso)
                                                {{ $usuario->ultimo_acceso->format('d/m/Y H:i') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-white/90 text-slate-900 hover:bg-white transition-colors shadow-sm">
                                                    Editar
                                                </a>

                                                <a href="{{ route('usuarios.password.edit', $usuario) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-sky-500/95 text-white hover:bg-sky-400 transition-colors shadow-sm">
                                                    Reset contraseña
                                                </a>

                                                <form action="{{ route('usuarios.toggle-estado', $usuario) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold
                                                                @if($usuario->estado === 'ACTIVO')
                                                                    bg-rose-500/95 text-white hover:bg-rose-400
                                                                @else
                                                                    bg-emerald-500/95 text-white hover:bg-emerald-400
                                                                @endif
                                                                transition-colors shadow-sm">
                                                        {{ $usuario->estado === 'ACTIVO' ? 'Bloquear' : 'Desbloquear' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="px-6 py-4 border-t border-white/15 bg-black/25">
                        <div class="flex items-center justify-between">
                            <div class="text-[11px] text-slate-100/90">
                                Mostrando
                                <span class="font-semibold text-white">
                                    {{ $usuarios->firstItem() }}–{{ $usuarios->lastItem() }}
                                </span>
                                de
                                <span class="font-semibold text-white">
                                    {{ $usuarios->total() }}
                                </span>
                                registros
                            </div>
                            <div class="text-xs">
                                {{ $usuarios->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="px-6 py-10 text-center text-sm text-slate-100/90">
                        No hay usuarios registrados todavía.
                        <div class="mt-3">
                            <a href="{{ route('usuarios.create') }}"
                               class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs font-semibold shadow hover:bg-white transition-colors">
                                Crear el primer usuario
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
