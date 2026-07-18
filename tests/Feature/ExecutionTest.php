<?php

use App\Events\ExecutionLimitReached;
use App\Models\{Agent, Client, Execution, Plan, User};
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

function clientWithLimit(int $limit): Client
{
    return Client::factory()
        ->for(Plan::factory()->create(['monthly_execution_limit' => $limit]))
        ->create();
}

it('registra uma execução quando há limite disponível', function () {
    $client = clientWithLimit(5);
    $agent  = Agent::factory()->for($client)->create();

    Sanctum::actingAs(User::factory()->for($client)->create());

    $this->postJson(route('api.v1.agents.executions.store', $agent), [
        'duration_ms' => 1200,
        'metadata'    => ['channel' => 'web'],
    ])
        ->assertCreated()
        ->assertJsonPath('data.status', 'success');

    $this->assertDatabaseCount('executions', 1);
});

it('permite exatamente a última execução do limite', function () {
    $client = clientWithLimit(5);
    $agent  = Agent::factory()->for($client)->create();
    Execution::factory(4)->for($agent)->create();

    Sanctum::actingAs(User::factory()->for($client)->create());

    $this->postJson(route('api.v1.agents.executions.store', $agent))->assertCreated();
});

it('bloqueia execução quando o limite do plano foi atingido', function () {
    Event::fake([ExecutionLimitReached::class]);

    $client = clientWithLimit(5);
    $agent  = Agent::factory()->for($client)->create();
    Execution::factory(5)->for($agent)->create();

    Sanctum::actingAs(User::factory()->for($client)->create());

    $this->postJson(route('api.v1.agents.executions.store', $agent))
        ->assertTooManyRequests()
        ->assertJsonStructure(['message', 'errors' => ['limit']]);

    Event::assertDispatched(ExecutionLimitReached::class);
});

it('registra a tentativa bloqueada com status blocked', function () {
    $client = clientWithLimit(2);
    $agent  = Agent::factory()->for($client)->create();
    Execution::factory(2)->for($agent)->create();

    Sanctum::actingAs(User::factory()->for($client)->create());

    $this->postJson(route('api.v1.agents.executions.store', $agent))->assertTooManyRequests();

    $this->assertDatabaseHas('executions', [
        'agent_id' => $agent->id,
        'status'   => 'blocked',
    ]);
});

it('não conta execuções do mês anterior no limite', function () {
    $client = clientWithLimit(3);
    $agent  = Agent::factory()->for($client)->create();
    Execution::factory(3)->for($agent)->lastMonth()->create();

    Sanctum::actingAs(User::factory()->for($client)->create());

    $this->postJson(route('api.v1.agents.executions.store', $agent))->assertCreated();
});

it('não conta tentativas bloqueadas no consumo', function () {
    $client = clientWithLimit(3);
    $agent  = Agent::factory()->for($client)->create();
    Execution::factory(2)->for($agent)->create();
    Execution::factory(5)->for($agent)->blocked()->create();

    Sanctum::actingAs(User::factory()->for($client)->create());

    $this->postJson(route('api.v1.agents.executions.store', $agent))->assertCreated();
});

it('não permite registrar execução em agente de outro cliente', function () {
    $agent = Agent::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->postJson(route('api.v1.agents.executions.store', $agent))->assertForbidden();
});

it('rejeita execução em agente inativo', function () {
    $user  = User::factory()->create();
    $agent = Agent::factory()->for($user->client)->inactive()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.agents.executions.store', $agent))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('agent');
});

it('valida o payload da execução', function (array $payload, string $field) {
    $user  = User::factory()->create();
    $agent = Agent::factory()->for($user->client)->create();

    Sanctum::actingAs($user);

    $this->postJson(route('api.v1.agents.executions.store', $agent), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'status blocked proibido' => [['status' => 'blocked'], 'status'],
    'status inválido'         => [['status' => 'weird'], 'status'],
    'duração negativa'        => [['duration_ms' => -1], 'duration_ms'],
]);

it('lista o histórico de execuções paginado', function () {
    $user  = User::factory()->create();
    $agent = Agent::factory()->for($user->client)->create();
    Execution::factory(20)->for($agent)->create();

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.agents.executions.index', $agent))
        ->assertSuccessful()
        ->assertJsonCount(15, 'data')
        ->assertJsonPath('meta.total', 20)
        ->assertJsonPath('meta.per_page', 15);
});

it('ordena o histórico da execução mais recente para a mais antiga', function () {
    $user  = User::factory()->create();
    $agent = Agent::factory()->for($user->client)->create();

    $older = Execution::factory()->for($agent)->create(['created_at' => now()->subDay()]);
    $newer = Execution::factory()->for($agent)->create(['created_at' => now()]);

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.agents.executions.index', $agent))
        ->assertJsonPath('data.0.id', $newer->id)
        ->assertJsonPath('data.1.id', $older->id);
});

it('filtra o histórico por status', function () {
    $user  = User::factory()->create();
    $agent = Agent::factory()->for($user->client)->create();
    Execution::factory(3)->for($agent)->create();
    Execution::factory(2)->for($agent)->blocked()->create();

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.agents.executions.index', ['agent' => $agent, 'status' => 'blocked']))
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.status', 'blocked');
});

it('não permite ver histórico de agente de outro cliente', function () {
    $agent = Agent::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('api.v1.agents.executions.index', $agent))->assertForbidden();
});
