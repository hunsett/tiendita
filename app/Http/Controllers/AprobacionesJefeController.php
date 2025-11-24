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
        $jefe = $this->getJefe();          // método que ya tienes
        $empleadoJefe = $jefe->empleado;   // empleado ligado al jefe

        $data = $request->validate([
            'accion'    => ['required', 'in:APRUEBA,RECHAZA'],
            'comentario' => ['nullable', 'string', 'max:2000'],
        ], [
            'accion.required' => 'Debes seleccionar una acción.',
            'accion.in'       => 'La acción no es válida.',
        ]);

        // Si rechaza, comentario obligatorio
        if ($data['accion'] === 'RECHAZA' && empty($data['comentario'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'comentario' => 'Para rechazar, debes indicar un comentario.',
            ]);
        }

        // Solicitud debe estar PENDIENTE y pertenecer a su equipo (mismo departamento)
        $solicitud = \App\Models\SolicitudVacaciones::with(['empleado', 'aprobaciones'])
            ->where('id_solicitud', $id)
            ->where('estado', 'PENDIENTE')
            ->whereHas('empleado', function ($q) use ($empleadoJefe) {
                $q->where('id_departamento', $empleadoJefe->id_departamento);
            })
            ->firstOrFail();

        // Verificar que este jefe no haya decidido antes (nivel 1)
        $yaDecidioEsteJefe = $solicitud->aprobaciones()
            ->where('nivel', 1)
            ->where('id_usuario_aprobador', $jefe->id_usuario)
            ->exists();

        if ($yaDecidioEsteJefe) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'accion' => 'Ya registraste una decisión sobre esta solicitud.',
            ]);
        }

        DB::transaction(function () use ($solicitud, $jefe, $data) {

            // 1. Registrar aprobación / rechazo de JEFE (nivel 1)
            Aprobacion::create([
                'id_solicitud'         => $solicitud->id_solicitud,
                'nivel'                => 1, // JEFE
                'id_usuario_aprobador' => $jefe->id_usuario,
                'accion'               => $data['accion'],
                'comentario'           => $data['comentario'] ?? null,
                'accion_en'            => now(),
            ]);

            // 2. Actualizar estado solo si RECHAZA
            if ($data['accion'] === 'RECHAZA') {
                // Rechazo definitivo: ya no pasa a RH
                $solicitud->estado      = 'RECHAZADA';
                $solicitud->decidida_en = now(); // decisión final
                $solicitud->save();
            }
            // Si APRUEBA:
            // - La solicitud permanece en estado PENDIENTE
            // - RH la verá en su bandeja (porque tiene nivel 1 = APRUEBA y aún no tiene nivel 2)
            // - No tocamos 'decidida_en' ni saldo aquí
        });

        return redirect()
            ->route('jefe.aprobaciones.show', $solicitud->id_solicitud)
            ->with('success', $data['accion'] === 'APRUEBA'
                ? 'Has aprobado la solicitud. Ahora pasará al flujo de Recursos Humanos.'
                : 'Has rechazado la solicitud. El proceso se ha dado por terminado.');
    }

    /** Descuenta los días del saldo del empleado al aprobar */
}
