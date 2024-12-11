<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Producto::create([
            'sku' => 'PROD-001',
            'name' => 'Producto 1',
            'Descripcion' => 'Descripción del producto 1',
            'image' => 'images/productos/default.png', 
            'almacen_id' => 1, 
            'categoria_id' => 1, 
            'marca_id' => 1, 
            'fecha_creacion' => now(),
            'slug' => Str::slug('Producto 1'),
            'detalles_adicionales' => 'Detalles adicionales del producto 1',
            'precio_venta' => 100.00,
            'precio_mayor' => 90.00,
            'precio_distribuidor' => 80.00,
            'precio_compra' => 70.00,
            'stock_minimo' => 10,
            'stock_actual' => 15, 
            'status' => 1,
        ]);

        Producto::create([
            'sku' => 'PROD-002',
            'name' => 'Producto 2',
            'Descripcion' => 'Descripción del producto 2',
            'image' => 'images/productos/default.png',
            'almacen_id' => 1,
            'categoria_id' => 2,
            'marca_id' => 2,
            'fecha_creacion' => now(),
            'slug' => Str::slug('Producto 2'),
            'detalles_adicionales' => 'Detalles adicionales del producto 2',
            'precio_venta' => 150.00,
            'precio_mayor' => 140.00,
            'precio_distribuidor' => 130.00,
            'precio_compra' => 120.00,
            'stock_minimo' => 20,
            'stock_actual' => 5, 
            'status' => 1,
        ]);
    }
}


















