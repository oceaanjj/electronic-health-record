{{-- search --}}
<!DOCTYPE html>
<html>

<head>
    <title>Search Patient</title>
</head>

<body>
    <h1>Search Patient</h1>

    <form action="{{ route('patients.search-results') }}" method="GET">
        <input type="text" name="input" placeholder="Search patient by id">
        <button type="submit">Search</button>
    </form>

    @if ($patients->isNotEmpty())
        <?php    $patient = $patients->first(); ?>
        <h2>Search Result</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Age</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $patient->id }}</td>
                    <td>{{ $patient->name }}</td>
                    <td>{{ $patient->age }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p>No patient found with that ID.</p>
    @endif
</body>

</html>