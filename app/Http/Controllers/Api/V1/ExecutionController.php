<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\RegisterExecutionAction;
use App\Enums\ExecutionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreExecutionRequest;
use App\Http\Resources\Api\V1\ExecutionResource;
use App\Models\Agent;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ExecutionController extends Controller
{
    public function index(Request $request, Agent $agent): AnonymousResourceCollection
    {
        Gate::authorize('view', $agent);

        $executions = $agent->executions()
            ->when(
                $request->enum('status', ExecutionStatus::class),
                fn ($query, ExecutionStatus $status) => $query->where('status', $status),
            )
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 50));

        return ExecutionResource::collection($executions);
    }

    public function store(StoreExecutionRequest $request, Agent $agent, RegisterExecutionAction $action): JsonResponse
    {
        Gate::authorize('registerExecution', $agent);

        $execution = $action->handle($agent, $request->validated());

        return ExecutionResource::make($execution)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
