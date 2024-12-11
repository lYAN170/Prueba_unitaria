<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonaaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Si no se requieren permisos adicionales, retorna true.
    }

    public function rules()
    {
        $rules = [
            'nombres' => 'required|string|max:80',
            'apellido_paterno' => 'required|string|max:80',
            'apellido_materno' => 'nullable|string|max:80',
            'sexo' => 'required|in:M,F',  // Solo puede ser 'M' o 'F'
            'contacto' => 'nullable|string|max:80',
            'direccion' => 'required|string|max:80',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:80|unique:personaas,email,' . $this->route('personaa'),  // Corregido el nombre de la tabla 'personaas'
            'tipo_persona' => 'required|string|max:20',
            'estado' => 'required|boolean',
            'documento_id' => 'required|exists:documentos,id', // Validar que el 'documento_id' exista en la tabla 'documentos'
            'documento_entidad' => 'nullable|string|max:80',
        ];

        // Validación para el campo 'numero_documento' en función del método HTTP
        if ($this->isMethod('post')) {
            $rules['numero_documento'] = 'required|max:20|unique:personaas,numero_documento';  // Corregido el nombre de la tabla 'personaas'
        } elseif ($this->isMethod('put')) {
            $rules['numero_documento'] = 'required|max:20|unique:personaas,numero_documento,' . $this->route('personaa');  // Corregido el nombre de la tabla 'personaas'
        }

        return $rules;
    }
}
