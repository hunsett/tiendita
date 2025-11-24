@extends('layouts.app')

@section('title', 'Detalle solicitud – RH')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-4xl mx-auto px-4 space-y-6">

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('rh.aprobaciones.index') }}"
               class="text-xs text-slate-300 hover:text-white transition-colors">
                ← Volver a bandeja RH
            </a>
        </div>

        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-emerald-600"></div>
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative text-slate-50">

                {{-- Encabezado --}}
                <div class="px-6 py-4 border-b border-white/20 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-semibold tracking-tight">
                            Solicitud #{{ $solicitud->id_solicitud }}
                        </h1>
                        <p class="mt-1 text-xs text-slate-200/80">
                            Del colaborador {{ $solicitud->empleado->nombre }} {{ $solicitud->empleado->apellidos }}.
                        </p>
                    </div>
                    <div class="text-right text-xs space-y-1">
                        <div>
                            Estado:
                            <span class="inline-flex items-center px-2 py-1 rounded-full
                                @class([
                                    'bg-amber-400/95 text-slate-900 font-semibold' => $solicitud->estado === 'PENDIENTE',
                                    'bg-emerald-400/95 text-slate-900 font-semibold' => $solicitud->estado === 'APROBADA',
                                    'bg-rose-500/95 text-white font-semibold' => $solicitud->estado === 'RECHAZADA',
                                    'bg-slate-500/90 text-slate-900 font-semibold' => in_array($solicitud->estado, ['BORRADOR','CANCELADA']),
                                ])
                            ">
                                {{ $solicitud->estado }}
                            </span>
                        </div>
                        <div class="text-[0.7rem] text-slate-100/80">
                            Tipo: <span class="font-semibold">{{ $solicitud->tipo }}</span><br>
                            Creada: {{ $solicitud->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                {{-- Contenido --}}
                <div class="px-6 py-5 space-y-5">

                    {{-- Bloque de fechas y días --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                Fechas
                            </span>
                            <div class="text-slate-50">
                                {{ $solicitud->fecha_inicio->format('d/m/Y') }}
                                –
                                {{ $solicitud->fecha_fin->format('d/m/Y') }}
                            </div>
                        </div>
                        <div>
                            <span class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                Días solicitados
                            </span>
                            <div class="text-slate-50">
                                {{ $solicitud->dias_solicitados }}
                            </div>
                        </div>
                        <div>
                            <span class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                Enviada por el colaborador
                            </span>
                            <div class="text-slate-50">
                                {{ $solicitud->enviada_en ? $solicitud->enviada_en->format('d/m/Y H:i') : '—' }}
                            </div>
                        </div>
                    </div>

                    {{-- Motivo del colaborador --}}
                    <div class="text-sm">
                        <span class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                            Motivo del colaborador
                        </span>
                        <div class="bg-black/30 border border-white/20 rounded-xl px-3 py-2 text-slate-50 text-sm">
                            {{ $solicitud->motivo ?: 'Sin motivo capturado.' }}
                        </div>
                    </div>

                    {{-- Traza de aprobaciones --}}
                    <div class="text-sm">
                        <span class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-2">
                            Traza de aprobaciones
                        </span>

                        @if($solicitud->aprobaciones->isEmpty())
                            <p class="text-xs text-slate-100/80">
                                No hay aprobaciones registradas todavía.
                            </p>
                        @else
                            <ul class="space-y-2 text-xs">
                                @foreach($solicitud->aprobaciones->sortBy('accion_en') as $ap)
                                    <li class="bg-black/30 border border-white/20 rounded-xl px-3 py-2">
                                        <div class="flex justify-between gap-3">
                                            <div>
                                                <span class="font-semibold">
                                                    Nivel {{ $ap->nivel }} – {{ $ap->accion }}
                                                </span>
                                                @if($ap->aprobador)
                                                    <span class="text-slate-200">
                                                        · {{ $ap->aprobador->usuario }}
                                                    </span>
                                                    @if($ap->aprobador->empleado)
                                                        <span class="text-slate-300">
                                                            ({{ $ap->aprobador->empleado->nombre }} {{ $ap->aprobador->empleado->apellidos }})
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                            <span class="text-slate-300">
                                                {{ $ap->accion_en ? $ap->accion_en->format('d/m/Y H:i') : '—' }}
                                            </span>
                                        </div>
                                        @if($ap->comentario)
                                            <div class="mt-1 text-slate-100">
                                                Comentario: {{ $ap->comentario }}
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Formulario de decisión RH --}}
                    @if($solicitud->estado === 'PENDIENTE')
                        <div class="pt-4 border-t border-white/20 mt-4">
                            <h2 class="text-sm font-semibold text-slate-50 mb-2">
                                Decisión de Recursos Humanos
                            </h2>

                            <form method="POST" action="{{ route('rh.aprobaciones.decidir', $solicitud->id_solicitud) }}"
                                  class="space-y-3 text-sm">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-1">
                                        <label class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                            Días aprobados
                                        </label>
                                        <input type="number"
                                               name="dias_aprobados"
                                               step="0.5"
                                               min="0.5"
                                               max="{{ $solicitud->dias_solicitados }}"
                                               value="{{ old('dias_aprobados', $solicitud->dias_solicitados) }}"
                                               class="w-full rounded-xl bg-black/30 border border-white/20 text-slate-50 px-3 py-2 text-sm
                                                      focus:outline-none focus:ring-2 focus:ring-emerald-300/80 focus:border-emerald-300">
                                        <p class="text-[0.7rem] text-slate-200 mt-1">
                                            Por defecto se toman los {{ $solicitud->dias_solicitados }} días, pero puedes ajustar si aplica.
                                        </p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                            Comentario
                                        </label>
                                        <textarea name="comentario" rows="3"
                                                  class="w-full rounded-xl bg-black/30 border border-white/20 text-slate-50 px-3 py-2 text-sm
                                                         focus:outline-none focus:ring-2 focus:ring-emerald-300/80 focus:border-emerald-300"
                                                  placeholder="Explica brevemente tu decisión. Obligatorio en caso de rechazo.">{{ old('comentario') }}</textarea>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mt-3">
                                    <p class="text-xs text-slate-200">
                                        Al aprobar se actualizará el saldo de vacaciones del colaborador con los días aprobados.
                                    </p>
                                    <div class="flex gap-3 justify-end">
                                        <button type="submit" name="accion" value="RECHAZA"
                                                class="inline-flex items-center px-4 py-2 rounded-full bg-rose-500/95 text-white text-xs font-semibold shadow hover:bg-rose-400 hover:-translate-y-0.5 transition-all">
                                            Rechazar
                                        </button>
                                        <button type="submit" name="accion" value="APRUEBA"
                                                class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-400/95 text-slate-900 text-xs font-semibold shadow hover:bg-emerald-300 hover:-translate-y-0.5 transition-all">
                                            Aprobar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-4 pt-3 border-t border-white/20 text-xs text-slate-200">
                            Esta solicitud ya tiene una decisión final registrada por RH ({{ $solicitud->estado }}).
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
