
<<<<<<< HEAD
<head>
<<<<<<< HEAD
  <meta charset="UTF-8">
  <title>Patient Lab Values</title>
  @vite(['resources/css/#.css'])
=======
    <meta charset="UTF-8">
    <title>Patient Lab Values</title>
    @vite(['./resources/css/lab-values.css'])
    <meta charset="UTF-8">
    <title>Patient Lab Values</title>
    @vite(['./resources/css/lab-values.css'])
>>>>>>> cb933bfbd68b5b8f8556ba958f5f0b37559020cd
</head>
=======
@extends('layouts.app')
>>>>>>> a215d4e4a71cc5a74ec66d012de3f03ea7c80e63

@section('title', 'Patient Vital Signs')

@section('content')
                <div class="container">
                    <div class="header">
                        <label for="patient">PATIENT NAME :</label>
                        <select id="patient" name="patient">
                            <option value="">-- Select Patient --</option>
                            <option value="Althea Pascua">Althea Pascua</option>
                            <option value="Jovilyn Esquerra">Jovilyn Esquerra</option>
                        </select>
                    </div>

                    <table>
                        <tr>
                            <th class="title">LAB TEST</th>
                            <th class="title">RESULT</th>
                            <th class="title">PEDIATRIC NORMAL RANGE</th>
                            <th class="title">ALERTS</th>
                        </tr>

                        <tr>
                            <th class="title">WBC (×10⁹/L)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">RBC (×10¹²/L)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Hgb (g/dL)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Hct (%)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Platelets (×10⁹/L)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">MCV (fL)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">MCH (pg)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">MCHC (g/dL)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">RDW (%)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Neutrophils (%)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Lymphocytes (%)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Monocytes (%)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Eosinophils (%)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>

                        <tr>
                            <th class="title">Basophils (%)</th>
                            <td><input type="text" placeholder="result"></td>
                            <td><input type="text" placeholder="normal range"></td>
                            <td><input type="text" placeholder="alerts"></td>
                        </tr>
                    </table>
                </div>

                <div class="buttons">
                        <a href="#" class="btn">CDSS</a>
                    <button  class="btn"type="submit">Submit</button>
                </div>
@endsection

            @push('styles')
                    @vite(['resources/css/lab-values.css'])
            @endpush
