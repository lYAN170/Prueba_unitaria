<?php

namespace App\Http\Controllers;

use App\Models\Categoria; 
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function create()
    {
        return view('categorias.create'); 
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categorias,name', 
        ]);

        Categoria::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorias = Categoria::paginate(10);
        return view('categorias.index', compact('categorias')); 
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categoria = Categoria::findOrFail($id);
        return response()->json($categoria); 
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categorias.edit', compact('categoria')); 
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categorias,name,' . $id, 
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    public function updateEstado(Request $request, $id)
{
    $categoria = Categoria::findOrFail($id);
    $categoria->estado = $request->estado;  
    $categoria->save();

    return response()->json(['success' => true]);
}


    /**
     * 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
