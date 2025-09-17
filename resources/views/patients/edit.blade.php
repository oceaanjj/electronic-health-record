{{-- edit or update clients details --}}
<!DOCTYPE html>
<html>

<head>
    <title>Edit Patient</title>
</head>

<body>
    <h1>Edit Patient</h1>

    <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
        @csrf
        @method('PUT')
        Name: <input type="text" name="name" value="{{ $patient->name }}"><br>
        Age: <input type="number" name="age" value="{{ $patient->age }}"><br>
        Sex:
        <select name="sex">
            <option {{ $patient->sex == 'Male' ? 'selected' : '' }}>Male</option>
            <option {{ $patient->sex == 'Female' ? 'selected' : '' }}>Female</option>
            <option {{ $patient->sex == 'Other' ? 'selected' : '' }}>Other</option>
        </select><br>
        Address: <input type="text" name="address" value="{{ $patient->address }}"><br>
        Birthplace: <input type="text" name="birthplace" value="{{ $patient->birthplace }}"><br>
        Religion: <input type="text" name="religion" value="{{ $patient->religion }}"><br>
        Ethnicity: <input type="text" name="ethnicity" value="{{ $patient->ethnicity }}"><br>
        Chief Complaints: <textarea name="chief_complaints">{{ $patient->chief_complaints }}</textarea><br>
        Admission Date: <input type="date" name="admission_date" value="{{ $patient->admission_date }}"><br>
        <button type="submit">Update</button>
    </form>
</body>

</html>