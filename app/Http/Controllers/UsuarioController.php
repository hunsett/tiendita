<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioStoreRequest;
use App\Http\Requests\UsuarioUpdateRequest;
use App\Http\Requests\UsuarioPasswordRequest;
use App\Models\Empleado;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rol    = $request->input('rol');
        $estado = $request->input('estado');

        $query = Usuario::with('empleado');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('usuario', 'like', "%{$search}%")
                    ->orWhere('correo_sistema', 'like', "%{$search}%")
                    ->orWhereHas('empleado', function ($qe) use ($search) {
                        $qe->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellidos', 'like', "%{$search}%")
                            ->orWhere('correo', 'like', "%{$search}%");
                    });
            });
        }

        if ($rol) {
            $query->where('rol', $rol);
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        $usuarios = $query
            ->orderBy('rol')
            ->orderBy('usuario')
            ->paginate(10)
            ->appends($request->query());

        return view('usuarios.index', compact('usuarios', 'search', 'rol', 'estado'));
    }

    public function create()
    {
        $empleadosSinUsuario = Empleado::whereDoesntHave('usuario')
            ->where('estado', 'ACTIVO')
            ->orderBy('nombre')
            ->orderBy('apellidos')
            ->get();

        return view('usuarios.create', compact('empleadosSinUsuario'));
    }

    public function store(UsuarioStoreRequest $request)
    {
        $data = $request->validated();

        $usuario = new Usuario();
        $usuario->id_empleado      = $data['id_empleado'];
        $usuario->usuario          = $data['usuario'];
        $usuario->correo_sistema   = $data['correo_sistema'];
        $usuario->rol              = $data['rol'];
        $usuario->estado           = $data['estado'];
        $usuario->contrasenia_hash = Hash::make($data['password']);
        $usuario->save();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario)
    {
        $usuario->load('empleado');

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(UsuarioUpdateRequest $request, Usuario $usuario)
    {
        $data = $request->validated();

        $usuario->usuario        = $data['usuario'];
        $usuario->correo_sistema = $data['correo_sistema'];
        $usuario->rol            = $data['rol'];
        $usuario->estado         = $data['estado'];
        $usuario->save();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario)
    {
        // Opcional: si algún día quieres borrar físicamente
        $usuario->delete();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleEstado(Usuario $usuario)
    {
        $usuario->estado = $usuario->estado === 'ACTIVO' ? 'BLOQUEADO' : 'ACTIVO';
        $usuario->save();

        return redirect()
            ->back()
            ->with('success', 'Estado del usuario actualizado correctamente.');
    }

    // ----- Reset de contraseña por ADMIN -----

    public function editPassword(Usuario $usuario)
    {
        return view('usuarios.password', [
            'usuario' => $usuario,
            'modo'    => 'admin',
        ]);
    }

    public function updatePassword(UsuarioPasswordRequest $request, Usuario $usuario)
    {
        $data = $request->validated();

        $usuario->contrasenia_hash = Hash::make($data['password']);
        $usuario->save();

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Contraseña actualizada correctamente.');
    }

    // ----- Cambio de contraseña por el propio usuario -----

    public function editPasswordSelf()
    {
        $usuario = auth()->user();

        return view('usuarios.password', [
            'usuario' => $usuario,
            'modo'    => 'self',
        ]);
    }

    public function updatePasswordSelf(UsuarioPasswordRequest $request)
    {
        $usuario = auth()->user();

        $data = $request->validated();

        $usuario->contrasenia_hash = Hash::make($data['password']);
        $usuario->save();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tu contraseña se actualizó correctamente.');
    }
}
