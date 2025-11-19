<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Tienda Mary')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">

        {{-- Barra superior simple --}}
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-lg">Tienda Mary</span>
                    <span class="text-xs text-gray-500">Gestión de empleados y vacaciones</span>
                </div>

                @auth
                    <div class="flex items-center gap-4">
                        <div class="text-right text-sm">
                            <div class="font-semibold">{{ auth()->user()->usuario }}</div>
                            <div class="text-gray-500 text-xs">{{ auth()->user()->rol }}</div>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-indigo-500 text-white flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->usuario, 0, 1)) }}
                        </div>
                    </div>
                @endauth
                @auth
                    @if(auth()->user()->rol === 'ADMIN')
                            <li><a href="{{ route('departamentos.index') }}">Departamentos</a></li>
                            <li><a href="{{ route('puestos.index') }}">Puestos</a></li>
                        @endif
                @endauth

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Cerrar sesión</button>
                </form>

            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="border-t bg-white">
            <div class="max-w-7xl mx-auto px-4 py-3 text-xs text-gray-500 flex justify-between">
                <span>© {{ date('Y') }} Tienda Mary</span>
                <span>Sistema de gestión desarrollado para la súper tesis 💙</span>
            </div>
        </footer>
    </div>
</body>
</html>
