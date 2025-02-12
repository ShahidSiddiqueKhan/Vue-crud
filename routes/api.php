<?php
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks', TaskController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/users', [UserController::class, 'index'])->middleware('admin');
    Route::post('/logout', [AuthController::class, 'logout']);
});
