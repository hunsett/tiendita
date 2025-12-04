<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Iniciar sesión — Tiendita</title>
  @vite('resources/css/app.css')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* --- Fondo con estrellas animadas --- */
    .stars-container {
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 0;
      overflow: hidden;
    }

    .star {
      position: absolute;
      width: 12px;
      height: 12px;
      /* Estrella de 5 picos */
      background: radial-gradient(circle, #ffffff 0%, #ffe9c4 40%, #f9d2a0 70%, rgba(255,255,255,0) 100%);
      clip-path: polygon(
        50% 0%,
        61% 35%,
        98% 35%,
        68% 57%,
        79% 91%,
        50% 72%,
        21% 91%,
        32% 57%,
        2% 35%,
        39% 35%
      );
      filter: drop-shadow(0 0 6px rgba(255, 241, 210, 0.9));
      animation: twinkleMagic 4s infinite ease-in-out alternate;
    }

    /* Posiciones y tiempos para ~20 estrellas */
    .star1  { top: 10%; left: 15%; animation-duration: 4.2s; animation-delay: 0s; }
    .star2  { top: 25%; left: 70%; animation-duration: 3.6s; animation-delay: .5s; }
    .star3  { top: 40%; left: 30%; animation-duration: 5s;   animation-delay: 1s; }
    .star4  { top: 60%; left: 80%; animation-duration: 4.8s; animation-delay: 1.5s; }
    .star5  { top: 75%; left: 20%; animation-duration: 3.8s; animation-delay: 2s; }
    .star6  { top: 15%; left: 85%; animation-duration: 4.5s; animation-delay: .2s; }
    .star7  { top: 50%; left: 10%; animation-duration: 5.2s; animation-delay: 1.2s; }
    .star8  { top: 85%; left: 60%; animation-duration: 4.1s; animation-delay: 2.4s; }

    .star9  { top: 5%;  left: 40%; animation-duration: 4.7s; animation-delay: .3s; }
    .star10 { top: 18%; left: 5%;  animation-duration: 3.9s; animation-delay: 1.8s; }
    .star11 { top: 32%; left: 90%; animation-duration: 4.3s; animation-delay: 1.1s; }
    .star12 { top: 48%; left: 55%; animation-duration: 5.4s; animation-delay: .7s; }
    .star13 { top: 63%; left: 35%; animation-duration: 3.7s; animation-delay: 2.1s; }
    .star14 { top: 72%; left: 75%; animation-duration: 4.9s; animation-delay: 1.6s; }
    .star15 { top: 88%; left: 10%; animation-duration: 4.2s; animation-delay: .9s; }
    .star16 { top: 12%; left: 55%; animation-duration: 3.5s; animation-delay: 2.6s; }
    .star17 { top: 37%; left: 15%; animation-duration: 4.6s; animation-delay: .4s; }
    .star18 { top: 55%; left: 95%; animation-duration: 5.1s; animation-delay: 2.2s; }
    .star19 { top: 80%; left: 50%; animation-duration: 3.8s; animation-delay: 1.3s; }
    .star20 { top: 92%; left: 85%; animation-duration: 4.4s; animation-delay: .6s; }

    @keyframes twinkleMagic {
      0% {
        opacity: 0.25;
        transform: translate3d(0px, 0px, 0) scale(0.7) rotate(0deg);
      }
      40% {
        opacity: 1;
        transform: translate3d(-3px, -4px, 0) scale(1.3) rotate(12deg);
      }
      70% {
        opacity: 0.9;
        transform: translate3d(2px, 3px, 0) scale(1.1) rotate(-8deg);
      }
      100% {
        opacity: 0.3;
        transform: translate3d(0px, 0px, 0) scale(0.8) rotate(0deg);
      }
    }

    /* --- Botones con glow en hover --- */
    .btn-glow {
      position: relative;
      overflow: hidden;
      transition: all .3s ease;
      box-shadow: 0 0 0 rgba(255, 255, 255, 0);
    }

    .btn-glow::before {
      content: "";
      position: absolute;
      inset: -2px;
      border-radius: 9999px;
      background: radial-gradient(circle at 0 0, rgba(255,255,255,.7), transparent 60%);
      opacity: 0;
      transform: translate3d(-120%, 0, 0);
      transition: opacity .35s ease, transform .6s ease;
    }

    .btn-glow:hover {
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.55);
      transform: translateY(-1px);
    }

    .btn-glow:hover::before {
      opacity: 1;
      transform: translate3d(120%, 0, 0);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#05081a] relative overflow-hidden">

  {{-- Estrellas animadas de fondo --}}
  <div class="stars-container">
    <span class="star star1"></span>
    <span class="star star2"></span>
    <span class="star star3"></span>
    <span class="star star4"></span>
    <span class="star star5"></span>
    <span class="star star6"></span>
    <span class="star star7"></span>
    <span class="star star8"></span>
    <span class="star star9"></span>
    <span class="star star10"></span>
    <span class="star star11"></span>
    <span class="star star12"></span>
    <span class="star star13"></span>
    <span class="star star14"></span>
    <span class="star star15"></span>
    <span class="star star16"></span>
    <span class="star star17"></span>
    <span class="star star18"></span>
    <span class="star star19"></span>
    <span class="star star20"></span>
  </div>

  {{-- Fondo diagonal naranja detrás de la tarjeta --}}
  <div class="absolute -right-56 -top-40 w-[70%] h-[70%] bg-[#c06b2c] rotate-6 z-0"></div>

  <div class="relative z-10 w-full max-w-4xl px-4">
    {{-- Tarjeta principal estilo "LUNA" --}}
    <div class="flex flex-col md:flex-row rounded-3xl shadow-2xl overflow-hidden bg-transparent">

      {{-- Lado izquierdo oscuro con ilustración y texto LUNA --}}
      <div class="md:w-1/2 bg-[#05081a]/95 backdrop-blur-sm text-white flex items-center justify-center p-10 relative">

        {{-- Estrellitas extra fijas --}}
        <span class="absolute top-6 left-10 w-1 h-1 bg-white rounded-full opacity-70"></span>
        <span class="absolute top-16 right-16 w-1 h-1 bg-white rounded-full opacity-70"></span>
        <span class="absolute bottom-10 left-20 w-1 h-1 bg-white rounded-full opacity-70"></span>
        <span class="absolute bottom-20 right-8 w-1 h-1 bg-white rounded-full opacity-70"></span>

        <div class="flex flex-col items-center space-y-6">
          <div class="w-52 h-72 border border-[#fce6cf] rounded-[2.5rem] flex items-center justify-center relative overflow-hidden">
            {{-- Luna --}}
            <div class="absolute top-12">
              <div class="w-10 h-10 rounded-full border-[3px] border-[#fce6cf] border-r-transparent border-b-transparent rotate-12"></div>
            </div>
            {{-- Estrellitas dentro del marco --}}
            <div class="absolute top-8 left-10 w-3 h-3 border border-[#fce6cf] rotate-45 rounded-sm"></div>
            <div class="absolute top-20 right-10 w-3 h-3 border border-[#fce6cf] rotate-45 rounded-sm"></div>
            <div class="absolute bottom-24 left-1/2 -translate-x-1/2 w-2 h-2 border border-[#fce6cf] rotate-45 rounded-sm"></div>
            {{-- Montañas --}}
            <svg class="absolute bottom-10 left-0 w-full" viewBox="0 0 200 80" fill="none">
              <path d="M0 60 Q40 20 80 40 T160 30 T220 60 L220 100 L0 100 Z"
                    stroke="#fce6cf" stroke-width="2" fill="none"/>
              <path d="M0 70 Q50 35 100 50 T200 65 L200 100 Z"
                    stroke="#fce6cf" stroke-width="2" fill="none"/>
            </svg>
          </div>

          <div class="tracking-[0.4em] text-sm text-[#fce6cf]">
            L U N A
          </div>
        </div>
      </div>

      {{-- Lado derecho naranja con el formulario --}}
      <div class="md:w-1/2 bg-[#c06b2c] text-white px-10 py-10 relative flex flex-col justify-center space-y-8">

        {{-- “Luna” pequeña simulando reflejo abajo --}}
        <div class="absolute -bottom-10 right-16 w-16 h-16 bg-white/70 rounded-full blur-[1px] opacity-70"></div>

        <div>
          <h1 class="text-2xl font-semibold tracking-wide mb-2">Welcome back</h1>
          <p class="text-sm text-white/80">Inicia sesión para continuar</p>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
          @csrf

          <div>
            <label class="block text-sm tracking-wide mb-1 text-white/80">
              Username
            </label>
            <input
              type="text"
              name="login"
              value="{{ old('login') }}"
              required
              autofocus
              class="w-full bg-transparent border-0 border-b border-white/70 focus:border-white focus:ring-0 text-sm py-2 placeholder-white/60"
              placeholder="Usuario o correo">
          </div>

          <div>
            <label class="block text-sm tracking-wide mb-1 text-white/80">
              Password
            </label>
            <input
              type="password"
              name="password"
              required
              class="w-full bg-transparent border-0 border-b border-white/70 focus:border-white focus:ring-0 text-sm py-2 placeholder-white/60"
              placeholder="Ingresa tu contraseña">
          </div>

          <div class="flex items-center justify-between pt-4">
            <div class="flex gap-4">
              <button
                type="submit"
                class="btn-glow px-6 py-2 text-sm rounded-full border border-white/80 hover:bg:white/10 transition">
                Sing In
              </button>

              <a
                href="#"
                class="btn-glow px-6 py-2 text-sm rounded-full border border-white/80 hover:bg:white/10 transition inline-flex items-center justify-center">
                Sing Up
              </a>
            </div>
          </div>

          <div class="pt-2">
            <a href="#" class="text-xs text-white/80 hover:text-white underline-offset-4">
              Forgot password?
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Toast éxito --}}
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

  {{-- Toast error login --}}
  @if ($errors->has('login'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: @json($errors->first('login')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#1f2937',
                color: '#f9fafb',
            });
        });
    </script>
  @endif

</body>
</html>
