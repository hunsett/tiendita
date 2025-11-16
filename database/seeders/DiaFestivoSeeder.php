<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaFestivoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dias_festivos')->insert([
            [
                'fecha'      => '2025-01-01',
                'nombre'     => 'Año Nuevo',
                'es_nacional'=> true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha'      => '2025-02-05',
                'nombre'     => 'Constitución Política',
                'es_nacional'=> true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha'      => '2025-03-21',
                'nombre'     => 'Natalicio de Benito Juárez',
                'es_nacional'=> true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha'      => '2025-05-01',
                'nombre'     => 'Día del Trabajo',
                'es_nacional'=> true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fecha'      => '2025-09-16',
                'nombre'     => 'Día de la Independencia',
                'es_nacional'=> true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
