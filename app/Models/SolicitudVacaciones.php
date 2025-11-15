<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitudVacaciones extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_vacaciones';
    protected $primaryKey = 'id_solicitud';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empleado',
        'fecha_inicio',
        'fecha_fin',
        'dias_solicitados',
        'tipo',
        'estado',
        'motivo',
        'enviada_en',
        'decidida_en',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'enviada_en' => 'datetime',
        'decidida_en' => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    public function aprobaciones()
    {
        return $this->hasMany(Aprobacion::class, 'id_solicitud', 'id_solicitud');
    }
}
