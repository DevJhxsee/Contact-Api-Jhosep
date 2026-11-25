<?php

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Schema;

it('crea la tabla users con los campos del enunciado', function () {
    expect(Schema::hasColumns('users', [
        'id', 'first_name', 'last_name', 'email', 'password', 'phone_number', 'created_at', 'updated_at',
    ]))->toBeTrue();
});

it('crea la tabla contacts con los campos del enunciado', function () {
    expect(Schema::hasColumns('contacts', [
        'id', 'user_id', 'first_name', 'last_name', 'phone_number', 'email',
        'address', 'avatar_url', 'created_at', 'updated_at', 'deleted_at',
    ]))->toBeTrue();
});

it('permite que nombre, apellido y dirección se repitan', function () {
    $user = User::factory()->create();

    Contact::factory(2)->for($user)->create([
        'first_name' => 'Ana',
        'last_name' => 'Pérez',
        'address' => 'Calle 10 # 5-20',
    ]);

    expect($user->contacts()->where('first_name', 'Ana')->count())->toBe(2);
});

it('relaciona cada contacto con su usuario', function () {
    $user = User::factory()->create();
    Contact::factory(3)->for($user)->create();
    Contact::factory(2)->create();

    expect($user->contacts)->toHaveCount(3)
        ->and($user->contacts->first()->user->is($user))->toBeTrue();
});

it('hace soft delete de los contactos', function () {
    $contact = Contact::factory()->create();

    $contact->delete();

    expect(Contact::find($contact->id))->toBeNull()
        ->and(Contact::withTrashed()->find($contact->id))->not->toBeNull()
        ->and($contact->fresh()->deleted_at)->not->toBeNull();
});

it('borra los contactos cuando se elimina su usuario', function () {
    $user = User::factory()->create();
    Contact::factory(2)->for($user)->create();

    $user->delete();

    expect(Contact::withTrashed()->count())->toBe(0);
});

it('no permite repetir el campo único en usuarios', function (string $field) {
    $user = User::factory()->create();

    User::factory()->create([$field => $user->{$field}]);
})->with(['email', 'phone_number'])->throws(UniqueConstraintViolationException::class);

it('no permite repetir el campo único en la agenda de un mismo usuario', function (string $field) {
    $contact = Contact::factory()->create();

    Contact::factory()->for($contact->user)->create([$field => $contact->{$field}]);
})->with(['email', 'phone_number'])->throws(UniqueConstraintViolationException::class);

it('permite el mismo correo y teléfono en agendas de usuarios distintos', function () {
    $contact = Contact::factory()->create();

    Contact::factory()->create([
        'email' => $contact->email,
        'phone_number' => $contact->phone_number,
    ]);

    expect(Contact::where('email', $contact->email)->count())->toBe(2);
});

it('siembra el usuario demo y los contactos de ejemplo', function () {
    $this->seed(DatabaseSeeder::class);

    $demo = User::firstWhere('email', 'demo@contactapi.test');

    expect($demo)->not->toBeNull()
        ->and($demo->contacts)->toHaveCount(10)
        ->and(User::firstWhere('email', 'prueba@test.com')->contacts)->toHaveCount(5)
        ->and(User::count())->toBe(4)
        ->and(Contact::count())->toBe(25);
});
