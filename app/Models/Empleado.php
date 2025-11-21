<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{
    //
    use HasFactory;
    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'codigo',
        'nombre',
        'apellidos',
        'fecha_nacimiento',
        'curp',
        'rfc',
        'nss',
        'correo',
        'telefono',
        'fecha_ingreso',
        'estado',
        'id_departamento',
        'id_puesto'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso'    => 'date',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento', 'id_departamento');
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'id_puesto', 'id_puesto');
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_empleado', 'id_empleado');
    }

    public function solicitudesVacaciones()
    {
        return $this->hasMany(SolicitudVacaciones::class, 'id_empleado', 'id_empleado');
    }

    public function saldosVacaciones()
    {
        return $this->hasMany(SaldoVacaciones::class, 'id_empleado', 'id_empleado');
    }
}
