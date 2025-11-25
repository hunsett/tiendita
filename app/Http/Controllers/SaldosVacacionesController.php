<?php

namespace App\Http\Controllers;

use App\Models\SaldoVacaciones;
use App\Models\Empleado;
use App\Models\SolicitudVacaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class SaldosVacacionesController extends Controller
{
    private function getRhUser()
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'No autenticado.');
        }

        if ($user->rol !== 'RH') {
            abort(403, 'Solo usuarios RH pueden acceder a esta sección.');
        }

        return $user;
    }

    /**
     * Vista principal de saldos (por empleado y año)
     */
    public function index(Request $request)
    {
        $this->getRhUser();

        $anioSeleccionado = (int) ($request->input('anio') ?: now()->year);

        $periodoInicio = Carbon::create($anioSeleccionado, 1, 1)->toDateString();
        $periodoFin    = Carbon::create($anioSeleccionado, 12, 31)->toDateString();

        $query = SaldoVacaciones::with(['empleado.departamento', 'empleado.puesto'])
            ->where('periodo_inicio', $periodoInicio)
            ->where('periodo_fin', $periodoFin);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('empleado', function ($sub) use ($q) {
                $sub->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%")
                    ->orWhere('correo', 'like', "%{$q}%");
            });
        }

        $saldos = $query
            ->orderByRaw('COALESCE(empleados.codigo, 99999)')
            ->join('empleados', 'saldos_vacaciones.id_empleado', '=', 'empleados.id_empleado')
            ->select('saldos_vacaciones.*')
            ->paginate(10);

        // Años disponibles (según periodos creados)
        $aniosDisponibles = SaldoVacaciones::selectRaw('YEAR(periodo_inicio) as anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('rh.saldos.index', [
            'saldos'           => $saldos,
            'anioSeleccionado' => $anioSeleccionado,
            'aniosDisponibles' => $aniosDisponibles,
        ]);
    }

    /**
     * Vista de detalle por empleado y periodo.
     * Usa query param ?saldo_id=... para seleccionar periodo.
     */
    public function show($id_empleado, Request $request)
    {
        $this->getRhUser();

        $empleado = Empleado::with(['departamento', 'puesto'])
            ->where('id_empleado', $id_empleado)
            ->firstOrFail();

        $saldos = SaldoVacaciones::where('id_empleado', $empleado->id_empleado)
            ->orderBy('periodo_inicio', 'desc')
            ->get();

        if ($saldos->isEmpty()) {
            return redirect()
                ->route('rh.saldos.index')
                ->with('success', 'Este colaborador aún no tiene periodos de saldo configurados.');
        }

        $saldoSeleccionado = null;

        if ($request->filled('saldo_id')) {
            $saldoSeleccionado = $saldos->firstWhere('id_saldo', $request->saldo_id);
        }

        if (!$saldoSeleccionado) {
            $saldoSeleccionado = $saldos->first();
        }

        // Solicitudes aprobadas que afectan ese periodo
        $solicitudes = SolicitudVacaciones::where('id_empleado', $empleado->id_empleado)
            ->where('estado', 'APROBADA')
            ->whereBetween('fecha_inicio', [
                $saldoSeleccionado->periodo_inicio->toDateString(),
                $saldoSeleccionado->periodo_fin->toDateString(),
            ])
            ->orderBy('fecha_inicio', 'asc')
            ->get();

        return view('rh.saldos.show', [
            'empleado'         => $empleado,
            'saldos'           => $saldos,
            'saldoSeleccionado' => $saldoSeleccionado,
            'solicitudes'      => $solicitudes,
        ]);
    }

    /**
     * Generar/renovar periodos anuales de saldo para todos los empleados ACTIVO.
     * - Si el periodo ya existe para un empleado, se omite.
     * - Calcula días acumulados según antigüedad (puedes ajustar la política).
     */
    public function generarPeriodos(Request $request)
    {
        $this->getRhUser();

        $data = $request->validate([
            'anio' => ['required', 'integer', 'min:2000', 'max:2100'],
        ], [
            'anio.required' => 'Debes indicar el año del periodo.',
        ]);

        $anio = (int) $data['anio'];

        $periodoInicio = Carbon::create($anio, 1, 1)->toDateString();
        $periodoFin    = Carbon::create($anio, 12, 31)->toDateString();

        $empleados = Empleado::where('estado', 'ACTIVO')->get();

        if ($empleados->isEmpty()) {
            throw ValidationException::withMessages([
                'anio' => 'No hay empleados activos para generar saldos.',
            ]);
        }

        $creados = 0;
        $omitidos = 0;

        DB::transaction(function () use ($empleados, $anio, $periodoInicio, $periodoFin, &$creados, &$omitidos) {
            foreach ($empleados as $empleado) {
                $yaExiste = SaldoVacaciones::where('id_empleado', $empleado->id_empleado)
                    ->where('periodo_inicio', $periodoInicio)
                    ->where('periodo_fin', $periodoFin)
                    ->exists();

                if ($yaExiste) {
                    $omitidos++;
                    continue;
                }

                // Antigüedad al inicio del periodo
                $aniosAntiguedad = 0;
                if ($empleado->fecha_ingreso) {
                    $fi = Carbon::parse($empleado->fecha_ingreso);
                    $aniosAntiguedad = $fi->diffInYears(Carbon::create($anio, 1, 1));
                }

                $diasAcumulados = $this->calcularDiasPorAntiguedad($aniosAntiguedad);

                SaldoVacaciones::create([
                    'id_empleado'      => $empleado->id_empleado,
                    'periodo_inicio'   => $periodoInicio,
                    'periodo_fin'      => $periodoFin,
                    'dias_acumulados'  => $diasAcumulados,
                    'dias_usados'      => 0,
                    'dias_disponibles' => $diasAcumulados,
                ]);

                $creados++;
            }
        });

        return redirect()
            ->route('rh.saldos.index', ['anio' => $anio])
            ->with('success', "Periodos generados para {$anio}. Creados: {$creados}, omitidos (ya existían): {$omitidos}.");
    }

    /**
     * Política básica de días por antigüedad.
     * Ajusta esto según la política real de Tienda Mary / LFT.
     */
    private function calcularDiasPorAntiguedad(int $aniosAntiguedad): int
    {
        if ($aniosAntiguedad <= 0) {
            return 0;
        }

        if ($aniosAntiguedad === 1) {
            return 6;
        }

        if ($aniosAntiguedad === 2) {
            return 8;
        }

        if ($aniosAntiguedad === 3) {
            return 10;
        }

        if ($aniosAntiguedad === 4) {
            return 12;
        }

        // 5 o más años
        return 14;
    }
}
