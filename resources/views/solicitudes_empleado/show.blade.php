{{-- resources/views/solicitudes_empleado/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalle de solicitud')

@section('styles')
<style>
    body {
        background: radial-gradient(circle at top left, #0f172a, #020617 45%, #0f766e);
        min-height: 100vh;
    }
    .glass-card {
        background: rgba(15, 23, 42, 0.86);
        border-radius: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.65);
        box-shadow: 0 26px 55px rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(20px);
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
        <a href="{{ route('solicitudes-empleado.index') }}" class="text-sm text-slate-200 hover:text-white">
            ← Volver a mis solicitudes
        </a>
    </div>

    <div class="glass-card p-6 max-w-2xl mx-auto">
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

        <div class="flex items-start justify-between gap-4 mb-4">
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

        <dl class="text-sm space-y-3 mb-5">
            <div class="flex justify-between gap-3">
                <dt class="text-slate-300">Fechas</dt>
                <dd class="text-slate-100">
                    {{ $solicitud->fecha_inicio->format('d/m/Y') }} –
                    {{ $solicitud->fecha_fin->format('d/m/Y') }}
                    ({{ $solicitud->dias_solicitados }} días hábiles)
                </dd>
            </div>

            @if($solicitud->enviada_en)
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-300">Enviada</dt>
                    <dd class="text-slate-100">
                        {{ $solicitud->enviada_en->format('d/m/Y H:i') }}
                    </dd>
                </div>
            @endif

            @if($solicitud->decidida_en)
                <div class="flex justify-between gap-3">
                    <dt class="text-slate-300">Decidida</dt>
                    <dd class="text-slate-100">
                        {{ $solicitud->decidida_en->format('d/m/Y H:i') }}
                    </dd>
                </div>
            @endif

            <div class="flex justify-between gap-3">
                <dt class="text-slate-300">Saldo (referencia)</dt>
                <dd class="text-slate-100 text-right">
                    @if($saldoActual)
                        <span class="block text-xs">
                            Acum: <strong>{{ $saldoActual->dias_acumulados }}</strong> /
                            Usados: <strong>{{ $saldoActual->dias_usados }}</strong> /
                            Disp: <strong>{{ $saldoActual->dias_disponibles }}</strong>
                        </span>
                    @else
                        <span class="text-xs text-amber-100">
                            Sin saldo configurado para el periodo actual.
                        </span>
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-slate-300 mb-1">Motivo</dt>
                <dd class="text-slate-100 bg-slate-900/60 rounded-lg px-3 py-2 text-sm">
                    {{ $solicitud->motivo ?: 'Sin motivo especificado.' }}
                </dd>
            </div>
        </dl>

        <div class="pt-4 border-t border-slate-700/80 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <p class="text-xs text-slate-300">
                Desde aquí puedes enviar o cancelar tu solicitud según su estado.
            </p>

            <div class="flex gap-3 justify-end">
                @if($solicitud->estado === 'BORRADOR')
                    <form method="POST" action="{{ route('solicitudes-empleado.enviar', $solicitud->id_solicitud) }}">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-slate-900 transition">
                            Enviar solicitud
                        </button>
                    </form>
                @endif

                @if(in_array($solicitud->estado, ['BORRADOR','PENDIENTE']))
                    <form method="POST" action="{{ route('solicitudes-empleado.cancelar', $solicitud->id_solicitud) }}"
                          onsubmit="return confirm('¿Seguro que deseas cancelar esta solicitud?');">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 hover:bg-red-500 text-slate-100 transition">
                            Cancelar solicitud
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
