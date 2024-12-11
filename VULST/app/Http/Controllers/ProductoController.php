<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function create()
    {
        $categorias = Categoria::all();
        $marcas = Marca::all();
        $almacenes = Almacen::all();

        return view('productos.create', compact('categorias', 'marcas', 'almacenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:productos',
            'Descripcion' => 'required|string',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:1',
            'estado' => 'required|boolean',
            'almacen_id' => 'required|exists:almacenes,id',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'required|exists:marcas,id',
            'producto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imagen
            'detalles_adicionales' => 'nullable|string',
            'fecha_creacion' => 'required|date',
        ]);

        try {
            $producto = new Producto();
            $producto->name = $request->name;
            $producto->sku = $request->sku;
            $producto->Descripcion = $request->Descripcion;
            $producto->precio_venta = $request->precio_venta;
            $producto->precio_compra = $request->precio_compra;
            $producto->stock_minimo = $request->stock_minimo;
            $producto->estado = $request->estado;
            $producto->almacen_id = $request->almacen_id;
            $producto->categoria_id = $request->categoria_id;
            $producto->marca_id = $request->marca_id;
            $producto->detalles_adicionales = $request->detalles_adicionales;
            $producto->slug = Str::slug($request->name); // Generar slug automáticamente
            $producto->fecha_creacion = now(); 

            // Procesar la imagen si se proporciona
            if ($request->hasFile('producto')) {
                // Eliminar la imagen anterior si existe
                if ($producto->image) {
                    Storage::delete('public/' . $producto->image);
                }
                // Almacenar la nueva imagen
                $imagePath = $request->file('producto')->store('productos', 'public');
                $producto->image = $imagePath;  // Asignar la imagen
            }

            // Guardar el producto
            $producto->save();

            return response()->json([
                'success' => true,
                'message' => 'Producto creado con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto. ' . $e->getMessage()
            ]);
        }
    }

    public function storeCategoria(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categorias,name',
        ]);
        $categoria = Categoria::create([
            'name' => $request->name,
        ]);
        
        return response()->json([
            'success' => true,
            'category' => $categoria
        ]);
    }

    public function storeMarca(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:marcas,name',
        ]);
        $marca = Marca::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'success' => true,
            'marca' => $marca
        ]);
    }

    /**
     * Mostrar el formulario para editar un producto.
     */
    public function edit($id)
    {
        // Obtener el producto y las categorías, marcas y almacenes
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        $marcas = Marca::all();
        $almacenes = Almacen::all();

        return view('productos.edit', compact('producto', 'categorias', 'marcas', 'almacenes'));
    }

    /**
     * Actualizar un producto existente.
     */
    public function update(Request $request, $id)
    {
        // Obtener el producto
        $producto = Producto::findOrFail($id);

        // Validación de los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:productos,sku,' . $producto->id,
            'descripcion' => 'required|string',
            'precio_venta' => 'required|numeric',
            'precio_compra' => 'required|numeric',
            'stock_minimo' => 'required|integer',
            'estado' => 'required|integer|in:0,1',
            'slug' => 'nullable|string|unique:productos,slug,' . $producto->id,
            'detalles_adicionales' => 'nullable|string',
            'producto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'categoria_id' => 'nullable|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'almacen_id' => 'nullable|exists:almacenes,id',
        ]);

        if (!$request->slug) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }

        if ($request->hasFile('producto')) {
            if ($producto->image) {
                Storage::delete('public/' . $producto->image);
            }
         
            $imagePath = $request->file('producto')->store('productos', 'public');
            $producto->image = $imagePath;  
        }

        $producto->update([
            'name' => $request->name,
            'sku' => $request->sku,
            'descripcion' => $request->descripcion,
            'slug' => $request->slug,
            'detalles_adicionales' => $request->detalles_adicionales,
            'precio_venta' => $request->precio_venta,
            'precio_compra' => $request->precio_compra,
            'stock_minimo' => $request->stock_minimo,
            'estado' => $request->estado,
            'almacen_id' => $request->almacen_id,
            'categoria_id' => $request->categoria_id,
            'marca_id' => $request->marca_id,
            'image' => $imagePath,  
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado con éxito',
        ]);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->image) {
            Storage::delete('public/' . $producto->image);
        }

        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado con éxito',
        ]);
    }

    public function show($id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.show', compact('producto'));
    }

    public function index()
    {
        $categorias = Categoria::all();
        $productos = Producto::paginate(10); // Paginación de productos
        return view('productos.index', compact('productos', 'categorias'));
    }
}
