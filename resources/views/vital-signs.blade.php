<!DOCTYPE html>
<html
    <head>
        <title>Patient Vital Signs</title>
        @vite(['./resources/css/vital-signs-style.css'])

    </head>

    <body>

   
        <!-- Validation error messages -->
        @if ($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Error message from controller catch block -->
    @if (session('error'))
        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Success message from controller -->
    @if (session('success'))
        <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    



    <div class="form-group">

    <form action="{{ route('vital-signs') }}" method="POST">
        @csrf
        <div class="container">
            <div class="header">
                <label class="text-ehr" for="patient">PATIENT NAME :</label>
                <select id="patient" name="patient_id">
                    <option value="">-- Select Patient --</option>
                    @foreach($patients as $patient)
                        {{-- The value of this option is the patient's database ID --}}
                        <option value="{{ $patient->patient_id }}">{{ $patient->name }}</option>
                    @endforeach
                </select>
                @error('patient_id')
                    <span style="color: #e3342f; font-size: 0.8em; margin-top: 5px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="section-bar">
                <label for="day">DAY NO :</label>
                <select id="day" name="day">
                    <option value="">-- Select number --</option>
                    @for($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
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
                    <td><input type="text" name="temperature_06:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_06:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_06:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_06:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_06:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_06:00" placeholder="alerts"></td>
                </tr>
                <tr>
                    <th class="time">8:00 AM</th>
                    <td><input type="text" name="temperature_08:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_08:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_08:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_08:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_08:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_08:00" placeholder="alerts"></td>
                </tr>
                <tr>
                    <th class="time">12:00 PM</th>
                    <td><input type="text" name="temperature_12:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_12:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_12:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_12:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_12:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_12:00" placeholder="alerts"></td>
                </tr>
                <tr>
                    <th class="time">2:00 PM</th>
                    <td><input type="text" name="temperature_14:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_14:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_14:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_14:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_14:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_14:00" placeholder="alerts"></td>
                </tr>
                <tr>
                    <th class="time">6:00 PM</th>
                    <td><input type="text" name="temperature_18:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_18:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_18:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_18:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_18:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_18:00" placeholder="alerts"></td>
                </tr>
                <tr>
                    <th class="time">8:00 PM</th>
                    <td><input type="text" name="temperature_20:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_20:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_20:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_20:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_20:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_20:00" placeholder="alerts"></td>
                </tr>
                <tr>
                    <th class="time">12:00 AM</th>
                    <td><input type="text" name="temperature_00:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_00:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_00:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_00:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_00:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_00:00" placeholder="alerts"></td>
                </tr>
                <tr>
                    <th class="time">2:00 AM</th>
                    <td><input type="text" name="temperature_02:00" placeholder="temperature"></td>
                    <td><input type="text" name="hr_02:00" placeholder="HR"></td>
                    <td><input type="text" name="rr_02:00" placeholder="RR"></td>
                    <td><input type="text" name="bp_02:00" placeholder="BP"></td>
                    <td><input type="text" name="spo2_02:00" placeholder="SpO2"></td>
                    <td><input type="text" name="alerts_02:00" placeholder="alerts"></td>
                </tr>
            </table>
        </div>

        <div class="buttons">
            <button class="btn" type="submit">Submit</button>
            <a href="#" class="btn">CDSS</a>
        </div>
    </form>
</html>




