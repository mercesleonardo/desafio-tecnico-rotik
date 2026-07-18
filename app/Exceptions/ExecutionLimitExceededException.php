<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\{JsonResponse, Response};

class ExecutionLimitExceededException extends Exception
{
    public function __construct(
        public readonly int $used,
        public readonly int $limit,
    ) {
        parent::__construct('Limite mensal de execuções do plano atingido.');
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors'  => [
                'limit' => ["Consumo atual: {$this->used} de {$this->limit} execuções no mês."],
            ],
        ], Response::HTTP_TOO_MANY_REQUESTS);
    }
}
