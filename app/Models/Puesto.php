<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    //
    protected $table = 'puestos';
    protected $primaryKey = 'id_puesto';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['nombre'];

    public function empleados(){ return $this->hasMany(Empleado::class, 'id_puesto'); }
}
