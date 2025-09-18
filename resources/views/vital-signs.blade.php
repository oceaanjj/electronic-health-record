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
                                <th class="title">TIME</th>
                                <th class="title">TEMPERATURE</th>
                                <th class="title">HR (bpm)</th>
                                <th class="title">RR (bpm)</th>
                                <th class="title">BP (mmHg)</th>
                                <th class="title">SpO2 (%)</th>
                                <th class="title">Alerts</th>
                            </tr>

                            <tr>
                                <th class="time">6:00 AM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>



                            <tr>
                                <th class="time">8:00 AM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>



                            <tr>
                                <th class="time">12:00 PM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>



                            <tr>
                                <th class="time">2:00 PM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>



                            <tr>
                                <th class="time">6:00 PM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>



                            <tr>
                                <th class="time">8:00 PM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>



                            <tr>
                                <th class="time">12:00 AM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>



                            <tr>
                                <th class="time">2:00 AM</th>
                                <td><input type="text" name="temperature" placeholder="temperature"></td>
                                <td><input type="text" name="hr" placeholder="HR"></td>
                                <td><input type="text" name="rr" placeholder="RR"></td>
                                <td><input type="text" name="bp" placeholder="BP"></td>
                                <td><input type="text" name="spo2" placeholder="SpO2"></td>
                                <td><input type="text" name="alerts" placeholder="alerts"></td>
                            </tr>
                    
                        
                </table>
            </div>

            <div class="buttons">
                <button  class="btn"type="submit">Submit</button>
                <a href="#" class="btn">CDSS</a>
            </div>
@endsection


            @push('styles')
                @vite(['resources/css/vital-signs-style.css'])
            @endpush