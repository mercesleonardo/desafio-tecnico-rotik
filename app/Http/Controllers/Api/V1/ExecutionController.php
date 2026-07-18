<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RegisterExecutionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreExecutionRequest;
use App\Http\Resources\Api\V1\ExecutionResource;
use App\Models\Agent;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Support\Facades\Gate;

class ExecutionController extends Controller
{
    public function store(StoreExecutionRequest $request, Agent $agent, RegisterExecutionAction $action): JsonResponse
    {
        Gate::authorize('registerExecution', $agent);

        $execution = $action->handle($agent, $request->validated());

        return ExecutionResource::make($execution)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
