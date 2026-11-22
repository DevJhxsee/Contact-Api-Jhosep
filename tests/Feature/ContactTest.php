<?php

use App\Models\Contact;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

function contactData(array $cambios = []): array
{
    return array_merge([
        'first_name' => 'Ana',
        'last_name' => 'Pérez',
        'phone_number' => '3001234567',
        'email' => 'ana.perez@correo.test',
        'address' => 'Calle 10 # 5-20',
        'avatar_url' => null,
    ], $cambios);
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->otro = User::factory()->create();
});

describe('sin sesión', function () {
    it('pide token para usar los contactos', function (string $metodo, string $ruta) {
        $contact = Contact::factory()->for($this->user)->create();

        $this->json($metodo, str_replace('{id}', $contact->id, $ruta))
            ->assertUnauthorized()
            ->assertJsonPath('login_url', route('auth.login'));
    })->with([
        'listar' => ['GET', '/api/v1/contacts'],
        'crear' => ['POST', '/api/v1/contacts'],
        'ver' => ['GET', '/api/v1/contacts/{id}'],
        'actualizar' => ['PUT', '/api/v1/contacts/{id}'],
        'eliminar' => ['DELETE', '/api/v1/contacts/{id}'],
    ]);
});

describe('con sesión', function () {
    beforeEach(function () {
        Sanctum::actingAs($this->user);
    });

    it('lista solo mis contactos', function () {
        Contact::factory(3)->for($this->user)->create();
        Contact::factory(2)->for($this->otro)->create();

        $this->getJson('/api/v1/contacts')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'first_name', 'last_name', 'full_name', 'phone_number', 'email', 'address', 'avatar_url']],
                'meta' => ['current_page', 'total'],
            ]);
    });

    it('crea un contacto para el usuario autenticado', function () {
        $this->postJson('/api/v1/contacts', contactData())
            ->assertCreated()
            ->assertJsonPath('data.full_name', 'Ana Pérez')
            ->assertJsonPath('data.email', 'ana.perez@correo.test');

        expect($this->user->contacts()->count())->toBe(1);
    });

    it('permite dejar vacíos el apellido y la foto', function () {
        $this->postJson('/api/v1/contacts', contactData(['last_name' => null, 'avatar_url' => null]))
            ->assertCreated()
            ->assertJsonPath('data.last_name', null)
            ->assertJsonPath('data.full_name', 'Ana');
    });

    it('valida los campos obligatorios', function (string $campo) {
        $this->postJson('/api/v1/contacts', contactData([$campo => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$campo]);
    })->with(['first_name', 'phone_number', 'email', 'address']);

    it('valida el formato del correo y de la foto', function () {
        $this->postJson('/api/v1/contacts', contactData(['email' => 'no-es-correo', 'avatar_url' => 'no-es-url']))
            ->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'El correo no tiene un formato válido.')
            ->assertJsonPath('errors.avatar_url.0', 'La foto debe ser una URL válida.');
    });

    it('no permite repetir el correo de otro contacto', function () {
        Contact::factory()->for($this->otro)->create(['email' => 'ana.perez@correo.test']);

        $this->postJson('/api/v1/contacts', contactData())
            ->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'Ya existe un contacto con este correo.');
    });

    it('no permite repetir el teléfono de otro contacto', function () {
        Contact::factory()->for($this->user)->create(['phone_number' => '3001234567']);

        $this->postJson('/api/v1/contacts', contactData())
            ->assertUnprocessable()
            ->assertJsonPath('errors.phone_number.0', 'Ya existe un contacto con este teléfono.');
    });

    it('permite repetir el nombre y la dirección', function () {
        Contact::factory()->for($this->user)->create([
            'first_name' => 'Ana',
            'last_name' => 'Pérez',
            'address' => 'Calle 10 # 5-20',
        ]);

        $this->postJson('/api/v1/contacts', contactData())->assertCreated();

        expect($this->user->contacts()->where('first_name', 'Ana')->count())->toBe(2);
    });

    it('muestra el perfil de mi contacto', function () {
        $contact = Contact::factory()->for($this->user)->create();

        $this->getJson("/api/v1/contacts/{$contact->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.email', $contact->email);
    });

    it('no deja usar el contacto de otro usuario', function (string $metodo) {
        $ajeno = Contact::factory()->for($this->otro)->create();

        $this->json($metodo, "/api/v1/contacts/{$ajeno->id}", contactData())
            ->assertForbidden()
            ->assertJsonPath('message', 'Este contacto no te pertenece.');

        expect(Contact::find($ajeno->id))->not->toBeNull();
    })->with(['GET', 'PUT', 'DELETE']);

    it('responde 404 en JSON si el contacto no existe', function () {
        $this->getJson('/api/v1/contacts/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'El recurso solicitado no existe.');
    });

    it('actualiza mi contacto', function () {
        $contact = Contact::factory()->for($this->user)->create();

        $this->putJson("/api/v1/contacts/{$contact->id}", contactData(['first_name' => 'Ana María']))
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Ana María');

        expect($contact->fresh()->first_name)->toBe('Ana María');
    });

    it('permite actualizar solo algunos campos', function () {
        $contact = Contact::factory()->for($this->user)->create();

        $this->patchJson("/api/v1/contacts/{$contact->id}", ['address' => 'Carrera 7 # 12-30'])
            ->assertOk()
            ->assertJsonPath('data.address', 'Carrera 7 # 12-30')
            ->assertJsonPath('data.email', $contact->email);
    });

    it('permite conservar su propio correo y teléfono al actualizar', function () {
        $contact = Contact::factory()->for($this->user)->create();

        $this->putJson("/api/v1/contacts/{$contact->id}", contactData([
            'email' => $contact->email,
            'phone_number' => $contact->phone_number,
        ]))->assertOk();
    });

    it('no permite actualizar con el correo de otro contacto', function () {
        $contact = Contact::factory()->for($this->user)->create();
        $otroContacto = Contact::factory()->for($this->user)->create();

        $this->patchJson("/api/v1/contacts/{$contact->id}", ['email' => $otroContacto->email])
            ->assertUnprocessable()
            ->assertJsonPath('errors.email.0', 'Ya existe un contacto con este correo.');
    });

    it('elimina mi contacto con soft delete', function () {
        $contact = Contact::factory()->for($this->user)->create();

        $this->deleteJson("/api/v1/contacts/{$contact->id}")->assertNoContent();

        $this->assertSoftDeleted($contact);

        $this->getJson("/api/v1/contacts/{$contact->id}")->assertNotFound();
        $this->getJson('/api/v1/contacts')->assertJsonCount(0, 'data');
    });
});
