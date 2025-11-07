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
        Schema::create('empleados', function (Blueprint $table) {
            $table->smallIncrements('id_empleado');                    // 1..65535
            $table->unsignedSmallInteger('codigo')->unique()->nullable(); // opcional
            $table->string('nombre');
            $table->string('apellidos');
            $table->date('fecha_nacimiento')->nullable();
            $table->string('curp', 18)->unique();
            $table->string('rfc', 13)->unique();
            $table->string('nss', 15)->unique();
            $table->string('correo')->unique();
            $table->string('telefono')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->enum('estado', ['ACTIVO','INACTIVO'])->default('ACTIVO');

            $table->unsignedSmallInteger('id_departamento')->nullable();
            $table->unsignedSmallInteger('id_puesto')->nullable();

            $table->timestamps();

            $table->foreign('id_departamento')
                  ->references('id_departamento')->on('departamentos')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->foreign('id_puesto')
                  ->references('id_puesto')->on('puestos')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->index(['codigo']); // búsquedas rápidas por código
        });
    }
    public function down(): void {
        Schema::dropIfExists('empleados');
    }
};
