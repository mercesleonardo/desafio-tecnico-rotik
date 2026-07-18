<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};

#[Fillable(['name', 'plan_id'])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;
    // ...
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function executions(): HasManyThrough
    {
        return $this->hasManyThrough(Execution::class, Agent::class);
    }

    public function executionsThisMonth(): int
    {
        return $this->executions()->countedInCurrentMonth()->count();
    }

    public function hasReachedExecutionLimit(): bool
    {
        return $this->executionsThisMonth() >= $this->plan->monthly_execution_limit;
    }
}
