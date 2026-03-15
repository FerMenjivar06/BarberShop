<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\BarberoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CitaController;

Route::prefix('auth')->group(function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function(){
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::middleware('auth:api')->group(function () {
    
    Route::apiResource('servicios', ServicioController::class);
    Route::apiResource('citas', CitaController::class);
    Route::apiResource('clientes', ClienteController::class);

    Route::middleware('role:ADMIN')->group(function () {
        Route::apiResource('barberos', BarberoController::class);
    });
});