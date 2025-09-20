<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['assignedUser', 'patient'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $patients = Patient::all();
        
        return view('tasks.create', compact('users', 'patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:clinical,administrative,technical,training',
            'assigned_to' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:patients,patient_id',
            'due_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        // Set completed_at if status is completed
        if ($data['status'] === 'completed') {
            $data['completed_at'] = now();
        }

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['assignedUser', 'patient']);
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $users = User::all();
        $patients = Patient::all();
        
        return view('tasks.edit', compact('task', 'users', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'priority' => 'required|in:low,medium,high,critical',
            'category' => 'required|in:clinical,administrative,technical,training',
            'assigned_to' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:patients,patient_id',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Set completed_at if status is completed and wasn't completed before
        if ($data['status'] === 'completed' && $task->status !== 'completed') {
            $data['completed_at'] = now();
        } elseif ($data['status'] !== 'completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }

    /**
     * Get dashboard data for task overview
     */
    public function dashboard()
    {
        $stats = [
            'total' => Task::count(),
            'pending' => Task::pending()->count(),
            'in_progress' => Task::inProgress()->count(),
            'completed' => Task::completed()->count(),
            'overdue' => Task::overdue()->count(),
        ];

        $recentTasks = Task::with(['assignedUser', 'patient'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();

        return view('tasks.dashboard', compact('stats', 'recentTasks'));
    }
}
