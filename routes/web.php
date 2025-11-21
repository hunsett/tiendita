<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudVacacionesController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\PuestoController;


// Estos los usarás cuando vayas creando los módulos
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\VacacionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Puedes mandar al login si prefieres:
    // return redirect()->route('login');
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1') // 5 intentos por minuto
        ->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren sesión)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard (accesible para cualquiera con sesión)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Rutas solo ADMIN
    |----------------------------------------------------------------------
    */
    Route::middleware('role:ADMIN')->group(function () {
        // Cuando tengas este módulo:
        Route::resource('usuarios', UsuarioController::class);
        Route::resource('departamentos', DepartamentoController::class)->except(['show']);
        Route::resource('puestos', PuestoController::class)->except(['show']);
        // Otros módulos exclusivos de ADMIN...
    });

    /*
    |----------------------------------------------------------------------
    | Rutas solo RH
    |----------------------------------------------------------------------
    */
    Route::middleware('role:RH')->group(function () {
        // Cuando tengas estos módulos:
        Route::resource('empleados', EmpleadoController::class);
        // Activar / desactivar empleado
        Route::patch('/empleados/{empleado}/toggle-estado', [EmpleadoController::class, 'toggleEstado'])
            ->name('empleados.toggle-estado');

        Route::resource('vacaciones', VacacionController::class)->only(['index', 'update']);
        // Otros para RH...
    });

    /*
    |----------------------------------------------------------------------
    | Rutas solo JEFE
    |----------------------------------------------------------------------
    */
    Route::middleware('role:JEFE')->group(function () {
        // Ejemplos de aprobación de vacaciones
        Route::get('/vacaciones/pendientes', [VacacionController::class, 'pendientes'])
            ->name('vacaciones.pendientes');

        Route::post('/vacaciones/{vacacion}/aprobar', [VacacionController::class, 'aprobar'])
            ->name('vacaciones.aprobar');
    });

    /*
    |----------------------------------------------------------------------
    | Rutas solo EMPLEADO
    |----------------------------------------------------------------------
    */
    Route::middleware('role:EMPLEADO')->group(function () {

        // (Cuando exista) listado de sus propias vacaciones
        Route::get('/mis-vacaciones', [VacacionController::class, 'misVacaciones'])
            ->name('vacaciones.mias');

        // 🚪 Tu formulario actual de nueva solicitud
        Route::get('/vacaciones/solicitudes/crear', [SolicitudVacacionesController::class, 'create'])
            ->name('vacaciones.solicitudes.create');

        // 💾 Guardar solicitud de vacaciones (lo que ya tienes)
        Route::post('/vacaciones/solicitudes', [SolicitudVacacionesController::class, 'store'])
            ->name('vacaciones.solicitudes.store');
    });

    /*
    |----------------------------------------------------------------------
    | Logout (para cualquier rol)
    |----------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
