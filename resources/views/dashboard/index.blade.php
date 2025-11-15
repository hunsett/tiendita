@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-8">

    {{-- Bienvenida --}}
    <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Dashboard
            </h1>
            <p class="mt-2 text-gray-700">
                Bienvenido,
                <span class="font-semibold">
                    @if($empleado)
                        {{ $empleado->nombre }} {{ $empleado->apellidos }}
                    @else
                        {{ $usuario->usuario }}
                    @endif
                </span>
                👋
            </p>
            <p class="mt-1 text-sm text-gray-500">
                Rol: <span class="font-medium">{{ $rol }}</span>
            </p>
            @if($usuario->ultimo_acceso)
                <p class="mt-1 text-sm text-gray-500">
                    Último acceso: {{ \Illuminate\Support\Carbon::parse($usuario->ultimo_acceso)->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>

        <div class="flex flex-col items-start md:items-end gap-2">
            @if($rol === 'EMPLEADO')
                <a href="#"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                    Nueva solicitud de vacaciones
                </a>
            @elseif(in_array($rol, ['ADMIN','RH','JEFE']))
                <a href="#"
                   class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
                    Ver solicitudes pendientes
                </a>
            @endif
            @if($empleado && $empleado->departamento)
                <p class="text-xs text-gray-500">
                    Departamento: {{ $empleado->departamento->nombre }}
                </p>
            @endif
            @if($empleado && $empleado->puesto)
                <p class="text-xs text-gray-500">
                    Puesto: {{ $empleado->puesto->nombre }}
                </p>
            @endif
        </div>
    </div>

    {{-- Tarjetas de métricas principales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Saldo de vacaciones (para todos con empleado) --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                Saldo de vacaciones
            </div>
            @if($saldoActual)
                <div class="mt-2 text-2xl font-bold text-indigo-600">
                    {{ number_format($saldoActual->dias_disponibles, 1) }} días
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Periodo: {{ $saldoActual->periodo_inicio->format('d/m/Y') }}
                    – {{ $saldoActual->periodo_fin->format('d/m/Y') }}
                </p>
            @else
                <p class="mt-2 text-sm text-gray-500">
                    Aún no hay saldo registrado.
                </p>
            @endif
        </div>

        {{-- Próximas vacaciones del empleado --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                Próximas vacaciones
            </div>
            @if($proximasVacaciones->count() > 0)
                @php $prox = $proximasVacaciones->first(); @endphp
                <div class="mt-2 text-sm text-gray-800">
                    {{ $prox->fecha_inicio->format('d/m/Y') }}
                    – {{ $prox->fecha_fin->format('d/m/Y') }}
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Tipo: {{ $prox->tipo }} • Estado: {{ $prox->estado }}
                </p>
            @else
                <p class="mt-2 text-sm text-gray-500">
                    No tienes vacaciones próximas.
                </p>
            @endif
        </div>

        {{-- Solo para ADMIN / RH --}}
        @if(in_array($rol, ['ADMIN', 'RH']))
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Empleados activos
                </div>
                <div class="mt-2 text-2xl font-bold text-emerald-600">
                    {{ $empleadosActivos ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    De un total de {{ $totalEmpleados ?? 0 }} empleados.
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Solicitudes pendientes
                </div>
                <div class="mt-2 text-2xl font-bold text-amber-600">
                    {{ $solPendientes ?? 0 }}
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Aprobadas este mes: {{ $solAprobadasMes ?? 0 }}.
                </p>
            </div>
        @else
            {{-- Relleno para columnas en empleados / jefes --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Día festivo más cercano
                </div>
                @if($proximosFestivos->count() > 0)
                    @php $festivo = $proximosFestivos->first(); @endphp
                    <div class="mt-2 text-sm font-semibold text-gray-800">
                        {{ $festivo->nombre }}
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        {{ $festivo->fecha->format('d/m/Y') }}
                        @if($festivo->es_nacional)
                            • Nacional
                        @endif
                    </p>
                @else
                    <p class="mt-2 text-sm text-gray-500">
                        No hay días festivos próximos registrados.
                    </p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Estado de usuario
                </div>
                <div class="mt-2 inline-flex px-2 py-1 rounded-full text-xs font-semibold
                    @if($usuario->estado === 'ACTIVO')
                        bg-emerald-100 text-emerald-700
                    @else
                        bg-red-100 text-red-700
                    @endif
                ">
                    {{ $usuario->estado }}
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna izquierda: solicitudes del empleado o de la empresa --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Admin / RH: solicitudes pendientes --}}
            @if(in_array($rol, ['ADMIN', 'RH']))
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Solicitudes pendientes
                        </h2>
                        <a href="#" class="text-xs text-indigo-600 hover:text-indigo-800">
                            Ver todas
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Empleado</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fechas</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($solicitudesPendientesListado as $sol)
                                    <tr>
                                        <td class="px-4 py-2">
                                            @if($sol->empleado)
                                                {{ $sol->empleado->nombre }} {{ $sol->empleado->apellidos }}
                                            @else
                                                <span class="text-gray-400 italic">Sin empleado</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-700">
                                            {{ $sol->fecha_inicio->format('d/m/Y') }}
                                            –
                                            {{ $sol->fecha_fin->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-700">
                                            {{ $sol->tipo }}
                                        </td>
                                        <td class="px-4 py-2 text-xs">
                                            <span class="inline-flex px-2 py-1 rounded-full bg-amber-100 text-amber-700 font-semibold">
                                                {{ $sol->estado }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="#" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                                Revisar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-xs text-gray-500">
                                            No hay solicitudes pendientes.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Admin / RH: empleados por departamento (tabla resumida) --}}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Empleados por departamento
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Departamento</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Empleados</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($empleadosPorDepto as $item)
                                    <tr>
                                        <td class="px-4 py-2">
                                            {{ optional($item->departamento)->nombre ?? 'Sin departamento' }}
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            {{ $item->total }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-4 text-center text-xs text-gray-500">
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
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Solicitudes pendientes de tu equipo
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Empleado</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fechas</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($solicitudesPendientesAprobar as $sol)
                                    <tr>
                                        <td class="px-4 py-2">
                                            @if($sol->empleado)
                                                {{ $sol->empleado->nombre }} {{ $sol->empleado->apellidos }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-700">
                                            {{ $sol->fecha_inicio->format('d/m/Y') }}
                                            –
                                            {{ $sol->fecha_fin->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-700">
                                            {{ $sol->tipo }}
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="#" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                                Revisar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-xs text-gray-500">
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
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-800">
                            Mis últimas solicitudes
                        </h2>
                        <a href="#" class="text-xs text-indigo-600 hover:text-indigo-800">
                            Ver historial completo
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fechas</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($misSolicitudesRecientes as $sol)
                                    <tr>
                                        <td class="px-4 py-2 text-xs text-gray-700">
                                            {{ $sol->fecha_inicio->format('d/m/Y') }}
                                            –
                                            {{ $sol->fecha_fin->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-700">
                                            {{ $sol->tipo }}
                                        </td>
                                        <td class="px-4 py-2 text-xs">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                                @switch($sol->estado)
                                                    @case('APROBADA') bg-emerald-100 text-emerald-700 @break
                                                    @case('RECHAZADA') bg-red-100 text-red-700 @break
                                                    @case('PENDIENTE') bg-amber-100 text-amber-700 @break
                                                    @default bg-gray-100 text-gray-700
                                                @endswitch
                                            ">
                                                {{ $sol->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-xs text-gray-500">
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

        {{-- Columna derecha: saldo + días festivos --}}
        <div class="space-y-6">

            {{-- Resumen de saldo detallado --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="text-sm font-semibold text-gray-800">
                    Detalle de saldo de vacaciones
                </h2>
                @if($saldoActual)
                    <dl class="mt-3 space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Días acumulados</dt>
                            <dd class="font-semibold">{{ number_format($saldoActual->dias_acumulados, 1) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Días usados</dt>
                            <dd class="font-semibold">{{ number_format($saldoActual->dias_usados, 1) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Días disponibles</dt>
                            <dd class="font-semibold text-indigo-600">
                                {{ number_format($saldoActual->dias_disponibles, 1) }}
                            </dd>
                        </div>
                    </dl>
                @else
                    <p class="mt-3 text-sm text-gray-500">
                        Aún no hay registro de saldo. Contacta a RH si crees que es un error.
                    </p>
                @endif
            </div>

            {{-- Próximos días festivos --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="text-sm font-semibold text-gray-800">
                    Próximos días festivos
                </h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse($proximosFestivos as $festivo)
                        <li class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $festivo->nombre }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $festivo->fecha->format('d/m/Y') }}
                                    @if($festivo->es_nacional)
                                        • Nacional
                                    @endif
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="text-xs text-gray-500">
                            No hay días festivos próximos registrados.
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- Mensaje / tips --}}
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-xs text-indigo-900">
                <p class="font-semibold">
                    Tip:
                </p>
                <p class="mt-1">
                    Usa este panel solo como resumen. Las altas de empleados, gestión de vacaciones
                    y aprobaciones se manejarán desde los módulos específicos que iremos creando 💙.
                </p>
            </div>

        </div>
    </div>
</div>
@endsection
