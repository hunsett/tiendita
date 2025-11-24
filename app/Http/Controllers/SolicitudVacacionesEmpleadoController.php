<?php

namespace App\Http\Controllers;

use App\Models\SolicitudVacaciones;
use App\Models\SaldoVacaciones;
use App\Models\DiaFestivo;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SolicitudVacacionesEmpleadoController extends Controller
{
    /**
     * Helper para obtener el empleado logueado.
     * Ajusta esto según cómo guardas el usuario en sesión.
     */
    private function getEmpleadoActual(): Empleado
    {
        // Si usas Auth estándar:
        $usuario = auth()->user();

        // Si tú guardas el usuario en sesión: session('usuario')
        if (!$usuario) {
            $usuario = session('usuario'); // Eloquent Usuario
        }

        if (!$usuario) {
            abort(401, 'No hay usuario autenticado');
        }

        $idEmpleado = is_object($usuario) ? $usuario->id_empleado : $usuario['id_empleado'];

        return Empleado::findOrFail($idEmpleado);
    }

    /** Listado "Mis solicitudes" con filtros */
    public function index(Request $request)
    {
        $empleado = $this->getEmpleadoActual();

        $query = SolicitudVacaciones::where('id_empleado', $empleado->id_empleado)
            ->orderBy('fecha_inicio', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('desde')) {
            $query->where('fecha_inicio', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->where('fecha_fin', '<=', $request->hasta);
        }

        $solicitudes = $query->paginate(10);

        $estados = ['BORRADOR', 'PENDIENTE', 'APROBADA', 'RECHAZADA', 'CANCELADA'];
        $tipos   = ['VACACIONES', 'ENFERMEDAD', 'PERMISO'];

        return view('solicitudes_empleado.index', compact('solicitudes', 'estados', 'tipos'));
    }

    /** Formulario nueva solicitud */
    public function create()
    {
        $empleado = $this->getEmpleadoActual();

        $saldoActual = $this->obtenerSaldoActual($empleado->id_empleado);

        $tipos = ['VACACIONES', 'ENFERMEDAD', 'PERMISO'];

        return view('solicitudes_empleado.create', compact('saldoActual', 'tipos'));
    }

    /** Guardar nueva solicitud (borrador o enviar) */
    public function store(Request $request)
    {
        $empleado = $this->getEmpleadoActual();

        $data = $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'tipo'         => ['required', 'in:VACACIONES,ENFERMEDAD,PERMISO'],
            'motivo'       => ['nullable', 'string', 'max:2000'],
            'accion'       => ['required', 'in:guardar,enviar'], // botón
        ], [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required'    => 'La fecha de fin es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha fin debe ser mayor o igual a la fecha inicio.',
            'tipo.in'               => 'El tipo de solicitud no es válido.',
        ]);

        $fechaInicio = Carbon::parse($data['fecha_inicio']);
        $fechaFin    = Carbon::parse($data['fecha_fin']);

        $diasSolicitados = $this->calcularDiasHabiles($fechaInicio, $fechaFin);

        if ($diasSolicitados <= 0) {
            return back()
                ->withInput()
                ->withErrors(['fecha_inicio' => 'El rango de fechas no contiene días hábiles.']);
        }

        // Si va a enviar, validar saldo
        $estado   = 'BORRADOR';
        $enviadaEn = null;

        if ($data['accion'] === 'enviar') {
            $this->validarSaldo($empleado->id_empleado, $diasSolicitados, $fechaInicio);
            $estado   = 'PENDIENTE';
            $enviadaEn = now();
        }

        SolicitudVacaciones::create([
            'id_empleado'     => $empleado->id_empleado,
            'fecha_inicio'    => $fechaInicio->toDateString(),
            'fecha_fin'       => $fechaFin->toDateString(),
            'dias_solicitados' => $diasSolicitados,
            'tipo'            => $data['tipo'],
            'estado'          => $estado,
            'motivo'          => $data['motivo'] ?? null,
            'enviada_en'      => $enviadaEn,
        ]);

        return redirect()
            ->route('solicitudes-empleado.index')
            ->with('success', $estado === 'BORRADOR'
                ? 'Solicitud guardada como borrador.'
                : 'Solicitud enviada para aprobación.');
    }

    /** Ver detalle */
    public function show($id)
    {
        $empleado = $this->getEmpleadoActual();

        $solicitud = SolicitudVacaciones::where('id_empleado', $empleado->id_empleado)
            ->where('id_solicitud', $id)
            ->firstOrFail();

        $saldoActual = $this->obtenerSaldoActual($empleado->id_empleado);

        return view('solicitudes_empleado.show', compact('solicitud', 'saldoActual'));
    }

    /** Enviar una solicitud que estaba en BORRADOR */
    public function enviar($id)
    {
        $empleado = $this->getEmpleadoActual();

        $solicitud = SolicitudVacaciones::where('id_empleado', $empleado->id_empleado)
            ->where('id_solicitud', $id)
            ->firstOrFail();

        if ($solicitud->estado !== 'BORRADOR') {
            return back()->withErrors(['general' => 'Solo puedes enviar solicitudes en borrador.']);
        }

        $fechaInicio = $solicitud->fecha_inicio instanceof Carbon
            ? $solicitud->fecha_inicio
            : Carbon::parse($solicitud->fecha_inicio);

        // Validar saldo al momento de enviar
        $this->validarSaldo($empleado->id_empleado, $solicitud->dias_solicitados, $fechaInicio);

        $solicitud->estado    = 'PENDIENTE';
        $solicitud->enviada_en = now();
        $solicitud->save();

        return redirect()
            ->route('solicitudes-empleado.show', $solicitud->id_solicitud)
            ->with('success', 'Solicitud enviada correctamente.');
    }

    /** Cancelar solicitud (según reglas) */
    public function cancelar($id, Request $request)
    {
        $empleado = $this->getEmpleadoActual();

        $solicitud = SolicitudVacaciones::where('id_empleado', $empleado->id_empleado)
            ->where('id_solicitud', $id)
            ->firstOrFail();

        if (!in_array($solicitud->estado, ['BORRADOR', 'PENDIENTE'])) {
            return back()->withErrors(['general' => 'Solo puedes cancelar solicitudes en borrador o pendientes.']);
        }

        $solicitud->estado     = 'CANCELADA';
        $solicitud->decidida_en = now();
        $solicitud->save();

        return redirect()
            ->route('solicitudes-empleado.index')
            ->with('success', 'Solicitud cancelada correctamente.');
    }

    /* ==================== Helpers internos ==================== */

    /** Calcula días hábiles excluyendo fines de semana y festivos */
    private function calcularDiasHabiles(Carbon $inicio, Carbon $fin): int
    {
        $festivos = DiaFestivo::pluck('fecha')->map(fn($f) => Carbon::parse($f)->toDateString())->toArray();

        $dias = 0;
        $fecha = $inicio->copy();

        while ($fecha->lte($fin)) {
            $esFinDeSemana = $fecha->isWeekend();
            $esFestivo     = in_array($fecha->toDateString(), $festivos);

            if (!$esFinDeSemana && !$esFestivo) {
                $dias++;
            }

            $fecha->addDay();
        }

        return $dias;
    }

    /** Obtiene el saldo actual del empleado según la fecha actual */
    private function obtenerSaldoActual(int $idEmpleado): ?SaldoVacaciones
    {
        $hoy = now()->toDateString();

        return SaldoVacaciones::where('id_empleado', $idEmpleado)
            ->where('periodo_inicio', '<=', $hoy)
            ->where('periodo_fin', '>=', $hoy)
            ->orderByDesc('periodo_inicio')
            ->first();
    }

    /** Valida que el saldo alcance para los días solicitados */
    private function validarSaldo(int $idEmpleado, float $diasSolicitados, Carbon $fechaReferencia): void
    {
        $saldo = SaldoVacaciones::where('id_empleado', $idEmpleado)
            ->where('periodo_inicio', '<=', $fechaReferencia->toDateString())
            ->where('periodo_fin', '>=', $fechaReferencia->toDateString())
            ->orderByDesc('periodo_inicio')
            ->first();

        if (!$saldo) {
            abort(422, 'No tienes un saldo de vacaciones configurado para este periodo.');
        }

        if ($diasSolicitados > $saldo->dias_disponibles) {
            abort(422, 'No tienes saldo suficiente de vacaciones. Días disponibles: ' . $saldo->dias_disponibles);
        }
    }
}
