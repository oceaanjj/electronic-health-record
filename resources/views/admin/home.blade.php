@vite(['resources/css/app.css', 'resources/js/app.js'])

@extends('layouts.admin')

@section('content')

    <!-- Dashboard Stats -->
    <section class="ehr-stats">
        <h2>Admin Overview</h2>
        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Users</h3>
                <p>{{ \App\Models\User::where('role', '!=', 'admin')->count() }}</p>
            </div>
            <div class="stat-box">
                <h3>Doctors</h3>
                <p>{{ \App\Models\User::where('role', 'doctor')->count() }}</p>
            </div>
            <div class="stat-box">
                <h3>Nurses</h3>
                <p>{{ \App\Models\User::where('role', 'nurse')->count() }}</p>
            </div>
        </div>
    </section>

<!-- Recent Audit Logs -->
<section class="audit-logs">
    <h2>Recent Audit Logs</h2>
    <table class="logs-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Details</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Models\AuditLog::latest()->take(5)->get() as $log)
                <tr>
                    <td>{{ $log->user->username ?? 'System' }}</td>
                    <td><span class="action-badge">{{ $log->action }}</span></td>
                    <td>{{ $log->details }}</td>
                    <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>


    </table>
@endsection

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush
