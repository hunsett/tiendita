@extends('layouts.app')

@section('title', 'Nuevo puesto')

@section('content')
<div class="container">
    <h1>Nuevo puesto</h1>

    <form action="{{ route('puestos.store') }}" method="POST" style="max-width: 500px;">
        @csrf

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del puesto</label>
            <input
                type="text"
                id="nombre"
                name="nombre"
                class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre') }}"
                required
            >

            @error('nombre')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">
            Guardar
        </button>
        <a href="{{ route('puestos.index') }}" class="btn btn-secondary">
            Cancelar
        </a>
    </form>
</div>
@endsection
