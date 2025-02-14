<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskManagementController extends Controller
{
    public function index()
    {
        $tasks = Task::with([
            'assignedUsers' => function ($query) {
                $query->select('users.id', 'users.name');
            },
            'completedUsers' => function ($query) {
                $query->select('users.id', 'users.name', 'task_user.completed_at')
                      ->whereNotNull('task_user.completed_at');
            }
        ])->get();
    
        return Inertia::render('TaskManagement', ['tasks' => $tasks]);
    }
    
}
