<?php

namespace App\Listeners;

use App\Events\ExecutionLimitReached;
use Illuminate\Support\Facades\Log;

class LogExecutionLimitReached
{
    public function handle(ExecutionLimitReached $event): void
    {
        Log::warning('Execução bloqueada por limite do plano', [
            'client_id' => $event->agent->client_id,
            'agent_id'  => $event->agent->id,
            'used'      => $event->used,
            'limit'     => $event->limit,
        ]);
    }
}
