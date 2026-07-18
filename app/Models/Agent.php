<?php

namespace App\Models;

use App\Enums\AgentStatus;
use Database\Factories\AgentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

#[Fillable(['client_id', 'name', 'description', 'status'])]
class Agent extends Model
{
    /** @use HasFactory<AgentFactory> */
    use HasFactory;

    // ...
    protected $attributes = [
        'status' => AgentStatus::Active->value,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function executions(): HasMany
    {
        return $this->hasMany(Execution::class);
    }

    protected function casts(): array
    {
        return [
            'status' => AgentStatus::class,
        ];
    }
}
