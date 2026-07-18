<?php

namespace Database\Seeders;

use App\Models\{Agent, Client, Execution, Plan, User};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Cenário demo: um cliente saudável, um em alerta (>=80%) e um bloqueado (100%).
     */
    public function run(): void
    {
        $starter = Plan::factory()->create(['name' => 'Starter', 'monthly_execution_limit' => 100]);
        $pro     = Plan::factory()->create(['name' => 'Pro', 'monthly_execution_limit' => 1000]);
        Plan::factory()->create(['name' => 'Enterprise', 'monthly_execution_limit' => 10000]);

        // Acme (Pro, ~35% do limite) — saudável, com histórico no mês passado
        $acme = Client::factory()->for($pro)->create(['name' => 'Acme Corp']);
        User::factory()->for($acme)->create(['name' => 'Ana', 'email' => 'ana@acme.test']);
        $support = Agent::factory()->for($acme)->create(['name' => 'Bot de Suporte']);
        $sales   = Agent::factory()->for($acme)->create(['name' => 'Bot de Vendas']);
        Execution::factory(240)->for($support)->create();
        Execution::factory(10)->for($support)->failed()->create();
        Execution::factory(100)->for($sales)->create();
        Execution::factory(150)->for($support)->lastMonth()->create();

        // Globex (Starter, 85%) — em alerta
        $globex = Client::factory()->for($starter)->create(['name' => 'Globex']);
        User::factory()->for($globex)->create(['name' => 'Bruno', 'email' => 'bruno@globex.test']);
        $triage = Agent::factory()->for($globex)->create(['name' => 'Bot de Triagem']);
        Execution::factory(85)->for($triage)->create();

        // Initech (Starter, 100% + tentativas bloqueadas) — estourado
        $initech = Client::factory()->for($starter)->create(['name' => 'Initech']);
        User::factory()->for($initech)->create(['name' => 'Carla', 'email' => 'carla@initech.test']);
        $faq      = Agent::factory()->for($initech)->create(['name' => 'Bot de FAQ']);
        $inactive = Agent::factory()->for($initech)->inactive()->create(['name' => 'Bot Desativado']);
        Execution::factory(100)->for($faq)->create();
        Execution::factory(7)->for($faq)->blocked()->create();
    }
}
