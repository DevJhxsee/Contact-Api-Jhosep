<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contacts\StoreContactRequest;
use App\Http\Requests\Contacts\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ContactController extends Controller
{
    /**
     * Lista los contactos del usuario autenticado.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $contacts = $request->user()->contacts()->latest('id')->paginate(15);

        return ContactResource::collection($contacts);
    }

    /**
     * Crea un contacto para el usuario autenticado.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = $request->user()->contacts()->create($request->validated());

        return ContactResource::make($contact)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Perfil de un contacto.
     */
    public function show(Contact $contact): ContactResource
    {
        Gate::authorize('view', $contact);

        return ContactResource::make($contact);
    }

    /**
     * Actualiza un contacto.
     */
    public function update(UpdateContactRequest $request, Contact $contact): ContactResource
    {
        $contact->update($request->validated());

        return ContactResource::make($contact);
    }

    /**
     * Elimina un contacto (soft delete).
     */
    public function destroy(Contact $contact): Response
    {
        Gate::authorize('delete', $contact);

        $contact->delete();

        return response()->noContent();
    }
}
