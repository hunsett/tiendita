<?php

use App\Http\Controllers\AprobacionesJefeController;
use App\Http\Controllers\AprobacionesRhController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudVacacionesController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\PuestoController;


// Estos los usarás cuando vayas creando los módulos
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\SaldosVacacionesController;
use App\Http\Controllers\SolicitudVacacionesEmpleadoController;
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

    //Para el cambio de contraseña por el propio usuario (cualquier rol autenticado)
    Route::get('/mi-cuenta/password', [UsuarioController::class, 'editPasswordSelf'])
        ->name('mi-cuenta.password.edit');

    Route::patch('/mi-cuenta/password', [UsuarioController::class, 'updatePasswordSelf'])
        ->name('mi-cuenta.password.update');

    /*
    |----------------------------------------------------------------------
    | Rutas solo ADMIN
    |----------------------------------------------------------------------
    */
    Route::middleware('role:ADMIN')->group(function () {
        // Cuando tengas este módulo:
        Route::resource('usuarios', UsuarioController::class);
        Route::patch('usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])
            ->name('usuarios.toggle-estado');

        Route::get('usuarios/{usuario}/reset-password', [UsuarioController::class, 'editPassword'])
            ->name('usuarios.password.edit');

        Route::patch('usuarios/{usuario}/reset-password', [UsuarioController::class, 'updatePassword'])
            ->name('usuarios.password.update');

        Route::resource('departamentos', DepartamentoController::class)->except(['show']);
        Route::resource('puestos', PuestoController::class)->except(['show']);
        // Otros módulos exclusivos de ADMIN...
    });

    /*
    |----------------------------------------------------------------------
    | Rutas solo RH
    |----------------------------------------------------------------------
    */
    Route::middleware(['web', 'auth', 'role:RH'])
        ->prefix('rh')
        ->name('rh.')
        ->group(function () {

            Route::resource('empleados', EmpleadoController::class);
            // Activar / desactivar empleado
            Route::patch('/empleados/{empleado}/toggle-estado', [EmpleadoController::class, 'toggleEstado'])
                ->name('empleados.toggle-estado');
            Route::get('/aprobaciones', [AprobacionesRhController::class, 'index'])
                ->name('aprobaciones.index');

            Route::get('/aprobaciones/{id}', [AprobacionesRhController::class, 'show'])
                ->name('aprobaciones.show');

            Route::post('/aprobaciones/{id}/decidir', [AprobacionesRhController::class, 'decidir'])
                ->name('aprobaciones.decidir');

            Route::get('/saldos', [SaldosVacacionesController::class, 'index'])
                ->name('saldos.index');

            Route::get('/saldos/empleado/{id_empleado}', [SaldosVacacionesController::class, 'show'])
                ->name('saldos.show');

            Route::post('/saldos/generar', [SaldosVacacionesController::class, 'generarPeriodos'])
                ->name('saldos.generar');
        });

    /*
    |----------------------------------------------------------------------
    | Rutas solo JEFE
    |----------------------------------------------------------------------
    */
    Route::middleware(['web', 'auth', 'role:JEFE'])
        ->prefix('jefe')
        ->name('jefe.')
        ->group(function () {

            // Vacaciones (si las quieres conservar)
            Route::get('/vacaciones/pendientes', [VacacionController::class, 'pendientes'])
                ->name('vacaciones.pendientes');

            Route::post('/vacaciones/{vacacion}/aprobar', [VacacionController::class, 'aprobar'])
                ->name('vacaciones.aprobar');

            // Aprobaciones de vacaciones (bandeja del jefe)
            Route::get('/aprobaciones', [AprobacionesJefeController::class, 'index'])
                ->name('aprobaciones.index');

            Route::get('/aprobaciones/{id}', [AprobacionesJefeController::class, 'show'])
                ->name('aprobaciones.show');

            Route::post('/aprobaciones/{id}/decidir', [AprobacionesJefeController::class, 'decidir'])
                ->name('aprobaciones.decidir');
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

        Route::get('/mis-solicitudes', [SolicitudVacacionesEmpleadoController::class, 'index'])
            ->name('index');

        Route::get('/mis-solicitudes/nueva', [SolicitudVacacionesEmpleadoController::class, 'create'])
            ->name('create');

        Route::post('/mis-solicitudes', [SolicitudVacacionesEmpleadoController::class, 'store'])
            ->name('store');

        Route::get('/mis-solicitudes/{id}', [SolicitudVacacionesEmpleadoController::class, 'show'])
            ->name('show');

        Route::post('/mis-solicitudes/{id}/enviar', [SolicitudVacacionesEmpleadoController::class, 'enviar'])
            ->name('enviar');

        Route::post('/mis-solicitudes/{id}/cancelar', [SolicitudVacacionesEmpleadoController::class, 'cancelar'])
            ->name('cancelar');
    });

    /*
    |----------------------------------------------------------------------
    | Logout (para cualquier rol)
    |----------------------------------------------------------------------
    */
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
