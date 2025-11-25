@extends('layouts.app')

@section('title', 'Editar día festivo – RH')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-900 py-10">
    <div class="max-w-3xl mx-auto px-4 space-y-6">

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('rh.festivos.index', ['anio' => $festivo->fecha->year]) }}"
               class="text-xs text-slate-300 hover:text-white transition-colors">
                ← Volver al listado
            </a>
        </div>

        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-200 to-sky-600"></div>
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="absolute inset-0 backdrop-blur-2xl"></div>

            <div class="relative text-slate-50">

                <div class="px-6 py-4 border-b border-white/20">
                    <h1 class="text-xl font-semibold tracking-tight">
                        Editar día festivo
                    </h1>
                    <p class="mt-1 text-xs text-slate-200/80">
                        Actualiza la información del día festivo registrado.
                    </p>
                </div>

                <div class="px-6 py-5 text-sm space-y-4">
                    @if($errors->any())
                        <div class="mb-3 p-3 rounded-xl text-xs text-red-100 bg-red-700/70 border border-red-400 shadow">
                            <ul class="mb-0 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('rh.festivos.update', $festivo->id_festivo) }}"
                          method="POST"
                          class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                    Fecha
                                </label>
                                <input type="date" name="fecha"
                                       value="{{ old('fecha', $festivo->fecha->format('Y-m-d')) }}"
                                       class="w-full rounded-xl bg-black/30 border border-white/20 text-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300/80 focus:border-emerald-300">
                            </div>

                            <div>
                                <label class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                    Tipo de día
                                </label>
                                <div class="flex items-center gap-3 mt-1">
                                    <label class="inline-flex items-center gap-2 text-xs">
                                        <input type="checkbox" name="es_nacional" value="1"
                                               class="rounded border-white/40 bg-black/40"
                                               {{ old('es_nacional', $festivo->es_nacional ? '1' : '') ? 'checked' : '' }}>
                                        <span>Día festivo nacional</span>
                                    </label>
                                </div>
                                <p class="mt-1 text-[0.7rem] text-slate-200/80">
                                    Si lo desmarcas, se considerará un día festivo local/interno.
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[0.7rem] uppercase tracking-wide text-slate-100/80 mb-1">
                                Nombre del día festivo
                            </label>
                            <input type="text" name="nombre"
                                   value="{{ old('nombre', $festivo->nombre) }}"
                                   class="w-full rounded-xl bg-black/30 border border-white/20 text-slate-50 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-300/80 focus:border-emerald-300">
                        </div>

                        <div class="flex justify-end gap-3 pt-3">
                            <a href="{{ route('rh.festivos.index', ['anio' => $festivo->fecha->year]) }}"
                               class="inline-flex items-center px-4 py-2 rounded-full bg-black/40 text-slate-50 text-xs font-semibold border border-white/25 hover:bg-black/60 transition-all">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-full bg-emerald-400/95 text-slate-900 text-xs font-semibold shadow hover:bg-emerald-300 hover:-translate-y-0.5 transition-all">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
