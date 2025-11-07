<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaFestivo extends Model
{
    //
    protected $table = 'dias_festivos';
    protected $primaryKey = 'id_festivo';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['fecha','nombre','es_nacional'];
}
