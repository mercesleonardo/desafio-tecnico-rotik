<?php

namespace App\Models;

use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'monthly_execution_limit'])]
class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory;
    // ...
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
