<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Tienda Mary')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 👇 Aquí inyectamos los estilos específicos de cada vista (gradient glass, etc.) --}}
    @yield('styles')
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">

        {{-- Navbar tipo píldora centrada --}}
        <header class="w-full flex justify-center pt-6 pb-4">
            <div class="w-full max-w-4xl px-4">
                <div class="flex items-center bg-gray-900 text-white rounded-full px-4 py-2 shadow-xl gap-6">

                    {{-- Avatar / logo izquierda --}}
                    @auth
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-gray-900 text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->usuario, 0, 1)) }}
                        </div>
                    @else
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white/10 text-xs font-semibold">
                            TM
                        </div>
                    @endauth

                    {{-- Menú central --}}
                    <nav class="flex-1">
                        <ul class="flex items-center gap-6 text-sm">
                            <li>
                                <a href="{{ route('dashboard') }}"
                                   class="hover:text-gray-200 transition-colors">
                                    Dashboard
                                </a>
                            </li>

                            @auth
                                @if(auth()->user()->rol === 'ADMIN')
                                    <li>
                                        <a href="{{ route('departamentos.index') }}"
                                           class="hover:text-gray-200 transition-colors">
                                            Departamentos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('puestos.index') }}"
                                           class="hover:text-gray-200 transition-colors">
                                            Puestos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('usuarios.index') }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 text-slate-900 font-semibold text-xs shadow hover:bg-white">
                                            Usuarios
                                        </a>
                                    </li>
                                @endif

                                @if(in_array(auth()->user()->rol, ['RH']))
                                    <li>
                                        <a href="{{ route('rh.empleados.index') }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 text-slate-900 font-semibold text-xs shadow hover:bg-white">
                                            Empleados
                                        </a>
                                    </li>
                                     <li>
                                        <a href="{{ route('rh.aprobaciones.index') }}"
                                        class="hover:text-gray-200 transition-colors text-xs md:text-sm">
                                            Aprobaciones RH
                                        </a>
                                    </li>
                                    {{-- Link a saldos de vacaciones --}}
                                    <li>
                                        <a href="{{ route('rh.saldos.index') }}"
                                        class="hover:text-gray-200 transition-colors text-xs md:text-sm">
                                            Saldos de vacaciones
                                        </a>
                                    </li>
                                @endif
                            @endauth
                        </ul>
                    </nav>

                    {{-- Pill derecha con usuario + botón salir --}}
                    @auth
                        <div class="flex items-center gap-3">
                            <div class="bg-white text-gray-900 rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap">
                                {{ auth()->user()->correo_sistema ?? auth()->user()->usuario }}
                            </div>

                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="text-xs text-gray-300 hover:text-white transition-colors">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    @endauth

                </div>
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
