<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaldoVacaciones extends Model
{
    use HasFactory;

    protected $table = 'saldos_vacaciones';
    protected $primaryKey = 'id_saldo';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empleado',
        'periodo_inicio',
        'periodo_fin',
        'dias_acumulados',
        'dias_usados',
        'dias_disponibles',
    ];

    protected $casts = [
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }
}
