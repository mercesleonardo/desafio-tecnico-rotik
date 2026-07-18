<?php

namespace App\Actions;

use App\Enums\{AgentStatus, ExecutionStatus};
use App\Events\ExecutionLimitReached;
use App\Exceptions\ExecutionLimitExceededException;
use App\Models\{Agent, Client, Execution};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterExecutionAction
{
    /**
     * Registra uma execução aplicando a regra de bloqueio por limite do plano.
     *
     * @param array{status?: string, duration_ms?: int|null, metadata?: array<string, mixed>|null} $data
     *
     * @throws ExecutionLimitExceededException
     */
    public function handle(Agent $agent, array $data): Execution
    {
        if ($agent->status === AgentStatus::Inactive) {
            throw ValidationException::withMessages([
                'agent' => 'Agente inativo não pode registrar execuções.',
            ]);
        }

        [$execution, $used, $limit] = DB::transaction(function () use ($agent, $data) {
            $client = Client::query()
                ->whereKey($agent->client_id)
                ->lockForUpdate()
                ->firstOrFail();

            $used  = $client->executionsThisMonth();
            $limit = $client->plan->monthly_execution_limit;

            if ($used >= $limit) {
                $blocked = $agent->executions()->create([
                    'status'   => ExecutionStatus::Blocked,
                    'metadata' => $data['metadata'] ?? null,
                ]);

                return [$blocked, $used, $limit];
            }

            return [
                $agent->executions()->create(['status' => ExecutionStatus::Success, ...$data]),
                $used,
                $limit,
            ];
        });

        if ($execution->status === ExecutionStatus::Blocked) {
            event(new ExecutionLimitReached($agent, $used, $limit));

            throw new ExecutionLimitExceededException($used, $limit);
        }

        return $execution;
    }
}
