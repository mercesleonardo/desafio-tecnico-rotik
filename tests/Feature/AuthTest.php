<?php

use App\Models\User;

it('autentica com credenciais válidas e retorna token', function () {
    $user = User::factory()->create(['password' => 'secret-123']);

    $this->postJson(route('api.v1.auth.login'), [
        'email'    => $user->email,
        'password' => 'secret-123',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'client' => ['id', 'name']]]);
});

it('rejeita credenciais inválidas com mensagem genérica', function () {
    $user = User::factory()->create();

    $this->postJson(route('api.v1.auth.login'), [
        'email'    => $user->email,
        'password' => 'senha-errada',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('valida os campos de entrada', function (array $payload, string $field) {
    $this->postJson(route('api.v1.auth.login'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'sem email'      => [['password' => 'x'], 'email'],
    'email inválido' => [['email' => 'nao-e-email', 'password' => 'x'], 'email'],
    'sem senha'      => [['email' => 'ana@acme.test'], 'password'],
]);

it('revoga o token atual no logout', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('spa')->plainTextToken;

    $this->withToken($token)
        ->postJson(route('api.v1.auth.logout'))
        ->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});
