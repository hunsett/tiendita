@extends('layouts.app')

@section('title', 'Departamentos')

@section('content')
<div class="container">
    <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 1rem;">
        <h1>Departamentos</h1>
        <a href="{{ route('departamentos.create') }}" class="btn btn-primary">
            + Nuevo departamento
        </a>
    </div>

    @if ($departamentos->count())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th style="width: 180px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departamentos as $departamento)
                    <tr>
                        <td>{{ $departamento->id_departamento }}</td>
                        <td>{{ $departamento->nombre }}</td>
                        <td>
                            <a href="{{ route('departamentos.edit', $departamento) }}" class="btn btn-sm btn-warning">
                                Editar
                            </a>

                            <form action="{{ route('departamentos.destroy', $departamento) }}"
                                  method="POST"
                                  style="display:inline-block;"
                                  onsubmit="return confirm('¿Seguro que deseas eliminar este departamento?');">
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

        {{ $departamentos->links() }}
    @else
        <p>No hay departamentos registrados todavía.</p>
    @endif
</div>
@endsection
