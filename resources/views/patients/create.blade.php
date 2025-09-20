@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

</head>
{{-- wala pa palang columns for room number, bed number, and emergency contacts --}}
<body>
    <div class="header">PATIENT REGISTARTION</div>
    {{-- one of the things that i did here is the form action
as well as adding the names of each input so that they know where theyll be going to be inserted in --}}
    <div class="form-container">
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" placeholder="Enter patient name" name="name">
                </div>

                <div class="form-group">
                    <label>Age</label>
                    <input type="number" placeholder="Enter age" name="age">
                </div>

                <div class="form-group">
                    <label>Sex</label>
                    <select name="sex">
                        <option>Select sex</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" placeholder="Enter complete address" name="address">
                </div>

                <div class="form-group">
                    <label>Birth Place</label>
                    <input type="text" placeholder="Enter birth place" name="birthplace">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Religion</label>
                    <input type="text" placeholder="Enter religion" name="religion">
                </div>

                <div class="form-group">
                    <label>Ethnicity</label>
                    <input type="text" placeholder="Enter ethnicity" name="ethnicity">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Chief of Compliants</label>
                    <textarea rows="3" placeholder="Enter chief compliants" name="chief_complaints"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Admission Date</label>
                    <input type="date" name="admission_date">
                </div>
                <div class="form-group">
                    <label>Room No.</label>
                    <input type="text" placeholder="Enter room number">
                </div>
                <div class="form-group">
                    <label>Bed No.</label>
                    <input type="text" placeholder="Enter bed number">
                </div>
            </div>
            <button type="submit">Save</button>

        </form>
    </div>
    
    <div class="header">EMERGENCY CONTACT</div>
    <div class="form-container">
    <div class="form-row">
        <div class="form-group">
            <label>Name</label>
            <input type="text" placeholder="Enter name">
        </div>

        <div class="form-group">
            <label>Age</label>
            <input type="text" placeholder="Enter relationship to patient">
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" placeholder="Enter number">
        </div>

        <div class="form-group">
            <label>Date of Admission</label>
            <input type="date">
        </div>
    </div>

@endsection

            @push('styles')
                    @vite(['resources/css/registration-style.css'])
            @endpush
