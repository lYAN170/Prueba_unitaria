<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use Illuminate\Http\Request;

class CuponController extends Controller
{
    public function index()
    {
        $cupones = Cupon::all();
        return view('cupones.index', compact('cupones'));
    }

    public function create()
    {
        return view('cupones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:cupones,nombre|max:255',
            'descuento' => 'required|integer|min:1',
            'tipo' => 'required|in:porcentaje,fijo',
            'valido_hasta' => 'required|date|after:today',
            'limite_uso' => 'nullable|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        Cupon::create([
            'nombre' => $request->nombre,
            'descuento' => $request->descuento,
            'tipo' => $request->tipo,
            'valido_hasta' => $request->valido_hasta,
            'limite_uso' => $request->limite_uso,
            'activo' => $request->activo ?? true,
        ]);

        return redirect()->route('admin.cupones.index')->with('success', 'Cupón creado con éxito');
    }

    public function edit($id)
    {
        $cupon = Cupon::findOrFail($id);
        return view('cupones.edit', compact('cupon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:255|unique:cupones,nombre,' . $id,
            'descuento' => 'required|integer|min:1',
            'tipo' => 'required|in:porcentaje,fijo',
            'valido_hasta' => 'required|date|after:today',
            'limite_uso' => 'nullable|integer|min:1',
            'activo' => 'nullable|boolean',
        ]);

        $cupon = Cupon::findOrFail($id);
        $cupon->update([
            'nombre' => $request->nombre,
            'descuento' => $request->descuento,
            'tipo' => $request->tipo,
            'valido_hasta' => $request->valido_hasta,
            'limite_uso' => $request->limite_uso,
            'activo' => $request->activo ?? true,
        ]);

        return redirect()->route('admin.cupones.index')->with('success', 'Cupón actualizado con éxito');
    }

    public function destroy($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->delete();

        return redirect()->route('admin.cupones.index')->with('success', 'Cupón eliminado con éxito');
    }
}


