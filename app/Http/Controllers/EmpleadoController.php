<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmpleadoStoreRequest;
use App\Http\Requests\EmpleadoUpdateRequest;
use App\Models\Departamento;
use App\Models\Empleado;
use App\Models\Puesto;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $search        = $request->input('search');
        $estado        = $request->input('estado');
        $departamentoId = $request->input('id_departamento');
        $puestoId      = $request->input('id_puesto');

        $query = Empleado::with(['departamento', 'puesto']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('correo', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('curp', 'like', "%{$search}%")
                    ->orWhere('rfc', 'like', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        if ($departamentoId) {
            $query->where('id_departamento', $departamentoId);
        }

        if ($puestoId) {
            $query->where('id_puesto', $puestoId);
        }

        $empleados = $query
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->paginate(10)
            ->appends($request->query());

        $departamentos = Departamento::orderBy('nombre')->get();
        $puestos       = Puesto::orderBy('nombre')->get();

        return view('empleados.index', compact(
            'empleados',
            'departamentos',
            'puestos',
            'search',
            'estado',
            'departamentoId',
            'puestoId'
        ));
    }

    public function create()
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $puestos       = Puesto::orderBy('nombre')->get();

        return view('empleados.create', compact('departamentos', 'puestos'));
    }

    public function store(EmpleadoStoreRequest $request)
    {
        Empleado::create($request->validated());

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function show(Empleado $empleado)
    {
        $empleado->load(['departamento', 'puesto', 'usuario']);

        return view('empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        $departamentos = Departamento::orderBy('nombre')->get();
        $puestos       = Puesto::orderBy('nombre')->get();

        return view('empleados.edit', compact('empleado', 'departamentos', 'puestos'));
    }

    public function update(EmpleadoUpdateRequest $request, Empleado $empleado)
    {
        $empleado->update($request->validated());

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        // Si no quieres borrar físicamente, podrías bloquearo o marcar INACTIVO.
        $empleado->delete();

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }

    public function toggleEstado(Empleado $empleado)
    {
        $empleado->estado = $empleado->estado === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
        $empleado->save();

        return redirect()
            ->back()
            ->with('success', 'Estado del empleado actualizado correctamente.');
    }
}
