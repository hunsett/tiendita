<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\SolicitudVacaciones;
use App\Models\DiaFestivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $usuario = auth()->user(); // instancia de Usuario
        $rol = $usuario->rol;

        // Cargamos el empleado con su departamento y puesto
        $empleado = $usuario->empleado()
            ->with(['departamento', 'puesto'])
            ->first();

        $hoy = Carbon::today();

        // Valores comunes
        $saldoActual = null;
        $proximasVacaciones = collect();
        $misSolicitudesRecientes = collect();

        if ($empleado) {
            $saldoActual = $empleado->saldosVacaciones()
                ->orderByDesc('periodo_inicio')
                ->first();

            $proximasVacaciones = $empleado->solicitudesVacaciones()
                ->where('estado', 'APROBADA')
                ->whereDate('fecha_inicio', '>=', $hoy)
                ->orderBy('fecha_inicio')
                ->take(5)
                ->get();

            $misSolicitudesRecientes = $empleado->solicitudesVacaciones()
                ->orderByDesc('created_at')
                ->take(5)
                ->get();
        }

        $proximosFestivos = DiaFestivo::whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha')
            ->take(5)
            ->get();

        // Métricas según el rol
        $totalEmpleados = null;
        $empleadosActivos = null;
        $solPendientes = null;
        $solAprobadasMes = null;
        $solicitudesPendientesListado = collect();
        $empleadosPorDepto = collect();
        $solicitudesPendientesAprobar = collect();

        if (in_array($rol, ['ADMIN', 'RH'])) {
            $totalEmpleados = Empleado::count();
            $empleadosActivos = Empleado::where('estado', 'ACTIVO')->count();
            $solPendientes = SolicitudVacaciones::where('estado', 'PENDIENTE')->count();

            $solAprobadasMes = SolicitudVacaciones::where('estado', 'APROBADA')
                ->whereMonth('fecha_inicio', $hoy->month)
                ->whereYear('fecha_inicio', $hoy->year)
                ->count();

            $solicitudesPendientesListado = SolicitudVacaciones::with('empleado')
                ->where('estado', 'PENDIENTE')
                ->orderBy('fecha_inicio')
                ->take(10)
                ->get();

            $empleadosPorDepto = Empleado::select(
                    'id_departamento',
                    DB::raw('count(*) as total')
                )
                ->groupBy('id_departamento')
                ->with('departamento')
                ->get();
        } elseif ($rol === 'JEFE') {
            // Por ahora, algo simple: todas las pendientes.
            // Luego puedes filtrarlas por equipo cuando tengas esa lógica.
            $solicitudesPendientesAprobar = SolicitudVacaciones::with('empleado')
                ->where('estado', 'PENDIENTE')
                ->orderBy('fecha_inicio')
                ->take(10)
                ->get();
        }

        return view('dashboard.index', [
            'usuario' => $usuario,
            'empleado' => $empleado,
            'rol' => $rol,
            'saldoActual' => $saldoActual,
            'proximasVacaciones' => $proximasVacaciones,
            'misSolicitudesRecientes' => $misSolicitudesRecientes,
            'proximosFestivos' => $proximosFestivos,

            // métricas admin / rh
            'totalEmpleados' => $totalEmpleados,
            'empleadosActivos' => $empleadosActivos,
            'solPendientes' => $solPendientes,
            'solAprobadasMes' => $solAprobadasMes,
            'solicitudesPendientesListado' => $solicitudesPendientesListado,
            'empleadosPorDepto' => $empleadosPorDepto,

            // métricas jefe
            'solicitudesPendientesAprobar' => $solicitudesPendientesAprobar,
        ]);
    }
}
