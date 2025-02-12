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
        
        $tasks = $user->hasRole('admin') ? Task::with('assignedUser')->get() : Task::where('assigned_to', $user->id)->with('assignedUser')->get();
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
            'assigned_to' => 'nullable|exists:users,id' 
        ]);

   
        if ($request->hasFile('attachment')) {
            $validatedData['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        Task::create($validatedData);

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

    public function assignTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->assigned_to = $request->assigned_to;
        $task->save();
    
        return response()->json(['message' => 'Task assigned successfully!', 'task' => $task]);
    }
    
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:Pending,Completed',
        ]);
    
        // Debugging: Log the request and task
        \Log::info("Updating Task ID: {$task->id} to status: {$request->status}");
    
        $task->status = $request->status;
        $task->save();
    
        return response()->json([
            'message' => 'Task status updated successfully!',
            'task' => $task
        ], 200);
    }
}
