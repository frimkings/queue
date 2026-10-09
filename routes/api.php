<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QueueController;
use Illuminate\Support\Facades\Route;

// Authentication Endpoints
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Hospital Queue Endpoints
Route::prefix('queue')->group(function () {
    Route::get('/state', [QueueController::class, 'getInitialState']);
    Route::get('/stream', [QueueController::class, 'stream']);
    Route::post('/ticket/issue', [QueueController::class, 'issueTicket']);
    Route::post('/call-next', [QueueController::class, 'callNext']);
    Route::post('/mark-served', [QueueController::class, 'markServed']);
    Route::post('/transfer', [QueueController::class, 'transferTicket']);
    Route::get('/tracker/{ticketNumber}', [QueueController::class, 'getTicketStatus']);
});
