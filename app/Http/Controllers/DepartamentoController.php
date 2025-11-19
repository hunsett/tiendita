<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Mostrar listado de departamentos.
     */
    public function index()
    {
        $departamentos = Departamento::orderBy('nombre')->paginate(10);

        return view('departamentos.index', compact('departamentos'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('departamentos.create');
    }

    /**
     * Guardar nuevo departamento.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:departamentos,nombre'],
        ]);

        Departamento::create($validated);

        return redirect()
            ->route('departamentos.index')
            ->with('success', 'Departamento creado correctamente.');
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Departamento $departamento)
    {
        return view('departamentos.edit', compact('departamento'));
    }

    /**
     * Actualizar departamento.
     */
    public function update(Request $request, Departamento $departamento)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                'unique:departamentos,nombre,' . $departamento->id_departamento . ',id_departamento',
            ],
        ]);

        $departamento->update($validated);

        return redirect()
            ->route('departamentos.index')
            ->with('success', 'Departamento actualizado correctamente.');
    }

    /**
     * Eliminar departamento.
     */
    public function destroy(Departamento $departamento)
    {
        $departamento->delete();

        return redirect()
            ->route('departamentos.index')
            ->with('success', 'Departamento eliminado correctamente.');
    }
}
