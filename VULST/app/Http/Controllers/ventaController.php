<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\Producto;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ventaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['comprobante','cliente.persona','user'])
            ->where('estado', 1)
            ->latest()
            ->get();

        return view('venta.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subquery = DB::table('compra_producto')
            ->select('producto_id', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('producto_id');

        // Obtener los productos disponibles
        $productos = Producto::join('compra_producto as cpr', function ($join) use ($subquery) {
            $join->on('cpr.producto_id', '=', 'productos.id')
                ->whereIn('cpr.created_at', function ($query) use ($subquery) {
                    $query->select('max_created_at')
                        ->fromSub($subquery, 'subquery')
                        ->whereRaw('subquery.producto_id = cpr.producto_id');
                });
        })
            ->select('productos.nombre', 'productos.id', 'cpr.precio_venta')
            ->where('productos.estado', 1)
            ->where('productos.cantidad', '>', 0)
           ->get();

       $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })->get();

        // Obtener comprobantes disponibles
        $comprobantes = Comprobante::all();

        return view('venta.create', compact('productos', 'clientes', 'comprobantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVentaRequest $request)
    {
        try {
            DB::beginTransaction();

            // Llenar la tabla venta
            $venta = Venta::create($request->validated());

            // Recuperar los arrays enviados desde el formulario
            $arrayProducto_id = $request->get('arrayidproducto');
          $arrayCantidadVenta = $request->get('arraycantidad'); // Cambié el nombre para claridad
            $arrayPrecioVenta = $request->get('arrayprecioventa');
            $arrayDescuento = $request->get('arraydescuento');

            // Realizar el llenado de la tabla intermedia venta_producto
            $siseArray = count($arrayProducto_id);
            $cont = 0;

            while ($cont < $siseArray) {
                // Sincronizar los productos con la venta
                $venta->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$cont] => [
                     //   'cantidad' => $arrayCantidadVenta[$cont],  // Esta es la cantidad de la venta
                        'precio_venta' => $arrayPrecioVenta[$cont],
                       // 'descuento' => $arrayDescuento[$cont]
                    ]
                ]);

                // Actualizar la cantidad en el inventario
                $producto = Producto::find($arrayProducto_id[$cont]);
               // $cantidadInventario = $producto->cantidad;  // Esta es la cantidad en inventario
             //   $cantidadVenta = intval($arrayCantidadVenta[$cont]);

                // Restar la cantidad vendida del inventario
                DB::table('productos')
                    ->where('id', $producto->id)
                    ->update([
                 //       'cantidad' => $cantidadInventario - $cantidadVenta  // Restar la cantidad vendida
                    ]);

                $cont++;
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            // Manejar el error adecuadamente si es necesario
        }

        // Redirigir a la lista de ventas con un mensaje de éxito
        return redirect()->route('admin.ventas.index')->with('success', 'Venta exitosa');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        return view('venta.show', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Venta::where('id', $id)
            ->update([
                'estado' => 0
            ]);

        return redirect()->route('admin.ventas.index')->with('success', 'Venta eliminada');
    }
}
