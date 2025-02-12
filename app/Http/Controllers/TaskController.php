<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate; // Import Laravel Gate

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure user is authenticated
    }

    public function index()
    {
        $user = auth()->user();
        
        $tasks = $user->hasRole('admin') ? Task::all() : Task::where('assigned_to', $user->id)->get();
        
        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'userRole' => $user->roles->pluck('name')->first() 
        ]);
    }
    
    

    public function store(Request $request)
{
    // Only allow users with 'super-admin' role to create tasks
    if (!auth()->user()->hasRole('super-admin')) {
        abort(403, 'Unauthorized'); 
    }

    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'status' => 'required|string|in:Pending,Completed',
        'due_date' => 'nullable|date',
        'priority' => 'required|string|in:Low,Medium,High',
        'reminder' => 'nullable|date',
        'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        'assigned_to' => 'required|exists:users,id'
    ]);

    Task::create($validatedData);

    return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
}


    public function show(Task $task)
    {
        // Ensure the user has permission to view this task
        if (auth()->user()->cannot('view-task', $task)) {
            abort(403, 'Unauthorized');
        }

        return Inertia::render('Tasks/Show', [
            'task' => $task
        ]);
    }

    public function update(Request $request, Task $task)
    {
        if (auth()->user()->cannot('edit-task', $task)) {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:Pending,Completed',
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:Low,Medium,High',
            'reminder' => 'nullable|date',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $task->update($validatedData);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        if (auth()->user()->cannot('delete-task', $task)) {
            abort(403, 'Unauthorized');
        }

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
