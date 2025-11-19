@extends('layouts.app')

@section('title', 'Puestos')

@section('content')
<div class="container">
    <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 1rem;">
        <h1>Puestos</h1>
        <a href="{{ route('puestos.create') }}" class="btn btn-primary">
            + Nuevo puesto
        </a>
    </div>

    @if ($puestos->count())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th style="width: 180px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($puestos as $puesto)
                    <tr>
                        <td>{{ $puesto->id_puesto }}</td>
                        <td>{{ $puesto->nombre }}</td>
                        <td>
                            <a href="{{ route('puestos.edit', $puesto) }}" class="btn btn-sm btn-warning">
                                Editar
                            </a>

                            <form action="{{ route('puestos.destroy', $puesto) }}"
                                  method="POST"
                                  style="display:inline-block;"
                                  onsubmit="return confirm('¿Seguro que deseas eliminar este puesto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $puestos->links() }}
    @else
        <p>No hay puestos registrados todavía.</p>
    @endif
</div>
@endsection
