@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Task Details</h1>
            <div class="flex gap-3">
                <a href="{{ route('tasks.edit', $task) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Edit Task
                </a>
                <a href="{{ route('tasks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Back to Tasks
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Main Task Information -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ $task->title }}</h2>
                    @if($task->description)
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h3 class="font-medium text-gray-700 mb-2">Description</h3>
                            <p class="text-gray-600 whitespace-pre-wrap">{{ $task->description }}</p>
                        </div>
                    @endif
                </div>

                @if($task->notes)
                    <div class="bg-yellow-50 p-4 rounded-md">
                        <h3 class="font-medium text-gray-700 mb-2">Notes</h3>
                        <p class="text-gray-600 whitespace-pre-wrap">{{ $task->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Task Metadata -->
            <div class="space-y-6">
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-4">Task Information</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ str_replace('_', ' ', ucfirst($task->status)) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Priority:</span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($task->priority === 'critical') bg-red-100 text-red-800
                                @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Category:</span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($task->category === 'clinical') bg-blue-100 text-blue-800
                                @elseif($task->category === 'administrative') bg-gray-100 text-gray-800
                                @elseif($task->category === 'technical') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($task->category) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Assigned To:</span>
                            <span class="font-medium">{{ $task->assignedUser ? $task->assignedUser->username : 'Unassigned' }}</span>
                        </div>

                        @if($task->patient)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Patient:</span>
                                <span class="font-medium">{{ $task->patient->name }}</span>
                            </div>
                        @endif

                        @if($task->due_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Due Date:</span>
                                <span class="font-medium @if($task->due_date < now() && !in_array($task->status, ['completed', 'cancelled'])) text-red-600 @endif">
                                    {{ $task->due_date->format('M d, Y') }}
                                    @if($task->due_date < now() && !in_array($task->status, ['completed', 'cancelled']))
                                        <span class="text-red-600 text-sm">(Overdue)</span>
                                    @endif
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium">{{ $task->created_at->format('M d, Y g:i A') }}</span>
                        </div>

                        @if($task->completed_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Completed:</span>
                                <span class="font-medium text-green-600">{{ $task->completed_at->format('M d, Y g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-blue-50 p-4 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-2">
                        @if($task->status !== 'completed')
                            <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $task->title }}">
                                <input type="hidden" name="description" value="{{ $task->description }}">
                                <input type="hidden" name="priority" value="{{ $task->priority }}">
                                <input type="hidden" name="category" value="{{ $task->category }}">
                                <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                                <input type="hidden" name="patient_id" value="{{ $task->patient_id }}">
                                <input type="hidden" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}">
                                <input type="hidden" name="notes" value="{{ $task->notes }}">
                                <input type="hidden" name="status" value="completed">
                                
                                <button type="submit" 
                                        class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                                        onclick="return confirm('Mark this task as completed?')">
                                    Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if($task->status === 'pending')
                            <form action="{{ route('tasks.update', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $task->title }}">
                                <input type="hidden" name="description" value="{{ $task->description }}">
                                <input type="hidden" name="priority" value="{{ $task->priority }}">
                                <input type="hidden" name="category" value="{{ $task->category }}">
                                <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                                <input type="hidden" name="patient_id" value="{{ $task->patient_id }}">
                                <input type="hidden" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}">
                                <input type="hidden" name="notes" value="{{ $task->notes }}">
                                <input type="hidden" name="status" value="in_progress">
                                
                                <button type="submit" 
                                        class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Start Working
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection