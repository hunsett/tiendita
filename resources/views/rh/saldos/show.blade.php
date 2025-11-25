@extends('layouts.app')

@section('title', 'Saldo de vacaciones – RH')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-4xl mx-auto px-4 space-y-6">

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('rh.saldos.index') }}"
               class="text-xs text-slate-300 hover:text-white transition-colors">
                ← Volver a saldos
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
                            Saldo de vacaciones – {{ $empleado->nombre }} {{ $empleado->apellidos }}
                        </h1>
                        <p class="mt-1 text-xs text-slate-200/80">
                            Código: {{ $empleado->codigo ?? '—' }}
                            · {{ $empleado->correo }}
                            @if($empleado->departamento)
                                · {{ $empleado->departamento->nombre }}
                            @endif
                        </p>
                    </div>

                    <div class="text-right text-xs space-y-1">
                        <div>
                            Periodo actual:
                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-black/40 text-slate-50 border border-white/25">
                                {{ $saldoSeleccionado->periodo_inicio->format('d/m/Y') }}
                                –
                                {{ $saldoSeleccionado->periodo_fin->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="text-[0.7rem] text-slate-100/80">
                            Días acumulados:
                            <span class="font-semibold">{{ $saldoSeleccionado->dias_acumulados }}</span> ·
                            Usados:
                            <span class="font-semibold">{{ $saldoSeleccionado->dias_usados }}</span> ·
                            Disponibles:
                            <span class="font-semibold">{{ $saldoSeleccionado->dias_disponibles }}</span>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-6 text-sm">

                    {{-- Selector de periodo --}}
                    <div>
                        <span class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-2">
                            Otros periodos de saldo del colaborador
                        </span>

                        <div class="flex flex-wrap gap-2">
                            @foreach($saldos as $saldo)
                                <a href="{{ route('rh.saldos.show', [
                                        'id_empleado' => $empleado->id_empleado,
                                        'saldo_id'    => $saldo->id_saldo,
                                    ]) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold border
                                        @class([
                                            'bg-emerald-400/95 text-slate-900 border-emerald-300 shadow' => $saldo->id_saldo === $saldoSeleccionado->id_saldo,
                                            'bg-black/30 text-slate-100 border-white/25 hover:bg-black/50' => $saldo->id_saldo !== $saldoSeleccionado->id_saldo,
                                        ])">
                                    {{ $saldo->periodo_inicio->format('Y') }} ·
                                    {{ $saldo->dias_disponibles }} disp.
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Solicitudes que afectan ese periodo --}}
                    <div>
                        <span class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-2">
                            Solicitudes aprobadas en este periodo
                        </span>

                        @if($solicitudes->isEmpty())
                            <p class="text-xs text-slate-100/80">
                                No hay solicitudes aprobadas que afecten este periodo.
                            </p>
                        @else
                            <div class="overflow-x-auto rounded-2xl border border-white/20 bg-black/25">
                                <table class="min-w-full text-xs">
                                    <thead>
                                        <tr class="bg-black/30 text-[11px] uppercase tracking-wide text-slate-100/80">
                                            <th class="px-4 py-2 text-left font-semibold">Folio</th>
                                            <th class="px-4 py-2 text-left font-semibold">Fechas</th>
                                            <th class="px-4 py-2 text-right font-semibold">Días</th>
                                            <th class="px-4 py-2 text-left font-semibold">Tipo</th>
                                            <th class="px-4 py-2 text-left font-semibold">Decidida en</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        @foreach($solicitudes as $solicitud)
                                            <tr class="hover:bg-black/20 transition-colors">
                                                <td class="px-4 py-2">
                                                    #{{ $solicitud->id_solicitud }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $solicitud->fecha_inicio->format('d/m/Y') }}
                                                    –
                                                    {{ $solicitud->fecha_fin->format('d/m/Y') }}
                                                </td>
                                                <td class="px-4 py-2 text-right">
                                                    {{ $solicitud->dias_solicitados }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $solicitud->tipo }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $solicitud->decidida_en ? $solicitud->decidida_en->format('d/m/Y H:i') : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <p class="text-[0.7rem] text-slate-200/80">
                        Nota: Los saldos se actualizan automáticamente cuando Recursos Humanos aprueba solicitudes de vacaciones
                        en el módulo de aprobaciones RH (nivel 2).
                    </p>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
