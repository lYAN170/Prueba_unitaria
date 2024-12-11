<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\TransferenciaAlmacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransferenciaAlmacenController extends Controller
{

    public function create()
    {
        $almacenes = Almacen::all(); 
        $productos = Producto::all();
        return view('transferencias.create', compact('almacenes', 'productos'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'almacen_origen_id' => 'required|exists:almacenes,id',
            'almacen_destino_id' => 'required|exists:almacenes,id',
            'producto_id' => 'required|array', 
            'producto_id.*' => 'exists:productos,id', 
            'cantidad' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        $almacenOrigen = Almacen::find($request->almacen_origen_id);
        $almacenDestino = Almacen::find($request->almacen_destino_id);
        $productos = Producto::find($request->producto_id);

        DB::beginTransaction();

        try {
            foreach ($productos as $producto) {
                $stockOrigen = $almacenOrigen->productos()->where('producto_id', $producto->id)->first();
                if (!$stockOrigen || $stockOrigen->pivot->stock < $request->cantidad) {
                    DB::rollBack();
                    return response()->json(['error' => 'No hay suficiente stock en el almacén de origen para el producto ' . $producto->nombre], 400);
                }

                TransferenciaAlmacen::create([
                    'producto_id' => $producto->id,
                    'almacen_origen_id' => $request->almacen_origen_id,
                    'almacen_destino_id' => $request->almacen_destino_id,
                    'cantidad' => $request->cantidad,
                    'fecha_transferencia' => now(),
                ]);

                $almacenOrigen->productos()->updateExistingPivot($producto->id, [
                    'stock' => DB::raw('stock - ' . $request->cantidad)
                ]);

                $almacenDestino->productos()->updateExistingPivot($producto->id, [
                    'stock' => DB::raw('stock + ' . $request->cantidad)
                ]);
            }

            DB::commit();

            return response()->json(['success' => 'Transferencia realizada exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Hubo un error al procesar la transferencia. Inténtalo de nuevo.'], 500);
        }
    }


    public function index()
    {
        $transferencias = TransferenciaAlmacen::with(['producto', 'almacenOrigen', 'almacenDestino'])->paginate(10);
        $productos = Producto::all();
        $almacenes = Almacen::all();

        return view('transferencias.index', compact('transferencias', 'productos', 'almacenes'));
    }


    public function edit($id)
    {
        $transferencia = TransferenciaAlmacen::findOrFail($id);
        $almacenes = Almacen::all();
        $productos = Producto::all();
        return view('transferencias.edit', compact('transferencia', 'almacenes', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'almacen_origen_id' => 'required|exists:almacenes,id',
            'almacen_destino_id' => 'required|exists:almacenes,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $transferencia = TransferenciaAlmacen::findOrFail($id);

        $almacenOrigen = Almacen::find($request->almacen_origen_id);
        $almacenDestino = Almacen::find($request->almacen_destino_id);
        $producto = Producto::find($request->producto_id);

        $stockOrigen = $almacenOrigen->productos()->where('producto_id', $producto->id)->first();
        if (!$stockOrigen || $stockOrigen->pivot->stock < $request->cantidad) {
            return redirect()->back()->withErrors(['error' => 'No hay suficiente stock en el almacén de origen']);
        }

        DB::beginTransaction();

        try {
            $transferencia->update([
                'producto_id' => $producto->id,
                'almacen_origen_id' => $request->almacen_origen_id,
                'almacen_destino_id' => $request->almacen_destino_id,
                'cantidad' => $request->cantidad,
                'fecha_transferencia' => now(),
            ]);

            $almacenOrigen->productos()->updateExistingPivot($producto->id, [
                'stock' => DB::raw('stock - ' . $request->cantidad)
            ]);
            
            $almacenDestino->productos()->updateExistingPivot($producto->id, [
                'stock' => DB::raw('stock + ' . $request->cantidad)
            ]);

            DB::commit();

            return redirect()->route('admin.transferencias.index')->with('success', 'Transferencia actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Hubo un error al procesar la actualización.']);
        }
    }


    public function anular($id)
    {
        $transferencia = TransferenciaAlmacen::findOrFail($id);

        $producto = $transferencia->producto;
        $almacenOrigen = $transferencia->almacenOrigen;
        $almacenDestino = $transferencia->almacenDestino;

        if ($producto && $almacenOrigen && $almacenDestino) {
            DB::beginTransaction();

            try {
                $almacenOrigen->productos()->updateExistingPivot($producto->id, [
                    'stock' => DB::raw('stock + ' . $transferencia->cantidad)
                ]);

                $almacenDestino->productos()->updateExistingPivot($producto->id, [
                    'stock' => DB::raw('stock - ' . $transferencia->cantidad)
                ]);

                $transferencia->delete();

                DB::commit();

                return response()->json(['success' => 'Transferencia anulada exitosamente.']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Hubo un error al anular la transferencia.']);
            }
        }

        return response()->json(['error' => 'No se pudo revertir la transferencia.']);
    }
}
