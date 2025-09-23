@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Audit Logs</h1>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Audit ID</th>
                <th>User ID</th>
                <th>Action</th>
                <th>Details</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->audit_id }}</td>
                    <td>{{ $log->id ?? 'System' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->details }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No audit logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>

</div>
 <a href="{{ route('admin-home') }}" class="btn btn-primary mt-3">Back to Dashboard</a>
@endsection
            @push('styles')
                @vite(['resources/css/audit-index.css'])
            @endpush
