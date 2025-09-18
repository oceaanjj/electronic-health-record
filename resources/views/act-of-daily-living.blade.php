@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')
                <div class="container">
                    <div class="header">
                        <label for="patient">PATIENT NAME :</label>
                        <select id="patient" name="patient">
                            <option value="">-- Select Patient --</option>
                            <option value="Althea Pascua">Jovilyn Esquerra</option>
                        </select>
                    </div>

                    <div class="section-bar">
                        <label for="day">DAY NO :</label>
                        <select id="day" name="day">
                            <option value="">-- Select number --</option>
                            <option value="1">1</option>
                        </select>

                        <label for="date">DATE :</label>
                        <input type="date" id="date" name="date">
                    </div>


                    <table>
                                <tr>
                                    <th class="title">CATEGORY</th>
                                    <th class="title">ASSESSMENT</th>
                                    <th class="title">ALERTS</th>
                                </tr>

                                <tr>
                                    <th class="title">MOBILITY</th>
                                    <td><input type="text" placeholder="mobility"></td>
                                    <td><input type="text" placeholder="alerts"></td>
                                </tr>
                                
                                <tr>
                                    <th class="title">HYGIENE</th>
                                    <td><input type="text" placeholder="hygiene"></td>
                                    <td><input type="text" placeholder="alerts"></td>
                                </tr>

                                <tr>
                                    <th class="title">TOILETING</th>
                                    <td><input type="text" placeholder="toileting"></td>
                                    <td><input type="text" placeholder="alerts"></td>
                                </tr>

                                <tr>
                                    <th class="title">FEEDING</th>
                                    <td><input type="text" placeholder="feeding"></td>
                                    <td><input type="text" placeholder="alerts"></td>
                                </tr>

                                <tr>
                                    <th class="title">HYDRATION</th>
                                    <td><input type="text" placeholder="hydration"></td>
                                    <td><input type="text" placeholder="alerts"></td>
                                </tr>

                                <tr>
                                    <th class="title">SLEEP PATTERN</th>
                                    <td><input type="text" placeholder="sleep pattern"></td>
                                    <td><input type="text" placeholder="alerts"></td>
                                </tr>

                                <tr>
                                    <th class="title">PAIN LEVEL</th>
                                    <td><input type="text" placeholder="pain level"></td>
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
    @vite(['resources/css/vital-signs-style.css'])
@endpush
          
          
