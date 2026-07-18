<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientUsageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $used  = $this->executionsThisMonth();
        $limit = $this->plan->monthly_execution_limit;

        return [
            'used'          => $used,
            'limit'         => $limit,
            'usage_percent' => $limit > 0 ? (int) round($used * 100 / $limit) : 100,
            'is_blocked'    => $used >= $limit,
        ];
    }
}
