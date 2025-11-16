<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaldoVacacionesSeeder extends Seeder
{
    public function run(): void
    {
        $periodoInicio = '2025-01-01';
        $periodoFin    = '2025-12-31';

        $empleados = [1, 2, 3, 4];

        foreach ($empleados as $idEmpleado) {
            DB::table('saldos_vacaciones')->insert([
                'id_empleado'      => $idEmpleado,
                'periodo_inicio'   => $periodoInicio,
                'periodo_fin'      => $periodoFin,
                'dias_acumulados'  => 12.00,
                'dias_usados'      => 0.00,
                'dias_disponibles' => 12.00,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
}
