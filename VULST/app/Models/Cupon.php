<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    use HasFactory;

    protected $table = 'cupones'; 


    protected $fillable = [
        'nombre', 'descuento', 'tipo', 'valido_hasta', 'limite_uso', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

}
