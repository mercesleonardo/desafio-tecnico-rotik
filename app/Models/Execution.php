<?php

namespace App\Models;

use App\Enums\ExecutionStatus;
use Database\Factories\ExecutionFactory;
use Illuminate\Database\Eloquent\Attributes\{Fillable, Scope};
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['agent_id', 'status', 'duration_ms', 'metadata'])]
class Execution extends Model
{
    /** @use HasFactory<ExecutionFactory> */
    use HasFactory;
    // ...
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Execuções que contam para o consumo do mês corrente
     * (tentativas bloqueadas medem demanda reprimida, não uso servido).
     */
    #[Scope]
    protected function countedInCurrentMonth(Builder $query): Builder
    {
        return $query
            ->where($query->qualifyColumn('created_at'), '>=', now()->startOfMonth())
            ->whereNot($query->qualifyColumn('status'), ExecutionStatus::Blocked);
    }

    protected function casts(): array
    {
        return [
            'status'      => ExecutionStatus::class,
            'duration_ms' => 'integer',
            'metadata'    => 'array',
        ];
    }
}
