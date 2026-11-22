<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactPolicy
{
    public function view(User $user, Contact $contact): Response
    {
        return $this->esDueno($user, $contact);
    }

    public function update(User $user, Contact $contact): Response
    {
        return $this->esDueno($user, $contact);
    }

    public function delete(User $user, Contact $contact): Response
    {
        return $this->esDueno($user, $contact);
    }

    private function esDueno(User $user, Contact $contact): Response
    {
        // Un contacto ajeno responde 404, igual que uno inexistente:
        // así la API no confirma que existe en la agenda de otra persona.
        return $contact->user()->is($user)
            ? Response::allow()
            : Response::denyAsNotFound('El recurso solicitado no existe.');
    }
}
