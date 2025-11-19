<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Iniciar sesión — Tiendita</title>
  @vite('resources/css/app.css')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
  <div class="w-full max-w-sm bg-white p-6 rounded-xl shadow">
    <h1 class="text-xl font-semibold mb-4 text-center">Iniciar sesión</h1>

    @if ($errors->any())
      <div class="mb-4 text-sm text-red-600">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium">Usuario o correo</label>
        <input type="text" name="login" value="{{ old('login') }}"
               class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring"
               required autofocus>
      </div>

      <div>
        <label class="block text-sm font-medium">Contraseña</label>
        <input type="password" name="password"
               class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring"
               required>
      </div>

      <button type="submit" class="w-full rounded-lg px-4 py-2 bg-black text-white">
        Entrar
      </button>
    </form>
  </div>

  @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: '¡COMPLETADO!',
                text: {!! json_encode(session('success')) !!},
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
            });
        });
    </script>
@endif
</body>
</html>
