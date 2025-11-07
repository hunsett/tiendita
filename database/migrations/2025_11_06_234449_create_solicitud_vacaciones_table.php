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
        Schema::create('solicitudes_vacaciones', function (Blueprint $table) {
            $table->smallIncrements('id_solicitud');
            $table->unsignedSmallInteger('id_empleado');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('dias_solicitados', 5, 2);
            $table->enum('tipo', ['VACACIONES','ENFERMEDAD','PERMISO'])->default('VACACIONES');
            $table->enum('estado', ['BORRADOR','PENDIENTE','APROBADA','RECHAZADA','CANCELADA'])->default('BORRADOR');
            $table->text('motivo')->nullable();
            $table->timestamp('enviada_en')->nullable();
            $table->timestamp('decidida_en')->nullable();
            $table->timestamps();

            $table->foreign('id_empleado')
                  ->references('id_empleado')->on('empleados')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->index(['id_empleado','estado','fecha_inicio']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('solicitudes_vacaciones');
    }
};
