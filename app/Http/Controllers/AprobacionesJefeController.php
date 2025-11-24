<?php

namespace App\Http\Controllers;

use App\Models\SolicitudVacaciones;
use App\Models\Aprobacion;
use App\Models\SaldoVacaciones;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AprobacionesJefeController extends Controller
{
    /** Obtiene el usuario JEFE actual y su empleado */
    private function getJefe()
    {
        $usuario = auth()->user() ?? session('usuario');

        if (!$usuario) {
            abort(401, 'No autenticado.');
        }

        // Asegurar que es objeto Eloquent
        if (!is_object($usuario)) {
            $usuario = Usuario::findOrFail($usuario['id_usuario']);
        }

        if ($usuario->rol !== 'JEFE') {
            abort(403, 'Solo los usuarios con rol JEFE pueden acceder a este módulo.');
        }

        if (!$usuario->empleado) {
            abort(500, 'El usuario JEFE no está ligado a un empleado.');
        }

        return $usuario;
    }

    /** Bandeja de solicitudes pendientes del equipo */
    public function index(Request $request)
    {
        $jefe = $this->getJefe();
        $empleadoJefe = $jefe->empleado;

        $query = SolicitudVacaciones::with(['empleado'])
            ->where('estado', 'PENDIENTE')
            ->whereHas('empleado', function ($q) use ($empleadoJefe) {
                $q->where('id_departamento', $empleadoJefe->id_departamento);
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

        return view('jefe.aprobaciones.index', compact('solicitudes'));
    }

    /** Detalle de solicitud de un colaborador */
    public function show($id)
    {
        $jefe = $this->getJefe();
        $empleadoJefe = $jefe->empleado;

        $solicitud = SolicitudVacaciones::with([
            'empleado',
            'aprobaciones.aprobador.empleado',
        ])
            ->where('id_solicitud', $id)
            ->whereHas('empleado', function ($q) use ($empleadoJefe) {
                $q->where('id_departamento', $empleadoJefe->id_departamento);
            })
            ->firstOrFail();

        return view('jefe.aprobaciones.show', compact('solicitud', 'jefe'));
    }

    /** Aprobar / Rechazar con comentario */
    public function decidir($id, Request $request)
    {
        $jefe = $this->getJefe();
        $empleadoJefe = $jefe->empleado;

        $data = $request->validate([
            'accion'    => ['required', 'in:APRUEBA,RECHAZA'],
            'comentario' => ['nullable', 'string', 'max:2000'],
        ], [
            'accion.required' => 'Debes seleccionar una acción.',
            'accion.in'       => 'La acción no es válida.',
        ]);

        if ($data['accion'] === 'RECHAZA' && empty($data['comentario'])) {
            throw ValidationException::withMessages([
                'comentario' => 'Para rechazar, debes indicar un comentario.',
            ]);
        }

        $solicitud = SolicitudVacaciones::with(['empleado', 'aprobaciones'])
            ->where('id_solicitud', $id)
            ->where('estado', 'PENDIENTE')
            ->whereHas('empleado', function ($q) use ($empleadoJefe) {
                $q->where('id_departamento', $empleadoJefe->id_departamento);
            })
            ->firstOrFail();

        // Verificar que este jefe no haya aprobado/rechazado antes (nivel 1)
        $yaAprobo = $solicitud->aprobaciones()
            ->where('nivel', 1)
            ->where('id_usuario_aprobador', $jefe->id_usuario)
            ->exists();

        if ($yaAprobo) {
            throw ValidationException::withMessages([
                'accion' => 'Ya registraste una decisión sobre esta solicitud.',
            ]);
        }

        DB::transaction(function () use ($solicitud, $jefe, $data) {

            // 1. Registrar la aprobación/rechazo
            Aprobacion::create([
                'id_solicitud'       => $solicitud->id_solicitud,
                'nivel'              => 1, // JEFE
                'id_usuario_aprobador' => $jefe->id_usuario,
                'accion'             => $data['accion'],
                'comentario'         => $data['comentario'] ?? null,
                'accion_en'          => now(),
            ]);

            // 2. Actualizar estado de la solicitud
            if ($data['accion'] === 'APRUEBA') {
                $this->aplicarSaldo($solicitud); // descuenta días
                $solicitud->estado = 'APROBADA'; // aquí podrías dejar PENDIENTE para RH (nivel 2) si quisieras flujo de 2 niveles
            } else {
                $solicitud->estado = 'RECHAZADA';
            }

            $solicitud->decidida_en = now();
            $solicitud->save();
        });

        return redirect()
            ->route('jefe.aprobaciones.show', $solicitud->id_solicitud)
            ->with('success', $data['accion'] === 'APRUEBA'
                ? 'Has aprobado la solicitud correctamente.'
                : 'Has rechazado la solicitud.');
    }

    /** Descuenta los días del saldo del empleado al aprobar */
    private function aplicarSaldo(SolicitudVacaciones $solicitud): void
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

        if ($solicitud->dias_solicitados > $saldo->dias_disponibles) {
            throw ValidationException::withMessages([
                'accion' => 'El colaborador no tiene días suficientes. Disponibles: ' . $saldo->dias_disponibles,
            ]);
        }

        $saldo->dias_usados       += $solicitud->dias_solicitados;
        $saldo->dias_disponibles   = $saldo->dias_acumulados - $saldo->dias_usados;
        $saldo->save();
    }
}
