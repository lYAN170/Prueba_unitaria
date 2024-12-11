<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionProducto extends Model
{
    use HasFactory;

    protected $table = 'ubicacion_productos'; 

    protected $fillable = [
        'producto_id',
        'almacen_id',
        'cantidad',
        'pasillo',
        'estanteria',
    ];


    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }


    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }
}
