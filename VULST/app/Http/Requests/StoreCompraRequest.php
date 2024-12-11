<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'proveedor_id' => 'required|exists:proveedores,id',
            'comprobante_id' => 'required|exists:comprobantes,id',
            'numero_comprobante' => 'required|unique:compras,numero_comprobante|max:255',
            'impuesto' => 'required|numeric',  // Cambiado a 'numeric' para validar que sea un número
            'fecha' => 'required|date',        // Cambiado a 'fecha' en lugar de 'fecha_hora' y validación como 'date'
            'total' => 'required|numeric'      // Cambiado a 'numeric' para asegurar que sea un número
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'proveedor_id' => 'Proveedor',
            'comprobante_id' => 'Comprobante',
            'numero_comprobante' => 'Número de comprobante',
            'fecha' => 'Fecha', // Asegúrate de que el nombre del campo en la vista coincida
        ];
    }
}
