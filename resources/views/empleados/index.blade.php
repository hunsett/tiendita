@extends('layouts.app')

@section('title', 'Empleados')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-7xl mx-auto px-4 space-y-6">

        {{-- Cabecera --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Empleados
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Administra los empleados registrados en <span class="font-semibold">Tienda Mary</span>.
                    Usa filtros para encontrar rápidamente a la persona que buscas.
                </p>
            </div>

            <a href="{{ route('empleados.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-full bg-white/90 text-slate-900 text-sm font-semibold shadow-lg hover:bg-white hover:-translate-y-0.5 transition-all">
                + Nuevo empleado
            </a>
        </div>

        {{-- Card principal --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            {{-- Fondo degradado --}}
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative text-slate-50">

                {{-- Encabezado + filtros --}}
                <div class="px-6 py-4 border-b border-white/20 space-y-4">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-white/80">
                                Listado de empleados
                            </h2>
                            <p class="mt-1 text-xs text-slate-200/80">
                                Visualiza, filtra y gestiona el estado de los empleados.
                            </p>
                        </div>

                        @if($empleados->count())
                            <div class="flex items-center gap-2 text-[11px] text-slate-100/90">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-black/30 text-white font-semibold">
                                    {{ $empleados->total() }} en total
                                </span>
                                <span class="hidden sm:inline">
                                    Página {{ $empleados->currentPage() }} de {{ $empleados->lastPage() }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Filtros --}}
                    <form method="GET" action="{{ route('empleados.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 text-[11px]">
                        <div>
                            <label class="block mb-1 text-slate-100/80 uppercase tracking-[0.18em]">
                                Búsqueda
                            </label>
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Nombre, correo, código, CURP..."
                                   class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-xs text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block mb-1 text-slate-100/80 uppercase tracking-[0.18em]">
                                Estado
                            </label>
                            <select name="estado"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="" class="bg-slate-900">Todos</option>
                                <option value="ACTIVO" {{ $estado === 'ACTIVO' ? 'selected' : '' }} class="bg-slate-900">
                                    Activo
                                </option>
                                <option value="INACTIVO" {{ $estado === 'INACTIVO' ? 'selected' : '' }} class="bg-slate-900">
                                    Inactivo
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-slate-100/80 uppercase tracking-[0.18em]">
                                Departamento
                            </label>
                            <select name="id_departamento"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="" class="bg-slate-900">Todos</option>
                                @foreach($departamentos as $depto)
                                    <option value="{{ $depto->id_departamento }}"
                                        @selected($departamentoId == $depto->id_departamento)
                                        class="bg-slate-900">
                                        {{ $depto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-slate-100/80 uppercase tracking-[0.18em]">
                                Puesto
                            </label>
                            <select name="id_puesto"
                                    class="w-full rounded-2xl border border-white/30 bg-white/10 px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-white/70 focus:border-transparent">
                                <option value="" class="bg-slate-900">Todos</option>
                                @foreach($puestos as $p)
                                    <option value="{{ $p->id_puesto }}"
                                        @selected($puestoId == $p->id_puesto)
                                        class="bg-slate-900">
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4 flex items-center justify-end gap-2 pt-1">
                            <a href="{{ route('empleados.index') }}"
                               class="inline-flex items-center px-3 py-2 rounded-full border border-white/30 text-[11px] font-medium text-slate-100 hover:bg-black/40 transition-all">
                                Limpiar filtros
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs font-semibold shadow-lg hover:bg-white transition-all">
                                Aplicar filtros
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tabla --}}
                @if($empleados->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-black/25 text-[11px] uppercase tracking-wide text-slate-100/80">
                                    <th class="px-6 py-3 text-left font-semibold">Código</th>
                                    <th class="px-6 py-3 text-left font-semibold">Empleado</th>
                                    <th class="px-6 py-3 text-left font-semibold">Departamento</th>
                                    <th class="px-6 py-3 text-left font-semibold">Puesto</th>
                                    <th class="px-6 py-3 text-left font-semibold">Estado</th>
                                    <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($empleados as $empleado)
                                    <tr class="hover:bg-black/20 transition-colors">
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $empleado->codigo ?? '—' }}
                                        </td>
                                        <td class="px-6 py-3 text-sm font-medium text-white">
                                            <a href="{{ route('empleados.show', $empleado) }}"
                                               class="hover:underline">
                                                {{ $empleado->nombre }} {{ $empleado->apellidos }}
                                            </a>
                                            <div class="text-[11px] text-slate-200/80">
                                                {{ $empleado->correo }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $empleado->departamento->nombre ?? 'Sin depto.' }}
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $empleado->puesto->nombre ?? 'Sin puesto' }}
                                        </td>
                                        <td class="px-6 py-3 text-xs">
                                            <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-semibold
                                                @if($empleado->estado === 'ACTIVO')
                                                    bg-emerald-500/90 text-white
                                                @else
                                                    bg-rose-500/90 text-white
                                                @endif">
                                                {{ $empleado->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('empleados.edit', $empleado) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-white/90 text-slate-900 hover:bg-white transition-colors shadow-sm">
                                                    Editar
                                                </a>

                                                <form action="{{ route('empleados.toggle-estado', $empleado) }}"
                                                      method="POST">
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
                                    {{ $empleados->firstItem() }}–{{ $empleados->lastItem() }}
                                </span>
                                de
                                <span class="font-semibold text-white">
                                    {{ $empleados->total() }}
                                </span>
                                registros
                            </div>
                            <div class="text-xs">
                                {{ $empleados->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="px-6 py-10 text-center text-sm text-slate-100/90">
                        No hay empleados registrados todavía.
                        <div class="mt-3">
                            <a href="{{ route('empleados.create') }}"
                               class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs font-semibold shadow hover:bg-white transition-colors">
                                Crear el primer empleado
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
