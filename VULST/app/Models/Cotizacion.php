<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';
    protected $fillable = [
        'proveedor_id',
        'producto',
        'descripcion',
        'precio_unitario',
        'cantidad',
        'precio_total',
        'impuesto',
        'total_con_impuesto',
        'fecha_cotizacion',
    ];
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}

