@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

<div class="w-[80%] mx-auto mt-4">
    @if (session('success'))
        <div style="color: green; background: #e0f8e0; border: 1px solid green; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="color: red; background: #f8e0e0; border: 1px solid red; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red; background: #f8e0e0; border: 1px solid red; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
            <strong>Oops! Theres an error:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<form method="POST" action="{{ route('medication-administration.store') }}">
    @csrf

    <div class="header flex items-center gap-6 my-10 mx-auto w-[80%]">
        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
            PATIENT NAME :
        </label>

        {{-- Dropdown para sa pagpili ng patient --}}
        <div class="searchable-dropdown relative w-[280px]">
            <input 
                type="text" 
                        id="patient_search_input"
                        placeholder="- Select or type to search -" 
                        value="{{ trim($selectedPatient->name ?? '') }}"
                        autocomplete="off"
                        class="w-full px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
            >

            {{-- dito yung dropdown options --}}
            <div id="patient_options_container" 
                class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                @foreach ($patients as $patient)
                    <div 
                        class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150" 
                        data-value="{{ $patient->patient_id }}">
                        {{ trim($patient->name) }}
                    </div>
                @endforeach
            </div>
        </div>
        
        <input type="hidden" name="patient_id" id="patient_id_for_form" value="{{ $selectedPatient->patient_id ?? '' }}">

        {{-- 📅 DATE SELECTOR --}}
        <label for="date_selector" class="whitespace-nowrap font-alte font-bold text-dark-green">
            DATE :
        </label>
        <input
            type="date"
            id="date_selector"
            name="date"
            value="{{ $currentDate ?? now()->format('Y-m-d') }}"
            @if (!$selectedPatient) disabled @endif
            class="text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
        >
    </div>

    {{-- MAIN CONTAINER --}}
    <div class="w-[80%] mx-auto flex justify-center items-start gap-1 mt-6">

        <div class="w-[68%] rounded-[15px] overflow-hidden">
            <table class="w-full table-fixed border-collapse border-spacing-y-0">
                <tr>
                    <th class="w-[20%] main-header rounded-tl-[15px]">MEDICATION</th>
                    <th class="w-[15%] main-header">DOSE</th>
                    <th class="w-[15%] main-header">ROUTE</th>
                    <th class="w-[15%] main-header">FREQUENCY</th>
                    <th class="w-[20%] main-header">COMMENTS</th>
                    <th class="w-[15%] main-header rounded-tr-[15px]">TIME</th>
                </tr>

                {{-- Row 1 (10:00 AM) --}}
                <tr class="border-b-2 border-line-brown/70 h-[100px]">
                    <td class="bg-beige text-center">
                        <input type="text" name="medication[]" placeholder="Medication" class="w-full h-[45px] text-center">
                        <input type="hidden" name="time[]" value="10:00:00">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="dose[]" placeholder="Dose" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="route[]" placeholder="Route" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="frequency[]" placeholder="Frequency" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="comments[]" placeholder="Comments" class="w-full h-[45px] text-center">
                    </td>
                    <th class="table-header border-line-brown border-l-2">10:00 AM</th>
                </tr>

                {{-- Row 2 (2:00 PM) --}}
                <tr class="border-b-2 border-line-brown/70 h-[100px]">
                    <td class="bg-beige text-center">
                        <input type="text" name="medication[]" placeholder="Medication" class="w-full h-[45px] text-center">
                        <input type="hidden" name="time[]" value="14:00:00">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="dose[]" placeholder="Dose" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="route[]" placeholder="Route" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="frequency[]" placeholder="Frequency" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="comments[]" placeholder="Comments" class="w-full h-[45px] text-center">
                    </td>
                    <th class="table-header border-line-brown border-l-2">2:00 PM</th>
                </tr>

                {{-- Row 3 (6:00 PM) --}}
                <tr>
                    <td class="bg-beige text-center h-[100px]">
                        <input type="text" name="medication[]" placeholder="Medication" class="w-full h-[45px] text-center">

                        <input type="hidden" name="time[]" value="18:00:00">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="dose[]" placeholder="Dose" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="route[]" placeholder="Route" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="frequency[]" placeholder="Frequency" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" name="comments[]" placeholder="Comments" class="w-full h-[45px] text-center">
                    </td>
                    <th class="table-header border-line-brown border-l-2">6:00 PM</th>
                </tr>
            </table>
        </div>

        <div class="w-[25%] rounded-[15px] overflow-hidden">
            <div class="main-header rounded-[15px]">
                ALERTS
            </div>
            <table class="w-full border-collapse text-center">
                <tr><td><div class="alert-box my-[3px] h-[90px] flex justify-center items-center"><span class="opacity-70 text-white font-semibold">No Alerts</span></div></td></tr>
                <tr><td><div class="alert-box my-[3px] h-[90px] flex justify-center items-center"><span class="opacity-70 text-white font-semibold">No Alerts</span></div></td></tr>
                <tr><td><div class="alert-box my-[3px] h-[90px] flex justify-center items-center"><span class="opacity-70 text-white font-semibold">No Alerts</span></div></td></tr>
            </table>
        </div>
    </div>

    <div class="w-[70%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
        <button class="button-default" type="submit">SUBMIT</button>
    </div>

</form>

@endsection

@push('styles')
    @vite(['resources/css/medication-administration.css'])
@endpush

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/date-day-loader.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('patient_search_input');
            const optionsContainer = document.getElementById('patient_options_container');
            const options = optionsContainer.querySelectorAll('.option');
            const hiddenPatientIdInput = document.getElementById('patient_id_for_form');
            const dateInput = document.getElementById('date_selector');

            searchInput.addEventListener('focus', () => {
                optionsContainer.classList.remove('hidden');
            });

            searchInput.addEventListener('input', () => {
                const filter = searchInput.value.toLowerCase();
                options.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
                optionsContainer.classList.remove('hidden');
            });

            options.forEach(option => {
                option.addEventListener('click', () => {
                    const patientId = option.getAttribute('data-value');
                    const patientName = option.textContent.trim();

                    searchInput.value = patientName;
                    hiddenPatientIdInput.value = patientId;
                    dateInput.disabled = false;

                    optionsContainer.classList.add('hidden');
                });
            });
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !optionsContainer.contains(e.target)) {
                    optionsContainer.classList.add('hidden');
                }
            });
        });
    </script>
@endpush