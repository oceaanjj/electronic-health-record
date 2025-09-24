@vite(['resources/css/bootstrap.css', 'resources/js/app.js'])

@extends('layouts.app')

@section('content')


    <div class="container">

        {{-- Search and Filter Form --}}
        <h1>Audit Logs</h1>
        <div class="d-flex justify-content-between mb-3">
            <form action="{{ route('audit.index') }}" method="GET" class="d-flex">
                <input type="text" name="username_search" class="form-control me-2" placeholder="Search by username..."
                    value="{{ request('username_search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <div class="d-flex align-items-center">
                <span class="me-2">Sort by Date:</span>
                <a href="{{ route('audit.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_direction' => 'asc'])) }}"
                    class="btn btn-outline-secondary btn-sm me-1">
                    Oldest First
                </a>
                <a href="{{ route('audit.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_direction' => 'desc'])) }}"
                    class="btn btn-outline-secondary btn-sm">
                    Newest First
                </a>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>

                    <th>Audit ID</th>
                    <th>User</th>
                    <th>User Role</th>
                    <!-- <th>IP Address</th> -->
                    <th>Action</th>
                    <th>Details</th>
                    <th>Created At</th>

                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->user_name ?? 'System' }}</td>
                        <td>{{ $log->user_role ?? 'N/A' }}</td>
                        <!-- <td>{{ $log->ip_address ?? 'N/A' }}</td> -->
                        <td>{{ $log->action }}</td>
                        <td>
                            @if($log->details)
                                @php $details = json_decode($log->details, true); @endphp
                                @if(is_array($details))
                                    {{ $details['details'] ?? 'No primary details provided.' }}
                                    @if(count($details) > 1)
                                        <ul>
                                            @foreach($details as $key => $value)
                                                @if($key !== 'details')
                                                    <li>
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        {{ is_array($value) ? json_encode($value) : $value }}
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                @else
                                    {{ $log->details }}
                                @endif
                            @else
                                No details provided.
                            @endif
                        </td>
                        <td>{{ $log->created_at->format('Y-m-d | H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No audit logs found.</td>
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