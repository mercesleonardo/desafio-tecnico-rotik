<?php

namespace App\Models;

use App\Enums\ExecutionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['agent_id', 'status', 'duration_ms', 'metadata'])]
class Execution extends Model
{
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
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
