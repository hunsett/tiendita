<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PuestoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('puestos')->insert([
            [
                'nombre'      => 'Gerente general',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Jefe de Recursos Humanos',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Cajero',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Vendedor',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nombre'      => 'Encargado de almacén',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
