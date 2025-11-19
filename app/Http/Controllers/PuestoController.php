<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use Illuminate\Http\Request;

class PuestoController extends Controller
{
    /**
     * Mostrar listado de puestos.
     */
    public function index()
    {
        $puestos = Puesto::orderBy('nombre')->paginate(10);

        return view('puestos.index', compact('puestos'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('puestos.create');
    }

    /**
     * Guardar nuevo puesto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:puestos,nombre'],
        ]);

        Puesto::create($validated);

        return redirect()
            ->route('puestos.index')
            ->with('success', 'Puesto creado correctamente.');
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Puesto $puesto)
    {
        return view('puestos.edit', compact('puesto'));
    }

    /**
     * Actualizar puesto.
     */
    public function update(Request $request, Puesto $puesto)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                'unique:puestos,nombre,' . $puesto->id_puesto . ',id_puesto',
            ],
        ]);

        $puesto->update($validated);

        return redirect()
            ->route('puestos.index')
            ->with('success', 'Puesto actualizado correctamente.');
    }

    /**
     * Eliminar puesto.
     */
    public function destroy(Puesto $puesto)
    {
        $puesto->delete();

        return redirect()
            ->route('puestos.index')
            ->with('success', 'Puesto eliminado correctamente.');
    }
}
