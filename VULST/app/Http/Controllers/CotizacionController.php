<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cotizaciones = Cotizacion::with('proveedor')->get();
        return view('backend.cotizaciones.index', compact('cotizaciones'));
    }




    public function create()
    {
        $proveedores = Proveedor::all();
        return view('backend.cotizaciones.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $this->validateCotizacion($request);

    try {
        $cotizacion = Cotizacion::create($request->all());
            return redirect()->route('admin.cotizaciones.index')->with('success', 'Cotización creada con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear l a cotización: ' . $e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cotizacion = Cotizacion::find($id);

        if (!$cotizacion) {
            return response()->json(['message' => 'Cotización no encontrada'], 404);
        }

        $this->validateCotizacion($request);

        try {
            $cotizacion->update($request->all());
            return response()->json([
                'message' => 'Cotización actualizada con éxito',
                'cotizacion' => $cotizacion
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar la cotización'], 500);
        }
    }

    public function show($id)
    {
        $cotizacion = Cotizacion::with('proveedor')->find($id);
        if (!$cotizacion) {
            return response()->json(['message' => 'Cotización no encontrada'], 404);
        }
        return view('backend.cotizaciones.show', compact('cotizacion'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cotizacion = Cotizacion::find($id);

        if (!$cotizacion) {
            return response()->json(['message' => 'Cotización no encontrada'], 404);
        }

        $cotizacion->delete();

        return response()->json(['message' => 'Cotización eliminada con éxito']);
    }

    /**
     * Validate the cotizacion request.
     */
    private function validateCotizacion(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'producto' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'precio_unitario' => 'required|numeric',
            'cantidad' => 'required|integer',
            'precio_total' => 'required|numeric',
            'impuesto' => 'nullable|numeric',
            'total_con_impuesto' => 'required|numeric',
            'fecha_cotizacion' => 'required|date',
            ]);
    }
}

