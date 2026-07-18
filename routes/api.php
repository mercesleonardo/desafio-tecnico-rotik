<?php

use App\Http\Controllers\Api\V1\{AgentController, AuthController};
use App\Http\Controllers\Api\V1\ExecutionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1')
        ->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::apiResource('agents', AgentController::class)->only(['index', 'store', 'show']);
        Route::post('agents/{agent}/executions', [ExecutionController::class, 'store'])->name('agents.executions.store');
    });
});
