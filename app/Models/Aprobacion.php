<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aprobacion extends Model
{
    use HasFactory;

    protected $table = 'aprobaciones';
    protected $primaryKey = 'id_aprobacion';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_solicitud',
        'nivel',
        'id_usuario_aprobador',
        'accion',
        'comentario',
        'accion_en',
    ];

    protected $casts = [
        'accion_en' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudVacaciones::class, 'id_solicitud', 'id_solicitud');
    }

    public function aprobador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_aprobador', 'id_usuario');
    }
}
