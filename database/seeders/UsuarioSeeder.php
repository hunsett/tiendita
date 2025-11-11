<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;              // <-- IMPORTANTE
use Illuminate\Support\Facades\Hash; // si vas a hashear aquí

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Asegúrate de tener un empleado con id_empleado = 1
        Usuario::updateOrCreate(
            ['usuario' => 'admin'],
            [
                'id_empleado'      => 1,
                'correo_sistema'   => 'admin@tiendita.test',
                'contrasenia_hash' => '123', // mutador lo hashea
                'rol'              => 'ADMIN',
                'estado'           => 'ACTIVO',
            ]
        );
    }
}
