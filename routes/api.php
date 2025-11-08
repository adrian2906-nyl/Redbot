<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;

Route::get('/ping', fn() => response()->json(['ok' => true, 'api' => 'up']));

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat', [ChatController::class, 'responder'])->middleware('throttle:30,1');
    Route::post('/logout', [AuthController::class, 'logout']);
});
