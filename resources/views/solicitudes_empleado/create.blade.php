{{-- resources/views/solicitudes_empleado/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nueva solicitud de vacaciones')

@section('styles')
<style>
    body {
        background: radial-gradient(circle at top left, #1e40af, #020617 45%, #047857);
        min-height: 100vh;
    }
    .glass-card {
        background: rgba(15, 23, 42, 0.84);
        border-radius: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.6);
        box-shadow: 0 24px 50px rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(18px);
    }
</style>
@endsection

@section('content')
<div class="container mx-auto py-10 px-4 text-slate-100">
    <div class="mb-4">
        <a href="{{ route('solicitudes-empleado.index') }}" class="text-sm text-slate-200 hover:text-white">
            ← Volver a mis solicitudes
        </a>
    </div>

    <div class="glass-card p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-semibold mb-1">Nueva solicitud de vacaciones</h1>
        <p class="text-sm text-slate-300 mb-4">
            Llena la información de tu periodo de descanso. Los días se calculan
            automáticamente excluyendo fines de semana y días festivos.
        </p>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg text-sm text-red-100 bg-red-700/70 border border-red-400">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-4 text-sm">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-500/15 border border-emerald-300/60">
                <span class="text-xs font-semibold text-emerald-200">Saldo actual:</span>
                @if($saldoActual)
                    <span class="text-xs text-slate-100">
                        Acumulados: <strong>{{ $saldoActual->dias_acumulados }}</strong> /
                        Usados: <strong>{{ $saldoActual->dias_usados }}</strong> /
                        Disponibles: <strong>{{ $saldoActual->dias_disponibles }}</strong> días
                    </span>
                @else
                    <span class="text-xs text-amber-100">
                        No hay un saldo configurado para tu periodo actual. Si tienes dudas, consulta con RH.
                    </span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('solicitudes-empleado.store') }}" class="space-y-4 text-sm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-slate-200 text-xs uppercase tracking-wide">
                        Fecha inicio
                    </label>
                    <input type="date" name="fecha_inicio"
                           value="{{ old('fecha_inicio') }}"
                           class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block mb-1 text-slate-200 text-xs uppercase tracking-wide">
                        Fecha fin
                    </label>
                    <input type="date" name="fecha_fin"
                           value="{{ old('fecha_fin') }}"
                           class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block mb-1 text-slate-200 text-xs uppercase tracking-wide">
                    Tipo de solicitud
                </label>
                <select name="tipo"
                        class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-3 py-2 text-sm">
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 text-slate-200 text-xs uppercase tracking-wide">
                    Motivo (opcional)
                </label>
                <textarea name="motivo" rows="4"
                          class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-3 py-2 text-sm"
                          placeholder="Ej. Vacaciones anuales, evento familiar, etc.">{{ old('motivo') }}</textarea>
            </div>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pt-3 border-t border-slate-700/80 mt-6">
                <p class="text-xs text-slate-300">
                    Puedes guardar la solicitud como <strong>borrador</strong> y enviarla más tarde.
                </p>

                <div class="flex gap-3 justify-end">
                    <button type="submit" name="accion" value="guardar"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-600 hover:bg-slate-500 text-slate-100 transition">
                        Guardar borrador
                    </button>
                    <button type="submit" name="accion" value="enviar"
                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-slate-900 transition">
                        Enviar solicitud
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
