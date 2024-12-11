<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_hora',
        'impuesto',
        'numero_comprobante',
        'total',
        'comprobante_id',
        'proveedor_id' // Corregido de 'proveedore_id' a 'proveedor_id'
    ];

    // Relación con el modelo Proveedor
    public function proveedor(){
        return $this->belongsTo(Proveedor::class, 'proveedor_id'); // Asegúrate de que el nombre de la clave foránea esté correctamente definido
    }

    // Relación con el modelo Comprobante
    public function comprobante(){
        return $this->belongsTo(Comprobante::class);
    }

    // Relación muchos a muchos con el modelo Producto
    public function productos(){
        return $this->belongsToMany(Producto::class)
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'precio_venta');
    }
}
