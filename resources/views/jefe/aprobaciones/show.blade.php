{{-- resources/views/jefe/aprobaciones/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalle de solicitud – Jefe')

@section('styles')
<style>
    body {
        background: radial-gradient(circle at top left, #0f172a, #020617 45%, #15803d);
        min-height: 100vh;
    }
    .glass-card {
        background: rgba(15, 23, 42, 0.9);
        border-radius: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.7);
        box-shadow: 0 26px 55px rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(22px);
    }
    .pill {
        border-radius: 999px;
        padding: 0.15rem 0.65rem;
        font-size: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto py-10 px-4 text-slate-100">
    <div class="mb-4 flex items-center justify-between gap-3">
        <a href="{{ route('jefe.aprobaciones.index') }}" class="text-sm text-slate-200 hover:text-white">
            ← Volver a la bandeja
        </a>
    </div>

    <div class="glass-card p-6 max-w-3xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg text-sm text-emerald-100 bg-emerald-700/70 border border-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg text-sm text-red-100 bg-red-700/70 border border-red-400">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-5">
            <div>
                <h1 class="text-xl font-semibold">
                    Solicitud #{{ $solicitud->id_solicitud }}
                </h1>
                <p class="text-xs text-slate-300 mt-1">
                    Creada el {{ $solicitud->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="text-right space-y-1">
                @php
                    $colors = [
                        'BORRADOR' => 'bg-slate-500/30 border-slate-300/60 text-slate-100',
                        'PENDIENTE'=> 'bg-amber-500/20 border-amber-300/60 text-amber-100',
                        'APROBADA' => 'bg-emerald-500/20 border-emerald-300/60 text-emerald-100',
                        'RECHAZADA'=> 'bg-red-500/20 border-red-300/60 text-red-100',
                        'CANCELADA'=> 'bg-slate-600/30 border-slate-400/60 text-slate-100',
                    ];
                @endphp
                <span class="pill border {{ $colors[$solicitud->estado] ?? '' }}">
                    {{ $solicitud->estado }}
                </span>
                <div class="text-[0.7rem] text-slate-300">
                    Tipo: <span class="font-semibold">{{ $solicitud->tipo }}</span>
                </div>
            </div>
        </div>

        {{-- Datos del colaborador --}}
        <div class="mb-5 border border-slate-700/80 rounded-xl p-4 bg-slate-900/40">
            <h2 class="text-sm font-semibold text-slate-200 mb-2">Colaborador</h2>
            <div class="text-sm grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <span class="text-slate-400 text-xs">Nombre</span>
                    <div class="text-slate-100">
                        {{ $solicitud->empleado->nombre }} {{ $solicitud->empleado->apellidos }}
                    </div>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">Código</span>
                    <div class="text-slate-100">
                        {{ $solicitud->empleado->codigo ?? '—' }}
                    </div>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">Correo</span>
                    <div class="text-slate-100">
                        {{ $solicitud->empleado->correo }}
                    </div>
                </div>
                <div>
                    <span class="text-slate-400 text-xs">Estado</span>
                    <div class="text-slate-100">
                        {{ $solicitud->empleado->estado }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Datos de la solicitud --}}
        <dl class="text-sm space-y-3 mb-5">
            <div class="flex justify-between gap-3">
                <dt class="text-slate-300">Fechas solicitadas</dt>
                <dd class="text-slate-100">
                    {{ $solicitud->fecha_inicio->format('d/m/Y') }} –
                    {{ $solicitud->fecha_fin->format('d/m/Y') }}
                    ({{ $solicitud->dias_solicitados }} días hábiles)
                </dd>
            </div>

            @if($solicitud->enviada_en)
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-300">Enviada por el colaborador</dt>
                    <dd class="text-slate-100">
                        {{ $solicitud->enviada_en->format('d/m/Y H:i') }}
                    </dd>
                </div>
            @endif

            @if($solicitud->decidida_en)
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-300">Decisión final registrada</dt>
                    <dd class="text-slate-100">
                        {{ $solicitud->decidida_en->format('d/m/Y H:i') }}
                    </dd>
                </div>
            @endif

            <div>
                <dt class="text-slate-300 mb-1">Motivo del colaborador</dt>
                <dd class="text-slate-100 bg-slate-900/60 rounded-lg px-3 py-2 text-sm">
                    {{ $solicitud->motivo ?: 'Sin motivo especificado.' }}
                </dd>
            </div>
        </dl>

        {{-- Historial de aprobaciones --}}
        <div class="mb-6">
            <h2 class="text-sm font-semibold text-slate-200 mb-2">Historial de aprobaciones</h2>
            @if($solicitud->aprobaciones->isEmpty())
                <p class="text-xs text-slate-300">
                    No hay aprobaciones registradas aún.
                </p>
            @else
                <ul class="space-y-2 text-xs">
                    @foreach($solicitud->aprobaciones->sortBy('accion_en') as $ap)
                        <li class="border border-slate-700/70 rounded-lg px-3 py-2 bg-slate-900/60">
                            <div class="flex justify-between gap-3">
                                <span class="font-semibold text-slate-100">
                                    Nivel {{ $ap->nivel }} – {{ $ap->accion }}
                                </span>
                                <span class="text-slate-400">
                                    {{ $ap->accion_en ? $ap->accion_en->format('d/m/Y H:i') : '—' }}
                                </span>
                            </div>
                            <div class="mt-1 text-slate-300">
                                @if($ap->aprobador)
                                    <span class="font-semibold">
                                        {{ $ap->aprobador->usuario }}
                                    </span>
                                    @if($ap->aprobador->empleado)
                                        <span class="text-slate-400">
                                            ({{ $ap->aprobador->empleado->nombre }} {{ $ap->aprobador->empleado->apellidos }})
                                        </span>
                                    @endif
                                    <span class="text-slate-500"> · rol {{ $ap->aprobador->rol }}</span>
                                @endif
                            </div>
                            @if($ap->comentario)
                                <div class="mt-1 text-slate-200">
                                    Comentario: <span class="text-slate-100">{{ $ap->comentario }}</span>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Formulario de decisión del JEFE --}}
        @if($solicitud->estado === 'PENDIENTE')
            <div class="pt-4 border-t border-slate-700/80">
                <h2 class="text-sm font-semibold text-slate-200 mb-2">
                    Tu decisión como jefe
                </h2>

                <form method="POST" action="{{ route('jefe.aprobaciones.decidir', $solicitud->id_solicitud) }}"
                      class="space-y-3 text-sm">
                    @csrf

                    <div>
                        <label class="block mb-1 text-slate-200 text-xs uppercase tracking-wide">
                            Comentario
                        </label>
                        <textarea name="comentario" rows="3"
                                  class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-3 py-2 text-sm"
                                  placeholder="Explica brevemente el motivo de tu aprobación o rechazo.">{{ old('comentario') }}</textarea>
                        <p class="text-[0.7rem] text-slate-400 mt-1">
                            Si vas a <strong>rechazar</strong>, el comentario es obligatorio.
                        </p>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mt-3">
                        <p class="text-xs text-slate-300">
                            Esta acción actualizará el estado de la solicitud y el saldo de vacaciones del colaborador.
                        </p>
                        <div class="flex gap-3 justify-end">
                            <button type="submit" name="accion" value="RECHAZA"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 hover:bg-red-500 text-slate-100 transition">
                                Rechazar
                            </button>
                            <button type="submit" name="accion" value="APRUEBA"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-slate-900 transition">
                                Aprobar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="mt-4 text-xs text-slate-300 border-t border-slate-700/80 pt-3">
                Esta solicitud ya tiene una decisión final registrada ({{ $solicitud->estado }}).
            </div>
        @endif
    </div>
</div>
@endsection
