<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
            })->with(['assignedUsers' => function ($query) use ($user) {
                $query->where('users.id', $user->id); // Only fetch the logged-in user's status
            }])->get();

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
            'due_date' => 'nullable|date',
            'priority' => 'required|string|in:Low,Medium,High',
            'reminder' => 'nullable|date',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
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

    public function updateUserTaskStatus(Request $request, Task $task)
    {
        // Validate input
        $request->validate([
            'status' => 'required|string|in:Pending,Completed',
        ]);
    
        // Update the pivot table (task_user)
        DB::table('task_user')
            ->where('task_id', $task->id)
            ->where('user_id', auth()->id()) // Make sure the logged-in user is updating their own task status
            ->update([
                'status' => $request->status,
                'completed_at' => $request->status === 'Completed' ? now() : null,
                'updated_at' => now(),
            ]);
    
        return response()->json([
            'message' => 'Task status updated successfully!',
            'task' => $task
        ]);
    }
    
    
    

    public function assignTask(Request $request, Task $task)
    {
        Log::info('✅ Received Task:', [
            'task_id' => $task->id,
            'assigned_to' => $request->assigned_to
        ]);

        if (!$task->id) {
            return response()->json(['error' => 'Task ID is missing'], 400);
        }

        $request->validate([
            'assigned_to' => 'array',
            'assigned_to.*' => 'exists:users,id',
        ]);

        try {
            $task->assignedUsers()->sync($request->assigned_to);

            return response()->json(['message' => '✅ Task assigned successfully!']);
        } catch (\Exception $e) {
            Log::error('❌ Failed to assign task:', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to assign task',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTasks()
    {
        $tasks = Task::with(['assignedUsers' => function ($query) {
            $query->select('users.id', 'users.name', 'task_user.status', 'task_user.completed_at')
                  ->where('users.id', Auth::id()); // Only show the current user's status
        }])->get();

        return response()->json($tasks);
    }

    public function updateStatus(Request $request, Task $task)
    {
        $user = auth()->user();

        $request->validate([
            'status' => 'required|in:Pending,Completed',
        ]);

        Log::info("Updating Task ID: {$task->id} for User ID: {$user->id} to status: {$request->status}");

        // Update only the logged-in user's task status
        $task->assignedUsers()->updateExistingPivot($user->id, [
            'status' => $request->status,
            'completed_at' => $request->status === 'Completed' ? now() : null,
        ]);

        return response()->json([
            'message' => 'Task status updated successfully!',
            'task' => $task->load(['assignedUsers' => function ($query) use ($user) {
                $query->where('users.id', $user->id);
            }])
        ], 200);
    }
}
