@extends('layouts.app')

@section('title', 'Task Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Task Dashboard</h1>
        <div class="flex gap-3">
            <a href="{{ route('tasks.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                View All Tasks
            </a>
            <a href="{{ route('tasks.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Create New Task
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Total Tasks</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Pending</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl font-bold text-blue-500">{{ $stats['in_progress'] }}</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">In Progress</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Completed</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="text-3xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Overdue</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Progress Overview</h2>
        @if($stats['total'] > 0)
            <div class="w-full bg-gray-200 rounded-full h-6">
                <div class="bg-green-600 h-6 rounded-full flex items-center justify-center text-white text-sm font-medium" 
                     style="width: {{ ($stats['completed'] / $stats['total']) * 100 }}%">
                    {{ round(($stats['completed'] / $stats['total']) * 100, 1) }}% Complete
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                {{ $stats['completed'] }} of {{ $stats['total'] }} tasks completed
            </div>
        @else
            <div class="text-gray-500">No tasks available to show progress.</div>
        @endif
    </div>

    <!-- Recent Tasks -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Tasks</h2>
        
        @if($recentTasks->count() > 0)
            <div class="space-y-4">
                @foreach($recentTasks as $task)
                    <div class="border-l-4 pl-4 py-3 
                        @if($task->priority === 'critical') border-red-500
                        @elseif($task->priority === 'high') border-orange-500
                        @elseif($task->priority === 'medium') border-yellow-500
                        @else border-green-500 @endif">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800">{{ $task->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                
                                <div class="flex items-center gap-4 mt-2 text-xs">
                                    <span class="px-2 py-1 rounded-full 
                                        @if($task->status === 'completed') bg-green-100 text-green-800
                                        @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                    </span>
                                    
                                    <span class="px-2 py-1 rounded-full 
                                        @if($task->category === 'clinical') bg-blue-100 text-blue-800
                                        @elseif($task->category === 'administrative') bg-gray-100 text-gray-800
                                        @elseif($task->category === 'technical') bg-purple-100 text-purple-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($task->category) }}
                                    </span>
                                    
                                    @if($task->assignedUser)
                                        <span class="text-gray-500">Assigned to: {{ $task->assignedUser->username }}</span>
                                    @endif
                                    
                                    @if($task->patient)
                                        <span class="text-gray-500">Patient: {{ $task->patient->name }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-end gap-2 ml-4">
                                @if($task->due_date)
                                    <span class="text-xs 
                                        @if($task->due_date < now() && !in_array($task->status, ['completed', 'cancelled'])) 
                                            text-red-600 font-semibold 
                                        @else 
                                            text-gray-500 
                                        @endif">
                                        Due: {{ $task->due_date->format('M d') }}
                                    </span>
                                @endif
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('tasks.show', $task) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                    <a href="{{ route('tasks.edit', $task) }}" 
                                       class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('tasks.index') }}" 
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    View All Tasks →
                </a>
            </div>
        @else
            <div class="text-gray-500 text-center py-8">
                No tasks found. <a href="{{ route('tasks.create') }}" class="text-blue-600 hover:underline">Create your first task</a>
            </div>
        @endif
    </div>
</div>
@endsection