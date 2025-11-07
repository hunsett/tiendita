<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    //
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_empleado','usuario','correo_sistema','contraseña_hash','rol','estado','ultimo_acceso'
    ];

    public function empleado()  { return $this->belongsTo(Empleado::class, 'id_empleado'); }
    public function aprobaciones(){ return $this->hasMany(Aprobacion::class, 'id_usuario_aprobador'); }
}
