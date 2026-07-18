<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateAgentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAgentRequest;
use App\Http\Resources\Api\V1\{AgentResource, ClientUsageResource};
use App\Models\Agent;
use Illuminate\Http\{JsonResponse, Request, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class AgentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $client = $request->user()->client->loadMissing('plan');

        $agents = $client->agents()
            ->withCount(['executions as executions_this_month_count' => fn ($query) => $query->countedInCurrentMonth()])
            ->latest()
            ->get();

        return AgentResource::collection($agents)->additional([
            'meta' => ['usage' => ClientUsageResource::make($client)],
        ]);
    }

    public function store(StoreAgentRequest $request, CreateAgentAction $action): JsonResponse
    {
        Gate::authorize('create', Agent::class);

        $agent = $action->handle($request->user()->client, $request->validated());

        return AgentResource::make($agent)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, Agent $agent): AgentResource
    {
        Gate::authorize('view', $agent);

        $agent->loadCount(['executions as executions_this_month_count' => fn ($query) => $query->countedInCurrentMonth()]);

        return AgentResource::make($agent)->additional([
            'meta' => ['usage' => ClientUsageResource::make($request->user()->client->loadMissing('plan'))],
        ]);
    }
}
