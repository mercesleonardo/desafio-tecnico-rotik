<?php

use App\Models\{Agent, Client, Execution, Plan, User};
use Laravel\Sanctum\Sanctum;

it('lista apenas os agentes do cliente autenticado com consumo do mês', function () {
    $plan   = Plan::factory()->create(['monthly_execution_limit' => 100]);
    $client = Client::factory()->for($plan)->create();
    $user   = User::factory()->for($client)->create();
    $agent  = Agent::factory()->for($client)->create();

    Execution::factory(3)->for($agent)->create();
    Execution::factory(2)->for($agent)->lastMonth()->create();
    Execution::factory()->for($agent)->blocked()->create();
    Agent::factory()->create(); // agente de outro cliente

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.agents.index'))
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $agent->id)
        ->assertJsonPath('data.0.executions_this_month', 3)
        ->assertJsonPath('meta.usage.used', 3)
        ->assertJsonPath('meta.usage.limit', 100)
        ->assertJsonPath('meta.usage.is_blocked', false);
});

it('cadastra um novo agente para o cliente autenticado', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.agents.store'), [
        'name'        => 'Bot de Cobrança',
        'description' => 'Automatiza follow-up de faturas',
    ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Bot de Cobrança')
        ->assertJsonPath('data.status', 'active');

    $this->assertDatabaseHas('agents', [
        'name'      => 'Bot de Cobrança',
        'client_id' => $user->client_id,
    ]);
});

it('valida o cadastro de agente', function (array $payload, string $field) {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.v1.agents.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'sem nome'         => [[], 'name'],
    'nome muito longo' => [['name' => str_repeat('a', 256)], 'name'],
]);

it('exibe um agente do próprio cliente', function () {
    $user  = User::factory()->create();
    $agent = Agent::factory()->for($user->client)->create();

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.agents.show', $agent))
        ->assertSuccessful()
        ->assertJsonPath('data.id', $agent->id);
});

it('não permite ver agente de outro cliente', function () {
    $agent = Agent::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.v1.agents.show', $agent))->assertForbidden();
});

it('exige autenticação nos endpoints de agentes', function () {
    $this->getJson(route('api.v1.agents.index'))->assertUnauthorized();
});

it('retorna 401 em JSON mesmo sem o header Accept', function () {
    $this->get(route('api.v1.agents.index'))->assertUnauthorized();
});
