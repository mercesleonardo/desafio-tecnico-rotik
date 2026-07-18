<?php

namespace App\Policies;

use App\Models\{Agent, User};

class AgentPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Agent $agent): bool
    {
        return $user->client_id === $agent->client_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->client_id !== null;
    }

    public function registerExecution(User $user, Agent $agent): bool
    {
        return $user->client_id === $agent->client_id;
    }

}
