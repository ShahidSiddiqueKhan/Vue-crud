<?php
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskController::class);
    
    // Update status (Only keep ONE route)
    Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus']);

    // Ensure correct HTTP method (POST or PUT)
    Route::post('/tasks/{task}/update-user-status', [TaskController::class, 'updateUserTaskStatus']);

    // Assign task to multiple users
    Route::put('/tasks/{task}/assign', [TaskController::class, 'assignTask']);

    // Get all users (only accessible to admin)
    Route::get('/users', [UserController::class, 'index'])->middleware('admin');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
