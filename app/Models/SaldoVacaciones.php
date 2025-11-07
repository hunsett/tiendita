<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoVacaciones extends Model
{
    //
    protected $table = 'saldos_vacaciones';
    protected $primaryKey = 'id_saldo';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empleado','periodo_inicio','periodo_fin',
        'dias_acumulados','dias_usados','dias_disponibles'
    ];

    public function empleado(){ return $this->belongsTo(Empleado::class, 'id_empleado'); }
}
