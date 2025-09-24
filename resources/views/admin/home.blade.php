@extends('layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <a href="#" class="login" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        LOG OUT
    </a>
    <h2>Users</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Change Role</th>
        </tr>
        @foreach(\App\Models\User::all() as $user)
            <tr>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    <form method="POST" action="{{ route('users.role.update', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <select name="role">
                            <option value="Doctor" {{ $user->role == 'Doctor' ? 'selected' : '' }}>Doctor</option>
                            <option value="Nurse" {{ $user->role == 'Nurse' ? 'selected' : '' }}>Nurse</option>

                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>

        @endforeach
    </table>
    <a class="btn" href="{{ route('register') }}">Register User</a>
    <a class="btn" href="{{ route('audit.index') }}">View Audit Logs</a>
@endsection

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush