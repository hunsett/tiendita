@extends('layouts.app')

@section('title', 'Editar puesto')

@section('content')
<div class="container">
    <h1>Editar puesto</h1>

    <form action="{{ route('puestos.update', $puesto) }}" method="POST" style="max-width: 500px;">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del puesto</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $puesto->nombre) }}"
                required
            >

            @error('nombre')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Actualizar
        </button>
        <a href="{{ route('puestos.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
    </form>
</div>
@endsection
