<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard'); // o al login, como prefieras
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login'); // 👈 nombre = login

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1') // 5 intentos por minuto
        ->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

          // 🚪 Nueva solicitud de vacaciones (formulario)
    Route::get('/vacaciones/solicitudes/crear', [SolicitudVacacionesController::class, 'create'])
        ->name('vacaciones.solicitudes.create');

    // 💾 Guardar solicitud de vacaciones
    Route::post('/vacaciones/solicitudes', [SolicitudVacacionesController::class, 'store'])
        ->name('vacaciones.solicitudes.store');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});