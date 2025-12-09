<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    //
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');

        // Buscar por usuario o por correo_sistema
        $user = Usuario::where('usuario', $login)
            ->orWhere('correo_sistema', $login)
            ->first();

        // Mensaje ambiguo para no revelar si existe o no
        $error = 'Credenciales inválidas.';

        if (!$user) {
            return back()->withErrors(['login' => $error])->withInput();
        }

        if ($user->estado === 'BLOQUEADO') {
            return back()->withErrors(['login' => 'Tu cuenta está bloqueada.'])->withInput();
        }

        if (!Hash::check($password, $user->contrasenia_hash)) {
            // Opcional: throttle básico (véase rutas)
            return back()->withErrors(['login' => $error])->withInput();
        }

        // 👇 Asegúrate de usar el guard web
        Auth::guard('web')->login($user, false);

        // Regenerar sesión contra fixation
        $request->session()->regenerate();

        // 👇 DEBUG AQUÍ
        //dd('DESPUÉS DE LOGIN', Auth::user(), Auth::check(), session()->all());



        // Actualizar último acceso
        $user->ultimo_acceso = now();
        $user->save();

        return redirect()->intended('/dashboard'); // ajusta tu ruta de aterrizaje
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
