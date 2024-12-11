<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Persona;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::with('persona.documento');

        if ($request->has('tipo_persona') && in_array($request->tipo_persona, ['natural', 'juridica'])) {
            $clientes->whereHas('persona', function ($query) use ($request) {
                $query->where('tipo_persona', $request->tipo_persona);
            });
        }

        if ($request->has('documento_id') && $request->documento_id != '') {
            $clientes->whereHas('persona.documento', function ($query) use ($request) {
                $query->where('id', $request->documento_id);
            });
        }

        $clientes = $clientes->get();

        $documentos = Documento::all();

        return view('cliente.index', compact('clientes', 'documentos'));
    }

    public function create()
    {
        $documentos = Documento::all();
        return view('cliente.create', compact('documentos'));
    }

    public function store(StorePersonaRequest $request)
    {
        try {
            DB::beginTransaction();
            $persona = Persona::create($request->validated());
            $persona->cliente()->create([
                'persona_id' => $persona->id
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente registrado');
    }

    public function show(string $id)
    {
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load('persona.documento');
        $documentos = Documento::all();
        return view('cliente.edit', compact('cliente', 'documentos'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        try {
            DB::beginTransaction();

            Persona::where('id', $cliente->persona->id)
                ->update($request->validated());

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente editado');
    }

    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $persona = Persona::find($id);
            if ($persona) {
                $persona->cliente()->delete();
                $persona->delete();

                DB::commit();

                return redirect()->route('admin.clientes.index')->with('success', 'Cliente eliminado');
            } else {
                return redirect()->route('admin.clientes.index')->with('error', 'Cliente no encontrado');
            }

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.clientes.index')->with('error', 'Error al eliminar el cliente');
        }
    }
}
