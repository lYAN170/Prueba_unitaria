<?php

namespace App\Http\Controllers;

use App\Models\Personaa;
use App\Models\Documento;
use Illuminate\Http\Request;
use App\Http\Requests\PersonaaRequest;
use App\Http\Requests\UpdatePersonaaRequest;

class PersonaaController extends Controller
{
    
    public function index()
    {
        $personaas = Personaa::with('documento')->paginate(10);

        $documentos = Documento::all();

        return view('personas.index', compact('personaas', 'documentos'));
    }

    
    public function create()
    {
        $documentos = Documento::all();
        return view('personas.create', compact('documentos'));
    }

    public function store(PersonaaRequest $request)
    {
        Personaa::create($request->validated());

        return redirect()->route('admin.personas.index')->with('success', 'Persona creada correctamente.');
    }

    
    public function edit(Personaa $personaa)
    {
        $documentos = Documento::all();
        return view('personas.edit', compact('personaa', 'documentos'));
    }

    
    public function update(UpdatePersonaaRequest $request, $id)
{
    $person = Personaa::findOrFail($id);

    $person->update($request->validated());

    return redirect()->route('admin.personas.index')->with('success', 'Persona actualizada correctamente');
}

   
    public function destroy(Personaa $personaa)
    {
        // Verificamos si la persona está asociada con un cliente
        //if ($persona->clientes()->exists()) {
          //  return redirect()->route('admin.personas.index')->with('error', 'No se puede eliminar la persona porque está asociada a un cliente.');
        //}

        $personaa->delete();

        return redirect()->route('admin.personas.index')->with('success', 'Persona eliminada correctamente.');
    }
}