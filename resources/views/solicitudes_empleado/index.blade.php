{{-- resources/views/solicitudes_empleado/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Mis solicitudes de vacaciones')

@section('styles')
<style>
    body {
        background: radial-gradient(circle at top left, #1e3a8a, #020617 45%, #065f46);
        min-height: 100vh;
    }
    .glass-card {
        background: rgba(15, 23, 42, 0.8);
        border-radius: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.5);
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(18px);
    }
    .pill {
        border-radius: 999px;
        padding: 0.15rem 0.65rem;
        font-size: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto py-10 px-4">
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

    <div class="glass-card p-6 text-slate-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-semibold">Mis solicitudes de vacaciones</h1>
                <p class="text-sm text-slate-300 mt-1">
                    Consulta el historial de tus solicitudes y su estado.
                </p>
            </div>
            <div>
                <a href="{{ route('solicitudes-empleado.create') }}"
                   class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-500 hover:bg-emerald-400 text-slate-900 transition">
                    + Nueva solicitud
                </a>
            </div>
        </div>

        {{-- Filtros --}}
        <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
            <div>
                <label class="block text-slate-300 mb-1">Estado</label>
                <select name="estado" class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-2 py-1.5">
                    <option value="">Todos</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ $estado }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Tipo</label>
                <select name="tipo" class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-2 py-1.5">
                    <option value="">Todos</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Desde</label>
                <input type="date" name="desde"
                       value="{{ request('desde') }}"
                       class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-2 py-1.5">
            </div>
            <div>
                <label class="block text-slate-300 mb-1">Hasta</label>
                <input type="date" name="hasta"
                       value="{{ request('hasta') }}"
                       class="w-full rounded-lg bg-slate-900/60 border border-slate-600 text-slate-100 px-2 py-1.5">
            </div>
            <div class="md:col-span-4 flex gap-3 mt-1">
                <button type="submit"
                        class="px-4 py-1.5 rounded-xl text-sm font-semibold bg-sky-500 hover:bg-sky-400 text-slate-900 transition">
                    Filtrar
                </button>
                <a href="{{ route('solicitudes-empleado.index') }}"
                   class="px-4 py-1.5 rounded-xl text-sm font-semibold bg-slate-700 hover:bg-slate-600 text-slate-100 transition">
                    Limpiar
                </a>
            </div>
        </form>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-600 text-slate-300">
                        <th class="text-left py-2 pr-3">Folio</th>
                        <th class="text-left py-2 px-3">Fechas</th>
                        <th class="text-left py-2 px-3">Días</th>
                        <th class="text-left py-2 px-3">Tipo</th>
                        <th class="text-left py-2 px-3">Estado</th>
                        <th class="text-left py-2 px-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($solicitudes as $solicitud)
                        <tr class="hover:bg-slate-800/60">
                            <td class="py-2 pr-3 align-middle text-slate-100">
                                #{{ $solicitud->id_solicitud }}
                            </td>
                            <td class="py-2 px-3 align-middle">
                                <div class="text-slate-100 text-xs">
                                    {{ $solicitud->fecha_inicio->format('d/m/Y') }} –
                                    {{ $solicitud->fecha_fin->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="py-2 px-3 align-middle text-slate-100">
                                {{ $solicitud->dias_solicitados }}
                            </td>
                            <td class="py-2 px-3 align-middle">
                                <span class="pill bg-sky-500/20 border border-sky-400/60 text-sky-100">
                                    {{ $solicitud->tipo }}
                                </span>
                            </td>
                            <td class="py-2 px-3 align-middle">
                                @php
                                    $colors = [
                                        'BORRADOR' => 'bg-slate-500/30 border-slate-300/60 text-slate-100',
                                        'PENDIENTE'=> 'bg-amber-500/20 border-amber-300/60 text-amber-100',
                                        'APROBADA' => 'bg-emerald-500/20 border-emerald-300/60 text-emerald-100',
                                        'RECHAZADA'=> 'bg-red-500/20 border-red-300/60 text-red-100',
                                        'CANCELADA'=> 'bg-slate-600/30 border-slate-400/60 text-slate-100',
                                    ];
                                @endphp
                                <span class="pill border {{ $colors[$solicitud->estado] ?? 'bg-slate-600/40 border-slate-400/60 text-slate-100' }}">
                                    {{ $solicitud->estado }}
                                </span>
                            </td>
                            <td class="py-2 px-3 align-middle text-right">
                                <a href="{{ route('solicitudes-empleado.show', $solicitud->id_solicitud) }}"
                                   class="text-xs px-3 py-1 rounded-lg bg-slate-100 text-slate-900 hover:bg-white transition">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-slate-300">
                                Aún no tienes solicitudes registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $solicitudes->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
