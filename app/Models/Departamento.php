<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    //
    protected $table = 'departamentos';
    protected $primaryKey = 'id_departamento';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['nombre'];

    public function empleados(){ return $this->hasMany(Empleado::class, 'id_departamento'); }
}
