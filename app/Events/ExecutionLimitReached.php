<?php

namespace App\Events;

use App\Models\Agent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExecutionLimitReached
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Agent $agent,
        public int $used,
        public int $limit,
    ) {
    }
}
