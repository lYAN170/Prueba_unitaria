<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Comprobante;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with('comprobante', 'proveedor')
            ->where('estado', 1)
            ->latest()
            ->get();

        return view('compra.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();
        $comprobantes = Comprobante::all();
        $productos = Producto::where('estado', 1)->get();

        return view('compra.create', compact('proveedores', 'comprobantes', 'productos'));
    }

    public function store(StoreCompraRequest $request)
    {
        try {
            DB::beginTransaction();

            $fecha = Carbon::parse($request->fecha);
            $compra = Compra::create(array_merge($request->validated(), ['fecha_hora' => $fecha]));

            $arrayProducto_id = $request->get('producto_id', []);
            $arrayCantidad = $request->get('cantidad', []);
            $arrayPrecioCompra = $request->get('precio_compra', []);
            $arrayPrecioVenta = $request->get('precio_venta', []);

            if (empty($arrayProducto_id)) {
                throw new Exception('Debe agregar al menos un producto.');
            }

            foreach ($arrayProducto_id as $index => $producto_id) {
                $compra->productos()->attach([
                    $producto_id => [
                        'cantidad' => $arrayCantidad[$index],
                        'precio_compra' => $arrayPrecioCompra[$index],
                        'precio_venta' => $arrayPrecioVenta[$index]
                    ]
                ]);

                $producto = Producto::findOrFail($producto_id);
                $producto->cantidad += intval($arrayCantidad[$index]);
                $producto->save();
            }

            DB::commit();
            return redirect()->route('admin.compras.index')->with('success', 'Compra registrada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.compras.index')->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function show(Compra $compra)
    {
        return view('compra.show', compact('compra'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        Compra::where('id', $id)->update(['estado' => 0]);

        return redirect()->route('admin.compras.index')->with('success', 'Compra eliminada.');
    }
}
