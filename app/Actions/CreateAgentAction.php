<?php

namespace App\Actions;

use App\Models\{Agent, Client};

class CreateAgentAction
{
    /**
     * @param array{name: string, description?: string|null} $data
     */
    public function handle(Client $client, array $data): Agent
    {
        return $client->agents()->create($data);
    }
}
