<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Patient Physical Exam</title>
    @vite(['resources/css/physical-exam-style.css'])
</head>

<body>

    <form action="{{ route('physical-exam.store') }}" method="POST">
        @csrf

        <div class="container">
            <div class="header">
                <label for="patient_info">PATIENT NAME :</label>

                {{-- Patient Name DROPDOWN --}}
                <select id="patient_info" name="patient_info">
                    <option value="" {{ old('patient_info') == '' ? 'selected' : '' }}>-- Select Patient --</option>

                    {{-- Combined patients' id and name inside option's value used '|' to seperate--}}
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}|{{ $patient->name }}" {{ old('patient_info') == $patient->id . '|' . $patient->name ? 'selected' : '' }}>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>

                @error('patient_info')
                    <div style="color:red; font-size:12px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <table>
            <tr>
                <th class="title">SYSTEM</th>
                <th class="title">FINDINGS</th>
                <th class="title">ALERTS</th>
            </tr>

            <tr>
                <th class="system">GENERAL APPEARANCE</th>
                <td>
                    <textarea name="general_appearance_findings"
                        placeholder="Enter General Appearance findings">{{ old('general_appearance_findings') }}</textarea>
                    @error('general_appearance_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="general_appearance_alerts" placeholder="alerts">
                </td>
            </tr>

            <tr>
                <th class="system">SKIN</th>
                <td>
                    <textarea name="skin_findings"
                        placeholder="Enter Skin findings">{{ old('skin_findings') }}</textarea>
                    @error('skin_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="skin_alerts" placeholder="alerts" value="{{ old('skin_alerts') }}">
                </td>
            </tr>

            <tr>
                <th class="system">EYES</th>
                <td>
                    <textarea name="eyes_findings"
                        placeholder="Enter Eyes findings">{{ old('eyes_findings') }}</textarea>
                    @error('eyes_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="eyes_alerts" placeholder="alerts" value="{{ old('eyes_alerts') }}">
                </td>
            </tr>

            <tr>
                <th class="system">ORAL CAVITY</th>
                <td>
                    <textarea name="oral_cavity_findings"
                        placeholder="Enter Oral Cavity findings">{{ old('oral_cavity_findings') }}</textarea>
                    @error('oral_cavity_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="oral_cavity_alerts" placeholder="alerts"
                        value="{{ old('oral_cavity_alerts') }}">
                </td>
            </tr>

            <tr>
                <th class="system">CARDIOVASCULAR</th>
                <td>
                    <textarea name="cardiovascular_findings"
                        placeholder="Enter Cardiovascular findings">{{ old('cardiovascular_findings') }}</textarea>
                    @error('cardiovascular_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="cardiovascular_alerts" placeholder="alerts"
                        value="{{ old('cardiovascular_alerts') }}">
                </td>
            </tr>

            <tr>
                <th class="system">ABDOMEN</th>
                <td>
                    <textarea name="abdomen_findings"
                        placeholder="Enter Abdomen findings">{{ old('abdomen_findings') }}</textarea>
                    @error('abdomen_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="abdomen_alerts" placeholder="alerts" value="{{ old('abdomen_alerts') }}">
                </td>
            </tr>

            <tr>
                <th class="system">EXTREMITIES</th>
                <td>
                    <textarea name="extremities_findings"
                        placeholder="Enter Extremities findings">{{ old('extremities_findings') }}</textarea>
                    @error('extremities_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="extremities_alerts" placeholder="alerts"
                        value="{{ old('extremities_alerts') }}">
                </td>
            </tr>

            <tr>
                <th class="system">NEUROLOGICAL</th>
                <td>
                    <textarea name="neurological_findings"
                        placeholder="Enter Neurological findings">{{ old('neurological_findings') }}</textarea>
                    @error('neurological_findings')
                        <div style="color:red; font-size:12px;">{{ $message }}</div>
                    @enderror
                </td>
                <td>
                    <input type="text" name="neurological_alerts" placeholder="alerts"
                        value="{{ old('neurological_alerts') }}">
                </td>
            </tr>
        </table>

        <div class="btn">
            <button type="submit">Submit</button>
        </div>
    </form>
    </div>

    <div class="cdss-btn">
        <a href="#" class="btn">CDSS</a>
    </div>

    @if (session('success'))

        <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
        </div>

        <div style="background-color: wheat; padding:1rem; text-align:center; margin:1rem;">
            @if (session('physical_exam_data'))
                <h3>TEMPORARY: Submitted Physical Exam Data:</h3>
                <ul>
                    @foreach(session('physical_exam_data') as $key => $value)
                        <strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}<br>
                    @endforeach
                </ul>
            @endif
        </div>

    @endif

</body>

</html>