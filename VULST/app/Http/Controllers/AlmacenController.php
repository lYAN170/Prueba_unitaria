<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $almacenes = Almacen::paginate(10); // Mostrar los almacenes con paginación

        return view('almacenes.index', compact('almacenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('almacenes.create'); // Retorna la vista para crear el almacén
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación personalizada con el Validator
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'nullable|integer|in:0,1', // Validación para el campo 'estado' (0 o 1)
        ]);

        // Validación fallida
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Hay errores en el formulario.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Crear el nuevo almacén
            Almacen::create([
                'nombre' => $request->nombre,
                'ubicacion' => $request->ubicacion,
                'estado' => $request->estado ?? 1, // Si 'estado' no se pasa, por defecto será 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Almacén creado con éxito.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el almacén.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Almacen $almacen)
    {
        return view('almacenes.show', compact('almacen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Almacen $almacen)
    {
        return view('almacenes.edit', compact('almacen')); // Retorna la vista para editar el almacén
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Almacen $almacen)
    {
        // Validación personalizada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'estado' => 'nullable|integer|in:0,1', // Validación para el campo 'estado' (0 o 1)
        ]);

        // Validación fallida
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Hay errores en el formulario.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Actualizar el almacén
            $almacen->update([
                'nombre' => $request->nombre,
                'ubicacion' => $request->ubicacion,
                'estado' => $request->estado ?? 1, // Si no se pasa 'estado', por defecto será 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Almacén actualizado con éxito.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el almacén.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Almacen $almacen)
    {
        try {
            // Eliminar el almacén
            $almacen->delete();

            return response()->json([
                'success' => true,
                'message' => 'Almacén eliminado con éxito.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el almacén.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
