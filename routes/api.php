<?php
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::get('/users', [UserController::class, 'index'])->middleware('admin');
    Route::put('/tasks/{id}/assign', [TaskController::class, 'assignTask']);
    Route::put('tasks/{id}/assign', [TaskController::class, 'assignTask']);
    Route::put('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
