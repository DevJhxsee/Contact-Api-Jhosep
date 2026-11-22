<?php

namespace App\Http\Requests\Contacts;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique(Contact::class, 'phone_number')],
            'email' => ['required', 'email', 'max:255', Rule::unique(Contact::class, 'email')],
            'address' => ['required', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre admite máximo 100 caracteres.',
            'last_name.max' => 'El apellido admite máximo 100 caracteres.',
            'phone_number.required' => 'El teléfono es obligatorio.',
            'phone_number.max' => 'El teléfono admite máximo 20 caracteres.',
            'phone_number.unique' => 'Ya existe un contacto con este teléfono.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ya existe un contacto con este correo.',
            'address.required' => 'La dirección es obligatoria.',
            'address.max' => 'La dirección admite máximo 255 caracteres.',
            'avatar_url.url' => 'La foto debe ser una URL válida.',
        ];
    }
}
