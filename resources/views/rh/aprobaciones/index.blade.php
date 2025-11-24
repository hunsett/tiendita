@extends('layouts.app')

@section('title', 'Aprobaciones RH – Vacaciones')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        {{-- Cabecera --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Aprobaciones de vacaciones (RH)
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Administra las solicitudes de vacaciones ya revisadas por el jefe directo.
                    Aquí se registra la decisión final y el ajuste de días.
                </p>
            </div>

            @if($solicitudes->count())
                <div class="flex flex-col items-end gap-2 text-[11px] text-slate-100/90">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-500/90 text-slate-900 font-semibold shadow">
                        Pendientes RH: {{ $solicitudes->total() }}
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

        {{-- Card principal glass --}}
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative text-slate-50">

                {{-- Encabezado + filtros --}}
                <div class="px-6 py-4 border-b border-white/20 space-y-4">
                    <div>
                        <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-white/80">
                            Bandeja de solicitudes pendientes de RH
                        </h2>
                        <p class="mt-1 text-xs text-slate-200/80">
                            Solo se muestran solicitudes en estado <strong>PENDIENTE</strong> con aprobación de jefe (nivel 1).
                        </p>
                    </div>

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
                            <a href="{{ route('rh.aprobaciones.index') }}"
                               class="inline-flex items-center px-4 py-2 rounded-full bg-black/40 text-slate-50 text-xs font-semibold border border-white/25 hover:bg-black/60 hover:-translate-y-0.5 transition-all">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                @if($solicitudes->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-black/25 text-[11px] uppercase tracking-wide text-slate-100/80">
                                    <th class="px-6 py-3 text-left font-semibold">Folio</th>
                                    <th class="px-6 py-3 text-left font-semibold">Colaborador</th>
                                    <th class="px-6 py-3 text-left font-semibold">Fechas</th>
                                    <th class="px-6 py-3 text-left font-semibold">Días sol.</th>
                                    <th class="px-6 py-3 text-left font-semibold">Tipo</th>
                                    <th class="px-6 py-3 text-left font-semibold">Estado</th>
                                    <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($solicitudes as $solicitud)
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
                                                <a href="{{ route('rh.aprobaciones.show', $solicitud->id_solicitud) }}"
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
                    <div class="px-6 py-10 text-center text-sm text-slate-100/90">
                        No hay solicitudes pendientes de RH por el momento.
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
