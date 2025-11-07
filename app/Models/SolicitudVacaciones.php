<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudVacaciones extends Model
{
    //
    protected $table = 'solicitudes_vacaciones';
    protected $primaryKey = 'id_solicitud';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empleado','fecha_inicio','fecha_fin','dias_solicitados','tipo',
        'estado','motivo','enviada_en','decidida_en'
    ];

    public function empleado()     { return $this->belongsTo(Empleado::class, 'id_empleado'); }
    public function aprobaciones() { return $this->hasMany(Aprobacion::class, 'id_solicitud'); }
}
