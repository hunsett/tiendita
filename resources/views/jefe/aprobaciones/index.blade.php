{{-- resources/views/jefe/aprobaciones/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Aprobaciones de vacaciones – Jefe')

@section('content')
{{-- Fondo oscuro plano, igual que Departamentos --}}
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        {{-- Cabecera --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Aprobaciones de vacaciones
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Revisa y decide sobre las solicitudes de vacaciones de tu equipo
                    en <span class="font-semibold">Tienda Mary</span>.
                </p>
            </div>

            @if($solicitudes->count())
                <div class="flex flex-col items-end gap-2 text-[11px] text-slate-100/90">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-500/90 text-slate-900 font-semibold shadow">
                        Pendientes: {{ $solicitudes->total() }}
                    </span>
                    <span class="hidden sm:inline text-slate-300">
                        Página {{ $solicitudes->currentPage() }} de {{ $solicitudes->lastPage() }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="p-3 rounded-xl text-sm text-emerald-100 bg-emerald-700/70 border border-emerald-400 shadow">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 rounded-xl text-sm text-red-100 bg-red-700/70 border border-red-400 shadow">
                <ul class="mb-0 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Card principal con degradado tipo imagen (igual patrón que Departamentos) --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">

            {{-- Capa de degradado (gris -> blanco -> azul/verde) --}}
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-emerald-600"></div>

            {{-- Capa de oscurecimiento suave --}}
            <div class="absolute inset-0 bg-black/35"></div>

            {{-- Capa de blur tipo glass --}}
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            {{-- Contenido real --}}
            <div class="relative text-slate-50">

                {{-- Encabezado de la tabla + filtros --}}
                <div class="px-6 py-4 border-b border-white/20 space-y-4">

                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-white/80">
                                Solicitudes pendientes de tu equipo
                            </h2>
                            <p class="mt-1 text-xs text-slate-200/80">
                                Filtra por colaborador o por rango de fechas y revisa cada solicitud antes de aprobar o rechazar.
                            </p>
                        </div>
                    </div>

                    {{-- Filtros --}}
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs md:text-sm">
                        <div class="md:col-span-2">
                            <label class="block text-slate-100/90 mb-1 uppercase tracking-wide text-[0.7rem]">
                                Buscar colaborador
                            </label>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Nombre, apellidos o código"
                                   class="w-full rounded-full bg-black/30 border border-white/20 text-slate-50 px-3 py-2 text-sm
                                          placeholder:text-slate-300/70 focus:outline-none focus:ring-2 focus:ring-cyan-300/80 focus:border-cyan-300">
                        </div>
                        <div>
                            <label class="block text-slate-100/90 mb-1 uppercase tracking-wide text-[0.7rem]">
                                Desde
                            </label>
                            <input type="date" name="desde" value="{{ request('desde') }}"
                                   class="w-full rounded-full bg-black/30 border border-white/20 text-slate-50 px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-cyan-300/80 focus:border-cyan-300">
                        </div>
                        <div>
                            <label class="block text-slate-100/90 mb-1 uppercase tracking-wide text-[0.7rem]">
                                Hasta
                            </label>
                            <input type="date" name="hasta" value="{{ request('hasta') }}"
                                   class="w-full rounded-full bg-black/30 border border-white/20 text-slate-50 px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-cyan-300/80 focus:border-cyan-300">
                        </div>

                        <div class="md:col-span-4 flex flex-wrap items-center gap-3 mt-1">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-full bg-sky-500/95 text-slate-900 text-xs font-semibold shadow hover:bg-sky-400 hover:-translate-y-0.5 transition-all">
                                Filtrar
                            </button>
                            <a href="{{ route('jefe.aprobaciones.index') }}"
                               class="inline-flex items-center px-4 py-2 rounded-full bg-black/40 text-slate-50 text-xs font-semibold border border-white/25 hover:bg-black/60 hover:-translate-y-0.5 transition-all">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                @if ($solicitudes->count())
                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-black/25 text-[11px] uppercase tracking-wide text-slate-100/80">
                                    <th class="px-6 py-3 text-left font-semibold">Folio</th>
                                    <th class="px-6 py-3 text-left font-semibold">Colaborador</th>
                                    <th class="px-6 py-3 text-left font-semibold">Fechas</th>
                                    <th class="px-6 py-3 text-left font-semibold">Días</th>
                                    <th class="px-6 py-3 text-left font-semibold">Tipo</th>
                                    <th class="px-6 py-3 text-left font-semibold">Estado</th>
                                    <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($solicitudes as $solicitud)
                                    <tr class="hover:bg-black/20 transition-colors">
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            #{{ $solicitud->id_solicitud }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-white">
                                            <div class="font-medium">
                                                {{ $solicitud->empleado->nombre }} {{ $solicitud->empleado->apellidos }}
                                            </div>
                                            <div class="text-[11px] text-slate-100/80">
                                                Código: {{ $solicitud->empleado->codigo ?? '—' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $solicitud->fecha_inicio->format('d/m/Y') }}
                                            –
                                            {{ $solicitud->fecha_fin->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $solicitud->dias_solicitados }}
                                        </td>
                                        <td class="px-6 py-3 text-xs">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-sky-500/90 text-slate-900 font-semibold">
                                                {{ $solicitud->tipo }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-xs">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-amber-400/95 text-slate-900 font-semibold">
                                                {{ $solicitud->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-end">
                                                <a href="{{ route('jefe.aprobaciones.show', $solicitud->id_solicitud) }}"
                                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-white/90 text-slate-900 hover:bg-white transition-colors shadow-sm">
                                                    Ver detalle
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    <div class="px-6 py-4 border-t border-white/15 bg-black/25">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-2">
                            <div class="text-[11px] text-slate-100/90">
                                Mostrando
                                <span class="font-semibold text-white">
                                    {{ $solicitudes->firstItem() }}–{{ $solicitudes->lastItem() }}
                                </span>
                                de
                                <span class="font-semibold text-white">
                                    {{ $solicitudes->total() }}
                                </span>
                                solicitudes
                            </div>
                            <div class="text-xs">
                                {{ $solicitudes->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Sin registros --}}
                    <div class="px-6 py-10 text-center text-sm text-slate-100/90">
                        No hay solicitudes pendientes de tu equipo por el momento.
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
