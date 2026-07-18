<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'monthly_execution_limit'])]
class Plan extends Model
{
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
