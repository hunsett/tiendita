<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('empleados')->insert([
            [
                'codigo'          => 1,
                'nombre'          => 'Juan',
                'apellidos'       => 'Pérez López',
                'fecha_nacimiento'=> '1995-01-15',
                'curp'            => 'PELJ950115HPLXXX01',
                'rfc'             => 'PELJ950115XXX',
                'nss'             => '123456789012345',
                'correo'          => 'juan.admin@mary.test',
                'telefono'        => '2221112233',
                'fecha_ingreso'   => '2023-01-01',
                'estado'          => 'ACTIVO',
                'id_departamento' => 1, // Dirección
                'id_puesto'       => 1, // Gerente general
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'codigo'          => 2,
                'nombre'          => 'Rosa',
                'apellidos'       => 'García Ramírez',
                'fecha_nacimiento'=> '1990-05-10',
                'curp'            => 'GARR900510MPLXXX02',
                'rfc'             => 'GARR900510XXX',
                'nss'             => '223456789012345',
                'correo'          => 'rosa.rh@mary.test',
                'telefono'        => '2221113344',
                'fecha_ingreso'   => '2023-02-01',
                'estado'          => 'ACTIVO',
                'id_departamento' => 2, // Recursos Humanos
                'id_puesto'       => 2, // Jefe de RH
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'codigo'          => 3,
                'nombre'          => 'Carlos',
                'apellidos'       => 'López Martínez',
                'fecha_nacimiento'=> '1992-08-20',
                'curp'            => 'LOMC920820HPLXXX03',
                'rfc'             => 'LOMC920820XXX',
                'nss'             => '323456789012345',
                'correo'          => 'carlos.jefe@mary.test',
                'telefono'        => '2221114455',
                'fecha_ingreso'   => '2023-03-01',
                'estado'          => 'ACTIVO',
                'id_departamento' => 3, // Caja
                'id_puesto'       => 3, // Cajero (JEFE en rol del sistema)
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'codigo'          => 4,
                'nombre'          => 'Ana',
                'apellidos'       => 'Santos Ruiz',
                'fecha_nacimiento'=> '1998-11-30',
                'curp'            => 'SARA981130MPLXXX04',
                'rfc'             => 'SARA981130XXX',
                'nss'             => '423456789012345',
                'correo'          => 'ana.empleada@mary.test',
                'telefono'        => '2221115566',
                'fecha_ingreso'   => '2024-01-10',
                'estado'          => 'ACTIVO',
                'id_departamento' => 4, // Piso de venta
                'id_puesto'       => 4, // Vendedor
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}
