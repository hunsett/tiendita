<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    //
    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'codigo','nombre','apellidos','fecha_nacimiento','curp','rfc','nss',
        'correo','telefono','fecha_ingreso','estado','id_departamento','id_puesto'
    ];

    public function usuario()      { return $this->hasOne(Usuario::class, 'id_empleado'); }
    public function departamento() { return $this->belongsTo(Departamento::class, 'id_departamento'); }
    public function puesto()       { return $this->belongsTo(Puesto::class, 'id_puesto'); }
    public function solicitudes()  { return $this->hasMany(SolicitudVacaciones::class, 'id_empleado'); }
    public function saldos()       { return $this->hasMany(SaldoVacaciones::class, 'id_empleado'); }
}
