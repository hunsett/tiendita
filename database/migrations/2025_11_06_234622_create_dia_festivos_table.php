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
        Schema::create('dias_festivos', function (Blueprint $table) {
            $table->smallIncrements('id_festivo');
            $table->date('fecha')->unique();
            $table->string('nombre');
            $table->boolean('es_nacional')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('dias_festivos');
    }
};
