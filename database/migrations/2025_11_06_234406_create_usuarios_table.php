<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->smallIncrements('id_usuario');
            $table->unsignedSmallInteger('id_empleado')->unique(); // 1:1 con empleado
            $table->string('usuario')->unique();
            $table->string('correo_sistema')->unique();
            $table->string('contrasenia_hash'); // guarda hash, no plano
            $table->enum('rol', ['ADMIN','RH','JEFE','EMPLEADO'])->default('EMPLEADO');
            $table->enum('estado', ['ACTIVO','BLOQUEADO'])->default('ACTIVO');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->timestamps();

            $table->foreign('id_empleado')
                  ->references('id_empleado')->on('empleados')
                  ->cascadeOnUpdate()->restrictOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('usuarios');
    }
};
