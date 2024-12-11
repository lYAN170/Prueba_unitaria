<?php

namespace App\Http\Controllers;

use App\Models\Marca; 
use Illuminate\Http\Request;

class MarcaController extends Controller
{

    public function create()
    {
        return view('marcas.create'); 
    }


    /**
     * Almacenar una nueva marca
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Marca::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.marcas.index')->with('success', 'Marca creada exitosamente.');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marcas = Marca::all();
        return view('marcas.index', compact('marcas')); 
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = Marca::findOrFail($id);
        return response()->json($marca); 
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $marca = Marca::findOrFail($id);
        return view('admin.marcas.edit', compact('marca')); 
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255', 
        ]);

        $marca = Marca::findOrFail($id);
        $marca->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.marcas.index')->with('success', 'Marca actualizada exitosamente.');
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();

        return redirect()->route('admin.marcas.index')->with('success', 'Marca eliminada exitosamente.');
    }
}
