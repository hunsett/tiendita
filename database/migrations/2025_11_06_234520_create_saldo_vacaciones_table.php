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
        Schema::create('saldos_vacaciones', function (Blueprint $table) {
            $table->smallIncrements('id_saldo');
            $table->unsignedSmallInteger('id_empleado');
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
            $table->decimal('dias_acumulados', 5, 2)->default(0);
            $table->decimal('dias_usados', 5, 2)->default(0);
            $table->decimal('dias_disponibles', 5, 2)->default(0); // o cacular en app
            $table->timestamps();

            $table->foreign('id_empleado')
                  ->references('id_empleado')->on('empleados')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->unique(['id_empleado','periodo_inicio','periodo_fin']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('saldos_vacaciones');
    }
};
