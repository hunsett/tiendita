<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolicitudVacacionesRequest;
use App\Models\SolicitudVacaciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SolicitudVacacionesController extends Controller
{
    /**
     * Mostrar formulario para crear una nueva solicitud de vacaciones.
     */
    public function create()
    {
        $usuario = Auth::user();
        $empleado = $usuario->empleado; // relación definida en modelo Usuario

        if (!$empleado) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Tu usuario no está vinculado a un empleado. Contacta a RH.');
        }

        // Saldo actual de vacaciones (último periodo)
        $saldoActual = $empleado->saldosVacaciones()
            ->orderByDesc('periodo_inicio')
            ->first();

        // Para base mínima, solo frenamos si no hay saldo en el formulario
        return view('vacaciones.solicitudes.create', [
            'usuario' => $usuario,
            'empleado' => $empleado,
            'saldoActual' => $saldoActual,
        ]);
    }

    /**
     * Guardar la nueva solicitud en la BD.
     */
    public function store(StoreSolicitudVacacionesRequest $request)
    {
        $usuario = Auth::user();
        $empleado = $usuario->empleado;

        if (!$empleado) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Tu usuario no está vinculado a un empleado. Contacta a RH.');
        }

        $fechaInicio = Carbon::parse($request->input('fecha_inicio'));
        $fechaFin = Carbon::parse($request->input('fecha_fin'));

        // Cálculo sencillo: días naturales entre ambas fechas (incluyendo inicio y fin)
        $diasSolicitados = $fechaInicio->diffInDays($fechaFin) + 1;

        // Validar saldo solo si es tipo VACACIONES
        if ($request->input('tipo') === 'VACACIONES') {
            $saldoActual = $empleado->saldosVacaciones()
                ->orderByDesc('periodo_inicio')
                ->first();

            if (!$saldoActual || $diasSolicitados > $saldoActual->dias_disponibles) {
                return back()
                    ->withErrors([
                        'fecha_fin' => 'No tienes suficientes días de vacaciones disponibles para esta solicitud.',
                    ])
                    ->withInput();
            }
        }

        // Crear la solicitud como PENDIENTE (ya "enviada")
        $solicitud = SolicitudVacaciones::create([
            'id_empleado' => $empleado->id_empleado,
            'fecha_inicio' => $fechaInicio->toDateString(),
            'fecha_fin' => $fechaFin->toDateString(),
            'dias_solicitados' => $diasSolicitados,
            'tipo' => $request->input('tipo'),
            'estado' => 'PENDIENTE',
            'motivo' => $request->input('motivo'),
            'enviada_en' => now(),
            'decidida_en' => null,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tu solicitud de vacaciones se ha enviado correctamente.');
    }
}
