@extends('layouts.app')

@section('title', 'Departamentos')

@section('content')
{{-- Fondo oscuro plano --}}
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        {{-- Cabecera --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Departamentos
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Administra los departamentos de <span class="font-semibold">Tienda Mary</span>.
                    Mantener esta lista actualizada te ayudará a organizar mejor a tus empleados y sus vacaciones.
                </p>
            </div>

            <a href="{{ route('departamentos.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-full bg-white/90 text-slate-900 text-sm font-semibold shadow-lg hover:bg-white hover:-translate-y-0.5 transition-all">
                + Nuevo departamento
            </a>
        </div>

        {{-- Card principal con degradado tipo imagen --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">

            {{-- Capa de degradado (gris -> blanco -> azul) --}}
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>

            {{-- Capa de oscurecimiento suave --}}
            <div class="absolute inset-0 bg-black/30"></div>

            {{-- Capa de blur tipo glass --}}
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            {{-- Contenido real --}}
            <div class="relative text-slate-50">

                {{-- Encabezado de la tabla --}}
                <div class="px-6 py-4 border-b border-white/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-white/80">
                            Listado de departamentos
                        </h2>
                        <p class="mt-1 text-xs text-slate-200/80">
                            Visualiza, edita o elimina los departamentos registrados en el sistema.
                        </p>
                    </div>
                    @if($departamentos->count())
                        <div class="flex items-center gap-2 text-[11px] text-slate-100/90">
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-black/30 text-white font-semibold">
                                {{ $departamentos->total() }} en total
                            </span>
                            <span class="hidden sm:inline">
                                Página {{ $departamentos->currentPage() }} de {{ $departamentos->lastPage() }}
                            </span>
                        </div>
                    @endif
                </div>

                @if ($departamentos->count())
                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-black/25 text-[11px] uppercase tracking-wide text-slate-100/80">
                                    <th class="px-6 py-3 text-left font-semibold">#</th>
                                    <th class="px-6 py-3 text-left font-semibold">Nombre</th>
                                    <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($departamentos as $departamento)
                                    <tr class="hover:bg-black/20 transition-colors">
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $departamento->id_departamento }}
                                        </td>
                                        <td class="px-6 py-3 text-sm font-medium text-white">
                                            {{ $departamento->nombre }}
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('departamentos.edit', $departamento) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-white/90 text-slate-900 hover:bg-white transition-colors shadow-sm">
                                                    Editar
                                                </a>

                                                <form action="{{ route('departamentos.destroy', $departamento) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('¿Seguro que deseas eliminar este departamento?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-rose-500/95 text-white hover:bg-rose-400 transition-colors shadow-sm">
                                                        Eliminar
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
                                    {{ $departamentos->firstItem() }}–{{ $departamentos->lastItem() }}
                                </span>
                                de
                                <span class="font-semibold text-white">
                                    {{ $departamentos->total() }}
                                </span>
                                registros
                            </div>
                            <div class="text-xs">
                                {{ $departamentos->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Sin registros --}}
                    <div class="px-6 py-10 text-center text-sm text-slate-100/90">
                        No hay departamentos registrados todavía.
                        <div class="mt-3">
                            <a href="{{ route('departamentos.create') }}"
                               class="inline-flex items-center px-4 py-2 rounded-full bg-white/90 text-slate-900 text-xs font-semibold shadow hover:bg-white transition-colors">
                                Crear el primer departamento
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
