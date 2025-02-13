<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index()
{
    $user = auth()->user();

    $tasks = $user->hasRole('admin')
        ? Task::with('assignedUsers')->get() 
        : Task::whereHas('assignedUsers', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('assignedUsers')->get();

    $users = User::all();

    return Inertia::render('Tasks/Index', [
        'tasks' => $tasks,
        'users' => $users,
        'userRole' => $user->roles->pluck('name')->first()
    ]);
}

public function store(Request $request)
{
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
        'assigned_to' => 'nullable|array',
        'assigned_to.*' => 'exists:users,id'
    ]);

    if ($request->hasFile('attachment')) {
        $validatedData['attachment'] = $request->file('attachment')->store('attachments', 'public');
    }

    $task = Task::create($validatedData);

    if (isset($validatedData['assigned_to'])) {
        $task->assignedUsers()->sync($validatedData['assigned_to']);
    }

    return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
}
    public function show(Task $task)
    {
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
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        if ($request->hasFile('attachment')) {
            $validatedData['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

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

    public function assignTask(Request $request, Task $task)
    {
        \Log::info('✅ Received Task:', [
            'task_id' => $task->id,
            'assigned_to' => $request->assigned_to
        ]);
    
        // Check if task ID exists
        if (!$task->id) {
            return response()->json(['error' => 'Task ID is missing'], 400);
        }
    
        // Validate request
        $request->validate([
            'assigned_to' => 'array',
            'assigned_to.*' => 'exists:users,id',
        ]);
    
        try {
            // Make sure relationship exists in Task model
            $task->assignedUsers()->sync($request->assigned_to);
    
            return response()->json(['message' => '✅ Task assigned successfully!']);
        } catch (\Exception $e) {
            \Log::error('❌ Failed to assign task:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to assign task',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:Pending,Completed',
        ]);

        \Log::info("Updating Task ID: {$task->id} to status: {$request->status}");

        $task->status = $request->status;

        // If task is marked as Completed, store the completion timestamp
        if ($request->status === 'Completed' && !$task->completed_at) {
            $task->completed_at = now();
        } elseif ($request->status === 'Pending') {
            // Reset completion timestamp if status is changed back to Pending
            $task->completed_at = null;
        }

        $task->save();

        return response()->json([
            'message' => 'Task status updated successfully!',
            'task' => $task
        ], 200);
    }
}
