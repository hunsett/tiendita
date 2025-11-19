<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles  // 'ADMIN', 'RH', etc.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Si el rol del usuario no está en la lista permitida
        if (!in_array($user->rol, $roles)) {
            // Puedes redirigir a dashboard o lanzar 403
            abort(403, 'No tiene permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
