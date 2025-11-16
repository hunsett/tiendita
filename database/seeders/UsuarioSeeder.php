<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'id_empleado'      => 1,
                'usuario'          => 'admin',
                'correo_sistema'   => 'admin@mary.test',
                'contrasenia_hash' => Hash::make('admin123'),
                'rol'              => 'ADMIN',
                'estado'           => 'ACTIVO',
                'ultimo_acceso'    => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'id_empleado'      => 2,
                'usuario'          => 'rosa.rh',
                'correo_sistema'   => 'rosa.rh@mary.test',
                'contrasenia_hash' => Hash::make('rh123456'),
                'rol'              => 'RH',
                'estado'           => 'ACTIVO',
                'ultimo_acceso'    => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'id_empleado'      => 3,
                'usuario'          => 'carlos.jefe',
                'correo_sistema'   => 'carlos.jefe@mary.test',
                'contrasenia_hash' => Hash::make('jefe1234'),
                'rol'              => 'JEFE',
                'estado'           => 'ACTIVO',
                'ultimo_acceso'    => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'id_empleado'      => 4,
                'usuario'          => 'ana.empleada',
                'correo_sistema'   => 'ana.empleada@mary.test',
                'contrasenia_hash' => Hash::make('empleado123'),
                'rol'              => 'EMPLEADO',
                'estado'           => 'ACTIVO',
                'ultimo_acceso'    => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}
