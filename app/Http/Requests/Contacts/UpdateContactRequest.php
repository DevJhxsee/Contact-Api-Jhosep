<?php

namespace App\Http\Requests\Contacts;

use App\Models\Contact;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends StoreContactRequest
{
    public function authorize(): bool
    {
        Gate::authorize('update', $this->route('contact'));

        return true;
    }

    public function rules(): array
    {
        $contact = $this->route('contact');

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'phone_number' => ['sometimes', 'required', 'string', 'max:20', Rule::unique(Contact::class, 'phone_number')->ignore($contact)],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique(Contact::class, 'email')->ignore($contact)],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'avatar_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
        ];
    }
}
