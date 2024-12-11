<?php

namespace App\Http\Controllers;

use App\Models\UbicacionProducto;
use App\Models\Producto;
use App\Models\Almacen;
use Illuminate\Http\Request;

class UbicacionProductoController extends Controller
{
    
    public function index()
    {
        $ubicaciones = UbicacionProducto::with(['producto', 'almacen'])->get(); 
        return view('ubicaciones.index', compact('ubicaciones'));
    }

  
    public function create()
    {
        $productos = Producto::all();  
        $almacenes = Almacen::all();   
        return view('ubicaciones.create', compact('productos', 'almacenes'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'cantidad' => 'required|integer|min:0',
            'pasillo' => 'nullable|string|max:255',
            'estanteria' => 'nullable|string|max:255',
        ]);

        UbicacionProducto::create([
            'producto_id' => $request->producto_id,
            'almacen_id' => $request->almacen_id,
            'cantidad' => $request->cantidad,
            'pasillo' => $request->pasillo,
            'estanteria' => $request->estanteria,
        ]);

        return redirect()->route('admin.ubicaciones.index')->with('success', 'Ubicación de producto creada con éxito.');
    }


    public function edit(UbicacionProducto $ubicacion)
    {
        $productos = Producto::all();  
        $almacenes = Almacen::all();   
        return view('ubicaciones.edit', compact('ubicacion', 'productos', 'almacenes'));
    }

    public function update(Request $request, UbicacionProducto $ubicacion)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'cantidad' => 'required|integer|min:0',
            'pasillo' => 'nullable|string|max:255',
            'estanteria' => 'nullable|string|max:255',
        ]);

        $ubicacion->update([
            'producto_id' => $request->producto_id,
            'almacen_id' => $request->almacen_id,
            'cantidad' => $request->cantidad,
            'pasillo' => $request->pasillo,
            'estanteria' => $request->estanteria,
        ]);

        return redirect()->route('admin.ubicaciones.index')->with('success', 'Ubicación de producto actualizada con éxito.');
    }


    public function destroy(UbicacionProducto $ubicacion)
    {
        try {
            $ubicacion->delete();
            return redirect()->route('admin.ubicaciones.index')->with('success', 'Ubicación de producto eliminada con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ubicaciones.index')->with('error', 'No se pudo eliminar la ubicación de producto.');
        }
    }
}
