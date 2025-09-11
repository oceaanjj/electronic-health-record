{{-- show all patients --}}

<!DOCTYPE html>
<html>
<head>
    <title>Patients</title>
</head>
<body>
    <h1>Patients List</h1>

    <a href="{{ route('patients.create') }}">Add New Patient</a>

    @if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
    @endif

    <ul>
        @foreach($patients as $patient)
        <li>
            <a href="{{ route('patients.show', $patient->id) }}">
                {{ $patient->name }} ({{ $patient->age }} years old)
            </a>
            |
            <a href="{{ route('patients.edit', $patient->id) }}">Edit</a>
            |
            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Delete</button>
            </form>
        </li>
        @endforeach
    </ul>
</body>
</html>
