@extends('layouts.admin')

@section('content')
    <h1 class="users-title">Users</h1>

    <div class="users-table-container">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Change Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-badge {{ strtolower($user->role) }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('users.role.update', $user->id) }}">
                                @csrf
                                @method('PATCH')
                                <select name="role">
                                    <option value="Doctor" {{ $user->role == 'Doctor' ? 'selected' : '' }}>Doctor</option>
                                    <option value="Nurse" {{ $user->role == 'Nurse' ? 'selected' : '' }}>Nurse</option>
                                </select>
                                <button type="submit" class="update-btn">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="actions">
        <a href="{{ route('register') }}" class="btn">Register New User</a>
        <a href="{{ route('home') }}" class="btn back">Back to Dashboard</a>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/admin-users.css'])
@endpush
