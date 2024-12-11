<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'almacenes';

    /**
     * Los atributos que se pueden asignar de manera masiva.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'ubicacion',
        'estado', 

    ];

    /**
     * Relación con los productos a través de ubicaciones.
     * Un almacén puede tener varios productos en diferentes ubicaciones.
     */
    public function ubicaciones()
    {
        return $this->hasMany(UbicacionProducto::class, 'almacen_id');
    }

    /**
     * Relación con las transferencias como almacén de origen.
     */
    public function transferenciasOrigen()
    {
        return $this->hasMany(TransferenciaAlmacen::class, 'almacen_origen_id');
    }

    /**
     * Relación con las transferencias como almacén de destino.
     */
    public function transferenciasDestino()
    {
        return $this->hasMany(TransferenciaAlmacen::class, 'almacen_destino_id');
    }



    public function productos()
    {
        return $this->belongsToMany(Producto::class)
                    ->withPivot('stock')  // Campo 'stock' en la tabla pivot
                    ->withTimestamps();
    }



}
