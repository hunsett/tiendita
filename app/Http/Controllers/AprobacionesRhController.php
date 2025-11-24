<?php

namespace App\Http\Controllers;

use App\Models\SolicitudVacaciones;
use App\Models\Aprobacion;
use App\Models\SaldoVacaciones;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AprobacionesRhController extends Controller
{
    /** Usuario autenticado con rol RH */
    private function getRhUser(): Usuario
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'No autenticado.');
        }

        if ($user->rol !== 'RH') {
            abort(403, 'Solo usuarios RH pueden acceder a este módulo.');
        }

        return $user;
    }

    /**
     * Bandeja de solicitudes pendientes de RH
     * Regla: solicitudes en estado PENDIENTE, ya aprobadas por Jefe (nivel 1 APRUEBA),
     * y aún sin decisión de RH (sin nivel 2).
     */
    public function index(Request $request)
    {
        $this->getRhUser(); // Solo valida el rol

        $query = SolicitudVacaciones::with(['empleado', 'aprobaciones.aprobador'])
            ->where('estado', 'PENDIENTE')
            ->whereHas('aprobaciones', function ($q) {
                $q->where('nivel', 1)
                    ->where('accion', 'APRUEBA');
            })
            ->whereDoesntHave('aprobaciones', function ($q) {
                $q->where('nivel', 2);
            });

        if ($request->filled('q')) {
            $qSearch = $request->q;
            $query->whereHas('empleado', function ($q) use ($qSearch) {
                $q->where('nombre', 'like', "%{$qSearch}%")
                    ->orWhere('apellidos', 'like', "%{$qSearch}%")
                    ->orWhere('codigo', 'like', "%{$qSearch}%");
            });
        }

        if ($request->filled('desde')) {
            $query->where('fecha_inicio', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->where('fecha_fin', '<=', $request->hasta);
        }

        $solicitudes = $query
            ->orderBy('fecha_inicio', 'asc')
            ->paginate(10);

        return view('rh.aprobaciones.index', compact('solicitudes'));
    }

    /** Detalle + traza de aprobaciones */
    public function show($id)
    {
        $this->getRhUser();

        $solicitud = SolicitudVacaciones::with([
            'empleado',
            'aprobaciones.aprobador.empleado',
        ])
            ->where('id_solicitud', $id)
            ->firstOrFail();

        return view('rh.aprobaciones.show', compact('solicitud'));
    }

    /**
     * Decisión de RH (nivel 2)
     * - APRUEBA => puede ajustar días, descuenta saldo, marca APROBADA
     * - RECHAZA => marca RECHAZADA
     */
    public function decidir($id, Request $request)
    {
        $rh = $this->getRhUser();

        $data = $request->validate([
            'accion'         => ['required', 'in:APRUEBA,RECHAZA'],
            'comentario'     => ['nullable', 'string', 'max:2000'],
            'dias_aprobados' => ['nullable', 'numeric', 'min:0.5'],
        ], [
            'accion.required' => 'Debes seleccionar una acción.',
            'accion.in'       => 'La acción seleccionada no es válida.',
            'dias_aprobados.numeric' => 'El campo de días aprobados debe ser numérico.',
        ]);

        if ($data['accion'] === 'RECHAZA' && empty($data['comentario'])) {
            throw ValidationException::withMessages([
                'comentario' => 'Para rechazar, debes indicar un comentario.',
            ]);
        }

        // Obtener solicitud: debe estar pendiente y ya aprobada por Jefe
        $solicitud = SolicitudVacaciones::with(['empleado', 'aprobaciones'])
            ->where('id_solicitud', $id)
            ->where('estado', 'PENDIENTE')
            ->whereHas('aprobaciones', function ($q) {
                $q->where('nivel', 1)
                    ->where('accion', 'APRUEBA');
            })
            ->firstOrFail();

        // Validar que este RH no haya registrado ya una decisión nivel 2
        $yaHayDecisionRh = $solicitud->aprobaciones()
            ->where('nivel', 2)
            ->exists();

        if ($yaHayDecisionRh) {
            throw ValidationException::withMessages([
                'accion' => 'Esta solicitud ya tiene una decisión registrada por RH.',
            ]);
        }

        DB::transaction(function () use ($solicitud, $rh, $data) {

            // Crear registro de aprobación de RH (nivel 2)
            Aprobacion::create([
                'id_solicitud'        => $solicitud->id_solicitud,
                'nivel'               => 2,
                'id_usuario_aprobador' => $rh->id_usuario,
                'accion'              => $data['accion'],
                'comentario'          => $data['comentario'] ?? null,
                'accion_en'           => now(),
            ]);

            if ($data['accion'] === 'APRUEBA') {
                // Ajuste de días usados (si aplica)
                $diasAprobados = $data['dias_aprobados'] ?? $solicitud->dias_solicitados;

                // Validaciones de días
                if ($diasAprobados <= 0) {
                    throw ValidationException::withMessages([
                        'dias_aprobados' => 'Los días aprobados deben ser mayores a 0.',
                    ]);
                }

                if ($diasAprobados > $solicitud->dias_solicitados) {
                    throw ValidationException::withMessages([
                        'dias_aprobados' => 'Los días aprobados no pueden ser mayores a los solicitados (' .
                            $solicitud->dias_solicitados . ').',
                    ]);
                }

                // Aplicar saldo
                $this->aplicarSaldo($solicitud, $diasAprobados);

                // Guardar valor final de días aprobados en la solicitud
                $solicitud->dias_solicitados = $diasAprobados;
                $solicitud->estado = 'APROBADA';
            } else {
                // Rechazo definitivo
                $solicitud->estado = 'RECHAZADA';
            }

            $solicitud->decidida_en = now();
            $solicitud->save();
        });

        return redirect()
            ->route('rh.aprobaciones.show', $solicitud->id_solicitud)
            ->with('success', $data['accion'] === 'APRUEBA'
                ? 'La solicitud ha sido aprobada y el saldo de vacaciones actualizado.'
                : 'La solicitud ha sido rechazada correctamente.');
    }

    /** Aplica descuento de días al saldo del empleado */
    private function aplicarSaldo(SolicitudVacaciones $solicitud, float $diasAprobados): void
    {
        $saldo = SaldoVacaciones::where('id_empleado', $solicitud->id_empleado)
            ->where('periodo_inicio', '<=', $solicitud->fecha_inicio->toDateString())
            ->where('periodo_fin', '>=', $solicitud->fecha_inicio->toDateString())
            ->orderByDesc('periodo_inicio')
            ->lockForUpdate()
            ->first();

        if (!$saldo) {
            throw ValidationException::withMessages([
                'accion' => 'No existe un saldo de vacaciones configurado para el colaborador en este periodo.',
            ]);
        }

        if ($diasAprobados > $saldo->dias_disponibles) {
            throw ValidationException::withMessages([
                'dias_aprobados' => 'El colaborador no tiene días suficientes. Disponibles: ' .
                    $saldo->dias_disponibles,
            ]);
        }

        $saldo->dias_usados      += $diasAprobados;
        $saldo->dias_disponibles = $saldo->dias_acumulados - $saldo->dias_usados;
        $saldo->save();
    }
}
