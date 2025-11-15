<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiaFestivo extends Model
{
    use HasFactory;

    protected $table = 'dias_festivos';
    protected $primaryKey = 'id_festivo';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'fecha',
        'nombre',
        'es_nacional',
    ];

    protected $casts = [
        'fecha' => 'date',
        'es_nacional' => 'boolean',
    ];
}
