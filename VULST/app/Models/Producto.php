<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'sku',
        'name',
        'Descripcion',
        'image',
        'almacen_id',
        'categoria_id',
        'marca_id',
        'fecha_creacion',
        'slug',
        'detalles_adicionales',
        'precio_venta',
        'precio_compra',
        'stock_minimo',
        'stock_maximo',
        'status',
    ];

    protected $dates = [
        'fecha_creacion',
        'created_at',
        'updated_at',
    ];

    // Definir las relaciones con otros modelos
    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class)
                    ->withPivot('stock')  // Campo 'stock' en la tabla pivot
                    ->withTimestamps();
    }
    
    
    
    public function transferencias()
    {
        return $this->hasMany(TransferenciaAlmacen::class, 'producto_id');
    }



    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Establecer precio de venta con formato
    public function setPrecioVentaAttribute($value)
    {
        $this->attributes['precio_venta'] = number_format($value, 2, '.', '');
    }

    public function getPrecioVentaAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    // Establecer precio de compra con formato
    public function setPrecioCompraAttribute($value)
    {
        $this->attributes['precio_compra'] = number_format($value, 2, '.', '');
    }

    public function getPrecioCompraAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    // Método para actualizar el stock
    public function actualizarStock($cantidad)
    {
        if ($this->stock_minimo + $cantidad > $this->stock_maximo) {
            throw new \Exception('No se puede agregar más productos, stock máximo alcanzado.');
        }
        $this->stock_minimo += $cantidad;
        $this->save();
    }

    // Verificar si el stock está por debajo del mínimo
    public function estaPorDebajoStockMinimo()
    {
        return $this->stock_minimo <= 0;  // Verifica si el stock mínimo es menor o igual a cero
    }

    // Verificar si el producto está activo
    public function isActivo()
    {
        return $this->status === 1;  // Si el estado es 1, el producto está activo
    }

    // Obtener la URL de la imagen
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-product.png');
    }

    // Asignar la imagen del producto
    public function setImageAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['image'] = $value;
        } else {
            // Si hay un archivo de imagen
            $imagePath = $value->store('productos', 'public');
            $this->attributes['image'] = $imagePath;
        }
    }

    // Establecer la fecha de creación automáticamente si no se proporciona
    public static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            if (!$producto->fecha_creacion) {
                $producto->fecha_creacion = now();
            }
        });
    }

    // Relación para gestionar las categorías y marcas directamente desde el formulario (opcional)
    public function setCategoriaYMarca($categoriaInput, $marcaInput)
    {
        if ($categoriaInput) {
            $categoria = Categoria::firstOrCreate(['name' => $categoriaInput]);
            $this->categoria_id = $categoria->id;
        }

        if ($marcaInput) {
            $marca = Marca::firstOrCreate(['name' => $marcaInput]);
            $this->marca_id = $marca->id;
        }

        $this->save();
    }
}
