
<<<<<<< HEAD
<head>
<<<<<<< HEAD
  <meta charset="UTF-8">
  <title>Patient Activities of daily living</title>
    @vite(['resources/css/#.css'])
=======
    <meta charset="UTF-8">
    <title>Patient Ivs and Lines</title>
    @vite(['./resources/css/ivs-and-lines.css'])
>>>>>>> cb933bfbd68b5b8f8556ba958f5f0b37559020cd
</head>
=======
@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

>>>>>>> a215d4e4a71cc5a74ec66d012de3f03ea7c80e63

    <div class="container">
        <div class="header">
            <label for="patient">PATIENT NAME :</label>
            <select id="patient" name="patient">
                <option value="">-- Select Patient --</option>
                <option value="Althea Pascua">Jovilyn Esquerra</option>
            </select>
        </div>


        <table>
            <tr>
                <th class="title">IV FLUID</th>
                <th class="title">RATE</th>
                <th class="title">SITE</th>
                <th class="title">STATUS</th>
            </tr>

            <tr>
                <td><input type="text" placeholder="iv fluid"></td>
                <td><input type="text" placeholder="rate"></td>
                <td><input type="text" placeholder="site"></td>
                <td><input type="text" placeholder="status"></td>
            </tr>



        </table>
    </div>

    <div class="buttons">
        <button class="btn" type="submit">Submit</button>
    </div>


@endsection

            @push('styles')
                    @vite(['resources/css/ivs-and-lines.css'])
            @endpush


