<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    //
use Notifiable, HasFactory;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'id_empleado', 'usuario', 'correo_sistema', 'contrasenia_hash', 'rol', 'estado', 'ultimo_acceso',
    ];

    protected $hidden = ['contrasenia_hash', 'remember_token'];
    protected $casts = ['ultimo_acceso' => 'datetime'];

    // Para que Auth use este campo como password
    public function getAuthPassword()
    {
        return $this->contrasenia_hash;
    }

    // (Opcional) indicar que el "username" por defecto será 'usuario'
    public function getAuthIdentifierName()
    {
        return 'usuario';
    }

    // Mutador opcional por si algún lugar hace: $user->contrasenia_hash = 'plana'
    public function setContraseniaHashAttribute($value)
    {
        $this->attributes['contrasenia_hash'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function empleado(){return $this->belongsTo(Empleado::class, 'id_empleado','id_empleado');}
    public function aprobaciones(){ return $this->hasMany(Aprobacion::class, 'id_usuario_aprobador'); }
}
