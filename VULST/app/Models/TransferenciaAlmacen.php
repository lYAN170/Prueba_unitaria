<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaAlmacen extends Model
{
    use HasFactory;

    protected $table = 'transferencias_almacenes';


    protected $fillable = [
        'producto_id',
        'almacen_origen_id',
        'almacen_destino_id',
        'cantidad',
        'fecha_transferencia',
    ];

 
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }


    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }
    
    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }
}
