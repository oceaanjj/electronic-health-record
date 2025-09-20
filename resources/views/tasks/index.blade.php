@extends('layouts.app')

@section('title', 'Task Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Task Management</h1>
            <div class="flex gap-3">
                <a href="{{ route('tasks.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Dashboard
                </a>
                <a href="{{ route('tasks.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Create New Task
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Title</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Category</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Priority</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Assigned To</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Patient</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Due Date</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">
                                <strong>{{ $task->title }}</strong>
                                @if($task->description)
                                    <br><small class="text-gray-600">{{ Str::limit($task->description, 50) }}</small>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($task->category === 'clinical') bg-blue-100 text-blue-800
                                    @elseif($task->category === 'administrative') bg-gray-100 text-gray-800
                                    @elseif($task->category === 'technical') bg-purple-100 text-purple-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($task->category) }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($task->priority === 'critical') bg-red-100 text-red-800
                                    @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($task->status === 'completed') bg-green-100 text-green-800
                                    @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $task->assignedUser ? $task->assignedUser->username : 'Unassigned' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $task->patient ? $task->patient->name : 'General' }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($task->due_date)
                                    <span class="@if($task->due_date < now() && !in_array($task->status, ['completed', 'cancelled'])) text-red-600 font-semibold @endif">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No due date</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-green-600 hover:text-green-800">Edit</a>
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this task?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                No tasks found. <a href="{{ route('tasks.create') }}" class="text-blue-600 hover:underline">Create your first task</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->hasPages())
            <div class="mt-6">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection