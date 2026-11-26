<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

function loginData(array $cambios = []): array
{
    return array_merge([
        'email' => 'ana@contactapi.test',
        'password' => 'secreta123',
    ], $cambios);
}

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'ana@contactapi.test',
        'password' => 'secreta123',
    ]);
});

it('entrega un token con credenciales correctas', function () {
    $this->postJson('/api/v1/auth/login', loginData())
        ->assertOk()
        ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'first_name', 'last_name', 'email', 'phone_number']])
        ->assertJsonPath('user.email', 'ana@contactapi.test')
        ->assertJsonMissingPath('user.password');

    expect($this->user->tokens()->count())->toBe(1);
});

it('rechaza una contraseña incorrecta con 422', function () {
    $this->postJson('/api/v1/auth/login', loginData(['password' => 'otra']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('usa el mismo mensaje si el correo no existe', function () {
    $this->postJson('/api/v1/auth/login', loginData(['email' => 'nadie@contactapi.test']))
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Las credenciales no son correctas.');
});

it('valida los campos obligatorios', function (string $campo) {
    $this->postJson('/api/v1/auth/login', loginData([$campo => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$campo]);
})->with(['email', 'password']);

it('pide token para ver el perfil', function () {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('responde 401 en JSON con el enlace al login aunque falte la cabecera Accept', function () {
    $this->get('/api/v1/auth/me')
        ->assertUnauthorized()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonPath('message', 'No autenticado. Inicia sesión para continuar.')
        ->assertJsonPath('login_url', route('auth.login'));
});

it('devuelve el perfil del usuario autenticado', function () {
    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'ana@contactapi.test')
        ->assertJsonMissingPath('data.password');
});

it('cierra sesión y revoca el token', function () {
    $token = $this->user->createToken('pruebas')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/auth/logout')
        ->assertNoContent();

    expect($this->user->tokens()->count())->toBe(0);
});

it('limita los intentos de login a 5 por minuto', function () {
    foreach (range(1, 5) as $intento) {
        $this->postJson('/api/v1/auth/login', loginData(['password' => 'otra']))
            ->assertUnprocessable();
    }

    $this->postJson('/api/v1/auth/login', loginData(['password' => 'otra']))
        ->assertTooManyRequests()
        ->assertJsonPath('message', 'Demasiados intentos de inicio de sesión. Espera un minuto.');
});
