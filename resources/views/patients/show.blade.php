{{-- show specific patient --}}

<!DOCTYPE html>
<html>

<head>
    <title>Patient Details</title>
</head>

<body>
    <h1>{{ $patient->name }}</h1>
    <p>Age: {{ $patient->age }}</p>
    <p>Sex: {{ $patient->sex }}</p>
    <p>Address: {{ $patient->address }}</p>
    <p>Birthplace: {{ $patient->birthplace }}</p>
    <p>Religion: {{ $patient->religion }}</p>
    <p>Ethnicity: {{ $patient->ethnicity }}</p>
    <p>Chief Complaints: {{ $patient->chief_complaints }}</p>
    <p>Admission Date: {{ $patient->admission_date }}</p>

    <a href="{{ route('patients.index') }}">Back</a>
</body>

</html>