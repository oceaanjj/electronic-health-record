@vite(['resources/css/app.css', 'resources/js/app.js'])

@extends('layouts.admin')

@section('content')

    <!-- ✅ Success Message -->
    @if (session('success'))
        <div id="success-message" style="
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px 16px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
        ">
            {{ session('success') }}
        </div>
    @endif

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

@endsection

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@push('scripts')
<script>
    // ✅ Auto-hide success message after 4 seconds
    document.addEventListener("DOMContentLoaded", () => {
        const msg = document.getElementById("success-message");
        if (msg) {
            setTimeout(() => {
                msg.style.transition = "opacity 0.6s ease";
                msg.style.opacity = 0;
                setTimeout(() => msg.remove(), 600);
            }, 4000); // 4 seconds
        }
    });
</script>
@endpush
