<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\InscricaoController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('eventos', EventoController::class);

    Route::post('/eventos/{evento}/inscricoes', [InscricaoController::class, 'store']);
    Route::get('/minhas-inscricoes', [InscricaoController::class, 'index']);
    Route::delete('/inscricoes/{inscricao}', [InscricaoController::class, 'destroy']);
});
