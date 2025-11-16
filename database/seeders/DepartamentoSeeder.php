<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departamentos')->insert([
            [
                'nombre'      => 'Dirección',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Recursos Humanos',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Caja',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Piso de venta',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Almacén',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
