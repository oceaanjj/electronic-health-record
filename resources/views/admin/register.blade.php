@extends('layouts.admin')


@section('content')

    <h1 class="register">Register New User</h1>

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.attempt') }}">
        @csrf
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <div>
            <label>Role:</label>
            <select name="role" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit">Register User</button>
    </form>

    <a class="back" href="{{ route('admin-home') }}">Back to Dashboard</a>
@endsection
@push('styles')
    @vite(['resources/css/admin-register.css'])
@endpush