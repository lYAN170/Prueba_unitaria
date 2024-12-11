<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonaaRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Si no se necesitan permisos adicionales para acceder, retorna true.
    }

    public function rules()
    {
        $rules = [
            'nombres' => 'required|string|max:80',
            'apellido_paterno' => 'required|string|max:80',
            'apellido_materno' => 'nullable|string|max:80',
            'sexo' => 'required|string|max:10',
            'contacto' => 'nullable|string|max:80',
            'direccion' => 'required|string|max:80',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:80|unique:personaas,email,' . $this->route('personaa'),  // Excluir el correo del modelo actual si se está actualizando
            'tipo_persona' => 'required|string|max:20',
            'estado' => 'required|boolean',
            'documento_id' => 'required|exists:documentos,id', // Validar que el documento_id existe en la tabla 'documentos'
            'documento_entidad' => 'nullable|string|max:80',
        ];

        // Validación para el campo 'numero_documento' solo en POST y PUT
        if ($this->isMethod('post')) {
            $rules['numero_documento'] = 'required|max:20|unique:personaas,numero_documento'; // Validación para crear
        } elseif ($this->isMethod('put')) {
            $rules['numero_documento'] = 'required|max:20|unique:personaas,numero_documento,' . $this->route('personaa'); // Validación para actualizar
        }

        return $rules;
    }
}
