@extends('layouts.app')

@section('title', 'Saldos de vacaciones – RH')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        {{-- Cabecera --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white drop-shadow-sm">
                    Saldos de vacaciones
                </h1>
                <p class="mt-2 text-sm text-slate-300 max-w-xl">
                    Consulta los saldos de vacaciones por empleado y periodo anual.
                    Desde aquí también puedes generar los periodos de un nuevo año.
                </p>
            </div>

            <div class="flex flex-col items-end gap-3">
                {{-- Form generar periodos --}}
                <form action="{{ route('rh.saldos.generar') }}" method="POST"
                      class="flex items-center gap-2 text-xs">
                    @csrf
                    <input type="number" name="anio"
                           value="{{ old('anio', $anioSeleccionado) }}"
                           class="w-20 rounded-full bg-white/90 text-slate-900 px-3 py-1 border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400"
                           placeholder="Año">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-1.5 rounded-full bg-emerald-400/95 text-slate-900 font-semibold shadow hover:bg-emerald-300 hover:-translate-y-0.5 transition-all">
                        Generar periodo
                    </button>
                </form>

                @if($saldos->count())
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-black/40 text-slate-100 text-[11px]">
                        {{ $saldos->total() }} registros en {{ $anioSeleccionado }}
                    </span>
                @endif>
            </div>
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
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold tracking-[0.18em] uppercase text-white/80">
                                Saldos por empleado – Año {{ $anioSeleccionado }}
                            </h2>
                            <p class="mt-1 text-xs text-slate-200/80">
                                Filtra por nombre, código o correo. Selecciona otro año si ya se generaron más periodos.
                            </p>
                        </div>

                        <form method="GET" class="flex flex-col sm:flex-row gap-2 text-xs items-stretch sm:items-center">
                            {{-- Select año --}}
                            <select name="anio"
                                    class="rounded-full bg-black/30 border border-white/30 text-slate-50 px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-cyan-300/80 focus:border-cyan-300">
                                @forelse($aniosDisponibles as $anio)
                                    <option value="{{ $anio }}" @selected($anio == $anioSeleccionado)>
                                        {{ $anio }}
                                    </option>
                                @empty
                                    <option value="{{ $anioSeleccionado }}" selected>
                                        {{ $anioSeleccionado }}
                                    </option>
                                @endforelse
                            </select>

                            {{-- Buscador --}}
                            <div class="flex items-center gap-2">
                                <input type="text" name="q" value="{{ request('q') }}"
                                       placeholder="Buscar colaborador..."
                                       class="w-40 sm:w-52 rounded-full bg-black/30 border border-white/20 text-slate-50 px-3 py-1.5 text-xs placeholder:text-slate-300/70 focus:outline-none focus:ring-2 focus:ring-cyan-300/80 focus:border-cyan-300">
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-sky-500/95 text-slate-900 font-semibold shadow hover:bg-sky-400 transition-all">
                                    Aplicar
                                </button>
                                <a href="{{ route('rh.saldos.index', ['anio' => $anioSeleccionado]) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-full bg-black/40 text-slate-50 border border-white/25 text-[11px] hover:bg-black/60 transition-all">
                                    Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @if($saldos->count())
                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-black/25 text-[11px] uppercase tracking-wide text-slate-100/80">
                                    <th class="px-6 py-3 text-left font-semibold">Empleado</th>
                                    <th class="px-6 py-3 text-left font-semibold">Departamento</th>
                                    <th class="px-6 py-3 text-left font-semibold">Periodo</th>
                                    <th class="px-6 py-3 text-right font-semibold">Acumulados</th>
                                    <th class="px-6 py-3 text-right font-semibold">Usados</th>
                                    <th class="px-6 py-3 text-right font-semibold">Disponibles</th>
                                    <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($saldos as $saldo)
                                    <tr class="hover:bg-black/20 transition-colors">
                                        <td class="px-6 py-3 text-sm text-white">
                                            <div class="font-medium">
                                                {{ $saldo->empleado->nombre }} {{ $saldo->empleado->apellidos }}
                                            </div>
                                            <div class="text-[11px] text-slate-100/80">
                                                Código: {{ $saldo->empleado->codigo ?? '—' }} ·
                                                {{ $saldo->empleado->correo }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $saldo->empleado->departamento->nombre ?? 'Sin depto.' }}
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-100/90">
                                            {{ $saldo->periodo_inicio->format('d/m/Y') }}
                                            –
                                            {{ $saldo->periodo_fin->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-3 text-xs text-right text-slate-100/90">
                                            {{ $saldo->dias_acumulados }}
                                        </td>
                                        <td class="px-6 py-3 text-xs text-right text-slate-100/90">
                                            {{ $saldo->dias_usados }}
                                        </td>
                                        <td class="px-6 py-3 text-xs text-right">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full
                                                @class([
                                                    'bg-emerald-400/95 text-slate-900 font-semibold' => $saldo->dias_disponibles > 0,
                                                    'bg-rose-500/95 text-white font-semibold' => $saldo->dias_disponibles <= 0,
                                                ])
                                            ">
                                                {{ $saldo->dias_disponibles }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('rh.saldos.show', ['id_empleado' => $saldo->id_empleado, 'saldo_id' => $saldo->id_saldo]) }}"
                                               class="inline-flex items-center px-3 py-1.5 rounded-full text-[11px] font-semibold bg-white/90 text-slate-900 hover:bg-white transition-colors shadow-sm">
                                                Ver detalle
                                            </a>
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
                                    {{ $saldos->firstItem() }}–{{ $saldos->lastItem() }}
                                </span>
                                de
                                <span class="font-semibold text-white">
                                    {{ $saldos->total() }}
                                </span>
                                registros
                            </div>
                            <div class="text-xs">
                                {{ $saldos->appends(['anio' => $anioSeleccionado, 'q' => request('q')])->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="px-6 py-10 text-center text-sm text-slate-100/90">
                        No hay saldos registrados para el año {{ $anioSeleccionado }}.
                        <div class="mt-3 text-xs">
                            Puedes generar los periodos de este año usando el formulario de la derecha.
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection
