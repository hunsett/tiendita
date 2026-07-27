@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-gradient-to-br from-emerald-50 via-cyan-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 space-y-8">
        {{-- Bienvenida / Header --}}
        <div class="bg-white/80 backdrop-blur rounded-2xl shadow-lg border border-emerald-100 px-6 py-5 flex flex-col md:flex-row justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                    PANEL DE INICIO
                </h1>
                <p class="mt-2 text-slate-700 text-sm md:text-base">
                    Bienvenido(a):
                    <span class="font-semibold text-emerald-700">
                        @if($empleado)
                            {{ $empleado->nombre }} {{ $empleado->apellidos }}
                        @else
                            {{ $usuario->usuario }}
                        @endif
                    </span>
                    👋
                </p>
                <p class="mt-1 text-xs md:text-sm text-slate-500">
                    Rol:
                    <span class="font-semibold text-slate-800">
                        {{ $rol }}
                    </span>
                </p>
                @if($usuario->ultimo_acceso)
                    <p class="mt-1 text-xs text-slate-400">
                        Último acceso:
                        {{ \Illuminate\Support\Carbon::parse($usuario->ultimo_acceso)->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>

            <div class="flex flex-col items-start md:items-end gap-2 text-xs">
                @if($rol === 'EMPLEADO')
                    <a href="{{ route('vacaciones.solicitudes.create') }}"
                       class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 text-white text-xs md:text-sm font-semibold shadow-md hover:shadow-lg hover:brightness-110 transition-all">
                        Nueva solicitud de vacaciones
                    </a>
                @elseif(in_array($rol, ['JEFE']))
                    <a href="{{ route('jefe.aprobaciones.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-cyan-500 to-emerald-500 text-white text-xs md:text-sm font-semibold shadow-md hover:shadow-lg hover:brightness-110 transition-all">
                        Ver solicitudes pendientes
                    </a>
                @endif

                @if($empleado && $empleado->departamento)
                    <p class="text-[11px] text-slate-500">
                        Departamento:
                        <span class="font-medium text-slate-800">
                            {{ $empleado->departamento->nombre }}
                        </span>
                    </p>
                @endif
                @if($empleado && $empleado->puesto)
                    <p class="text-[11px] text-slate-500">
                        Puesto:
                        <span class="font-medium text-slate-800">
                            {{ $empleado->puesto->nombre }}
                        </span>
                    </p>
                @endif
            </div>
        </div>

        {{-- Tarjetas de métricas principales --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

            {{-- Saldo de vacaciones --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 text-white shadow-lg">
                <div class="absolute -right-6 -top-6 w-20 h-20 rounded-full bg-white/10"></div>
                <div class="absolute -left-8 -bottom-10 w-28 h-28 rounded-full bg-black/10"></div>

                <div class="relative p-4 space-y-1">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/80">
                            Saldo de vacaciones
                        </div>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white/15 text-xs">
                            🏖️
                        </span>
                    </div>

                    @if($saldoActual)
                        <div class="mt-2 text-2xl font-extrabold">
                            {{ number_format($saldoActual->dias_disponibles, 0) }} días disponibles
                        </div>
                        <p class="mt-1 text-[11px] text-white/80">
                            Periodo:
                            {{ $saldoActual->periodo_inicio->format('d/m/Y') }}
                            –
                            {{ $saldoActual->periodo_fin->format('d/m/Y') }}
                        </p>
                    @else
                        <p class="mt-3 text-sm text-white/90">
                            Aún no hay saldo registrado.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Próximas vacaciones --}}
            <div class="rounded-2xl bg-white/90 backdrop-blur shadow-md border border-cyan-100 p-4">
                <div class="flex items-center justify-between">
                    <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.18em]">
                        Próximas vacaciones
                    </div>
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-cyan-50 text-cyan-600 text-xs">
                        📅
                    </span>
                </div>

                @if($proximasVacaciones->count() > 0)
                    @php $prox = $proximasVacaciones->first(); @endphp
                    <div class="mt-3 text-sm font-semibold text-slate-900">
                        {{ $prox->fecha_inicio->format('d/m/Y') }}
                        – {{ $prox->fecha_fin->format('d/m/Y') }}
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">
                        Tipo: {{ $prox->tipo }} • Estado: {{ $prox->estado }}
                    </p>
                @else
                    <p class="mt-3 text-sm text-slate-500">
                        No tienes vacaciones próximas.
                    </p>
                @endif
            </div>

            {{-- Solo ADMIN / RH --}}
            @if(in_array($rol, ['ADMIN', 'RH']))
                {{-- Empleados activos --}}
                <div class="rounded-2xl bg-white/90 backdrop-blur shadow-md border border-emerald-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.18em]">
                            Empleados activos
                        </div>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 text-xs">
                            👥
                        </span>
                    </div>
                    <div class="mt-3 text-2xl font-extrabold text-emerald-600">
                        {{ $empleadosActivos ?? 0 }}
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">
                        De un total de {{ $totalEmpleados ?? 0 }} empleados.
                    </p>
                </div>

                {{-- Solicitudes pendientes --}}
                <div class="rounded-2xl bg-white/90 backdrop-blur shadow-md border border-amber-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.18em]">
                            Solicitudes pendientes
                        </div>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-50 text-amber-600 text-xs">
                            ⏳
                        </span>
                    </div>
                    <div class="mt-3 text-2xl font-extrabold text-amber-600">
                        {{ $solPendientes ?? 0 }}
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">
                        Aprobadas este mes: {{ $solAprobadasMes ?? 0 }}.
                    </p>
                </div>
            @else
                {{-- Día festivo más cercano --}}
                <div class="rounded-2xl bg-white/90 backdrop-blur shadow-md border border-cyan-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.18em]">
                            Día festivo más cercano
                        </div>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-cyan-50 text-cyan-600 text-xs">
                            🎉
                        </span>
                    </div>

                    @if($proximosFestivos->count() > 0)
                        @php $festivo = $proximosFestivos->first(); @endphp
                        <div class="mt-3 text-sm font-semibold text-slate-900">
                            {{ $festivo->nombre }}
                        </div>
                        <p class="mt-1 text-[11px] text-slate-500">
                            {{ $festivo->fecha->format('d/m/Y') }}
                            @if($festivo->es_nacional)
                                • Nacional
                            @endif
                        </p>
                    @else
                        <p class="mt-3 text-sm text-slate-500">
                            No hay días festivos próximos registrados.
                        </p>
                    @endif
                </div>

                {{-- Estado de usuario --}}
                <div class="rounded-2xl bg-white/90 backdrop-blur shadow-md border border-slate-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-[0.18em]">
                            Estado de usuario
                        </div>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 text-slate-600 text-xs">
                            ⚙️
                        </span>
                    </div>
                    <div class="mt-3">
                        <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-semibold
                            @if($usuario->estado === 'ACTIVO')
                                bg-emerald-50 text-emerald-700 border border-emerald-100
                            @else
                                bg-red-50 text-red-700 border border-red-100
                            @endif">
                            {{ $usuario->estado }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Contenido principal: tablas & resumen --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Columna izquierda (2/3) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Admin / RH: solicitudes pendientes --}}
                @if(in_array($rol, ['ADMIN', 'RH']))
                    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-cyan-50 to-emerald-50">
                            <h2 class="text-sm font-semibold text-slate-800">
                                Solicitudes pendientes
                            </h2>
                            <a href="#"
                               class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                Ver todas
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Empleado
                                        </th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Fechas
                                        </th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Tipo
                                        </th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Estado
                                        </th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white/80">
                                    @forelse($solicitudesPendientesListado as $sol)
                                        <tr class="hover:bg-emerald-50/40 transition-colors">
                                            <td class="px-4 py-2">
                                                @if($sol->empleado)
                                                    <span class="font-medium text-slate-800">
                                                        {{ $sol->empleado->nombre }} {{ $sol->empleado->apellidos }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic text-xs">
                                                        Sin empleado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-xs text-slate-700">
                                                {{ $sol->fecha_inicio->format('d/m/Y') }}
                                                –
                                                {{ $sol->fecha_fin->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-2 text-xs text-slate-700">
                                                {{ $sol->tipo }}
                                            </td>
                                            <td class="px-4 py-2 text-xs">
                                                <span class="inline-flex px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-100 font-semibold">
                                                    {{ $sol->estado }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <a href="#"
                                                   class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                                    Revisar
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-xs text-slate-500">
                                                No hay solicitudes pendientes.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Admin / RH: empleados por departamento --}}
                    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-cyan-50">
                            <h2 class="text-sm font-semibold text-slate-800">
                                Empleados por departamento
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Departamento
                                        </th>
                                        <th class="px-4 py-2 text-right text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Empleados
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white/80">
                                    @forelse($empleadosPorDepto as $item)
                                        <tr class="hover:bg-cyan-50/40 transition-colors">
                                            <td class="px-4 py-2 text-sm text-slate-800">
                                                {{ optional($item->departamento)->nombre ?? 'Sin departamento' }}
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm font-semibold text-slate-900">
                                                {{ $item->total }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-4 py-4 text-center text-xs text-slate-500">
                                                No hay datos de departamentos.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- JEFE: solicitudes pendientes a aprobar --}}
                @elseif($rol === 'JEFE')
                    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-cyan-50 to-emerald-50">
                            <h2 class="text-sm font-semibold text-slate-800">
                                Solicitudes pendientes de tu equipo
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Empleado
                                        </th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Fechas
                                        </th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Tipo
                                        </th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white/80">
                                    @forelse($solicitudesPendientesAprobar as $sol)
                                        <tr class="hover:bg-emerald-50/40 transition-colors">
                                            <td class="px-4 py-2">
                                                @if($sol->empleado)
                                                    <span class="font-medium text-slate-800">
                                                        {{ $sol->empleado->nombre }} {{ $sol->empleado->apellidos }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-xs text-slate-700">
                                                {{ $sol->fecha_inicio->format('d/m/Y') }}
                                                –
                                                {{ $sol->fecha_fin->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-2 text-xs text-slate-700">
                                                {{ $sol->tipo }}
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <a href="#"
                                                   class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                                    Revisar
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center text-xs text-slate-500">
                                                No hay solicitudes pendientes por aprobar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- EMPLEADO: sus solicitudes recientes --}}
                @else
                    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50 to-cyan-50">
                            <h2 class="text-sm font-semibold text-slate-800">
                                Mis últimas solicitudes
                            </h2>
                            <a href="#"
                               class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                Ver historial completo
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Fechas
                                        </th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Tipo
                                        </th>
                                        <th class="px-4 py-2 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                            Estado
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white/80">
                                    @forelse($misSolicitudesRecientes as $sol)
                                        <tr class="hover:bg-cyan-50/40 transition-colors">
                                            <td class="px-4 py-2 text-xs text-slate-700">
                                                {{ $sol->fecha_inicio->format('d/m/Y') }}
                                                –
                                                {{ $sol->fecha_fin->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-2 text-xs text-slate-700">
                                                {{ $sol->tipo }}
                                            </td>
                                            <td class="px-4 py-2 text-xs">
                                                <span class="inline-flex px-2 py-1 rounded-full text-[11px] font-semibold
                                                    @switch($sol->estado)
                                                        @case('APROBADA') bg-emerald-50 text-emerald-700 border border-emerald-100 @break
                                                        @case('RECHAZADA') bg-red-50 text-red-700 border border-red-100 @break
                                                        @case('PENDIENTE') bg-amber-50 text-amber-700 border border-amber-100 @break
                                                        @default bg-slate-50 text-slate-700 border border-slate-100
                                                    @endswitch
                                                ">
                                                    {{ $sol->estado }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-4 text-center text-xs text-slate-500">
                                                Todavía no has registrado solicitudes.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Columna derecha (1/3) --}}
            <div class="space-y-6">

                {{-- Detalle saldo vacaciones --}}
                <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-emerald-100 p-4">
                    <h2 class="text-sm font-semibold text-slate-800">
                        Detalle de saldo de vacaciones
                    </h2>
                    @if($saldoActual)
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Días acumulados</dt>
                                <dd class="font-semibold text-slate-900">
                                    {{ number_format($saldoActual->dias_acumulados, 0) }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Días usados</dt>
                                <dd class="font-semibold text-slate-900">
                                    {{ number_format($saldoActual->dias_usados, 0) }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Días disponibles</dt>
                                <dd class="font-semibold text-emerald-600">
                                    {{ number_format($saldoActual->dias_disponibles, 0) }}
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="mt-3 text-sm text-slate-500">
                            Aún no hay registro de saldo. Contacta a RH si crees que es un error.
                        </p>
                    @endif
                </div>

                {{-- Próximos días festivos --}}
                <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-cyan-100 p-4">
                    <h2 class="text-sm font-semibold text-slate-800">
                        Próximos días festivos
                    </h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        @forelse($proximosFestivos as $festivo)
                            <li class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">
                                        {{ $festivo->nombre }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $festivo->fecha->format('d/m/Y') }}
                                        @if($festivo->es_nacional)
                                            • Nacional
                                        @endif
                                    </p>
                                </div>
                            </li>
                        @empty
                            <li class="text-[11px] text-slate-500">
                                No hay días festivos próximos registrados.
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Mensaje / tips 
                <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 via-cyan-50 to-white px-4 py-4 text-[11px] text-emerald-900 shadow-sm">
                    <p class="font-semibold flex items-center gap-1">
                        <span>💡 Tip</span>
                    </p>
                    <p class="mt-1 leading-relaxed">
                        Usa este panel como resumen rápido. Las altas de empleados, gestión de vacaciones y
                        aprobaciones se manejan desde los módulos específicos de la izquierda. Tu súper sistema
                        de <span class="font-semibold">Tienda Mary</span> va quedando nivel empresa grande, rey. 💚
                    </p>
                </div>
                --}}
            </div>
        </div>
    </div>
</div>
@endsection
