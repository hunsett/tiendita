<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    //
    protected $table = 'aprobaciones';
    protected $primaryKey = 'id_aprobacion';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_solicitud','nivel','id_usuario_aprobador','accion','comentario','accion_en'
    ];

    public function solicitud(){ return $this->belongsTo(SolicitudVacaciones::class, 'id_solicitud'); }
    public function aprobador(){ return $this->belongsTo(Usuario::class, 'id_usuario_aprobador'); }
}
