<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExecutionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => $this->status,
            'duration_ms' => $this->duration_ms,
            'metadata'    => $this->metadata,
            'created_at'  => $this->created_at,
        ];
    }
}
