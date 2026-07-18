<?php

namespace App\Models;

use App\Enums\ExecutionStatus;
use Database\Factories\ExecutionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected function casts(): array
    {
        return [
            'status'      => ExecutionStatus::class,
            'duration_ms' => 'integer',
            'metadata'    => 'array',
        ];
    }
}
