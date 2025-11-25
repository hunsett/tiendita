<?php

namespace App\Http\Controllers;

use App\Models\DiaFestivo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DiasFestivosController extends Controller
{
    private function getRhUser()
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'No autenticado.');
        }

        if ($user->rol !== 'RH') {
            abort(403, 'Solo RH puede administrar días festivos.');
        }

        return $user;
    }

    /**
     * Listado de días festivos con filtros por año y búsqueda.
     */
    public function index(Request $request)
    {
        $this->getRhUser();

        $anioSeleccionado = (int) ($request->input('anio') ?: now()->year);

        $query = DiaFestivo::query();

        // Filtro por año
        if ($anioSeleccionado) {
            $query->whereYear('fecha', $anioSeleccionado);
        }

        // Filtro por texto
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre', 'like', "%{$q}%")
                    ->orWhere('fecha', 'like', "%{$q}%");
            });
        }

        $festivos = $query->orderBy('fecha', 'asc')->paginate(15);

        // Años disponibles para el selector
        $aniosDisponibles = DiaFestivo::selectRaw('YEAR(fecha) as anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        return view('rh.festivos.index', [
            'festivos'         => $festivos,
            'anioSeleccionado' => $anioSeleccionado,
            'aniosDisponibles' => $aniosDisponibles,
        ]);
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $this->getRhUser();

        return view('rh.festivos.create');
    }

    /**
     * Guardar nuevo día festivo.
     */
    public function store(Request $request)
    {
        $this->getRhUser();

        $data = $request->validate([
            'fecha'       => ['required', 'date', 'unique:dias_festivos,fecha'],
            'nombre'      => ['required', 'string', 'max:255'],
            'es_nacional' => ['nullable', 'boolean'],
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.unique'   => 'Ya existe un día festivo registrado para esa fecha.',
            'nombre.required' => 'El nombre del día festivo es obligatorio.',
        ]);

        $data['es_nacional'] = $request->boolean('es_nacional', true);

        DiaFestivo::create($data);

        return redirect()
            ->route('rh.festivos.index', ['anio' => Carbon::parse($data['fecha'])->year])
            ->with('success', 'Día festivo creado correctamente.');
    }

    /**
     * Formulario de edición.
     */
    public function edit($id)
    {
        $this->getRhUser();

        $festivo = DiaFestivo::findOrFail($id);

        return view('rh.festivos.edit', compact('festivo'));
    }

    /**
     * Actualizar día festivo.
     */
    public function update(Request $request, $id)
    {
        $this->getRhUser();

        $festivo = DiaFestivo::findOrFail($id);

        $data = $request->validate([
            'fecha'       => [
                'required',
                'date',
                Rule::unique('dias_festivos', 'fecha')->ignore($festivo->id_festivo, 'id_festivo'),
            ],
            'nombre'      => ['required', 'string', 'max:255'],
            'es_nacional' => ['nullable', 'boolean'],
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.unique'   => 'Ya existe un día festivo registrado para esa fecha.',
            'nombre.required' => 'El nombre del día festivo es obligatorio.',
        ]);

        $data['es_nacional'] = $request->boolean('es_nacional', true);

        $festivo->update($data);

        return redirect()
            ->route('rh.festivos.index', ['anio' => $festivo->fecha->year])
            ->with('success', 'Día festivo actualizado correctamente.');
    }

    /**
     * Eliminar día festivo.
     */
    public function destroy($id)
    {
        $this->getRhUser();

        $festivo = DiaFestivo::findOrFail($id);
        $anio    = $festivo->fecha->year;

        $festivo->delete();

        return redirect()
            ->route('rh.festivos.index', ['anio' => $anio])
            ->with('success', 'Día festivo eliminado correctamente.');
    }
}
