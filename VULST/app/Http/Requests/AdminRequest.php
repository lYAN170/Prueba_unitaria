<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $name
 * @property mixed $username
 * @property mixed $email
 * @property mixed $password
 * @property mixed $roles
 * @property mixed $personaa_id
 * @property mixed $avatar
 */
class AdminRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $adminId = $this->route('admin')?->id;

        return [
            'name' => 'required|max:50',
            'email' => 'required|max:100|email|unique:admins,email,' . $adminId,
            'username' => 'required|max:100|unique:admins,username,' . $adminId,
            'password' => $adminId ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
            'is_superuser' => 'sometimes|boolean',
            'personaa_id' => 'nullable|exists:personaas,id', // Relación con la tabla personaas
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validación del avatar
        ];
    }
}
