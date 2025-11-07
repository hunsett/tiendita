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
        Schema::create('aprobaciones', function (Blueprint $table) {
            $table->smallIncrements('id_aprobacion');
            $table->unsignedSmallInteger('id_solicitud');
            $table->unsignedTinyInteger('nivel'); // 1 = Jefe directo, 2 = RH
            $table->unsignedSmallInteger('id_usuario_aprobador');
            $table->enum('accion', ['APRUEBA','RECHAZA']);
            $table->text('comentario')->nullable();
            $table->timestamp('accion_en')->nullable();
            $table->timestamps();

            $table->foreign('id_solicitud')
                  ->references('id_solicitud')->on('solicitudes_vacaciones')
                  ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('id_usuario_aprobador')
                  ->references('id_usuario')->on('usuarios')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->index(['id_solicitud','nivel']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('aprobaciones');
    }
};
