@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    {{-- HEADER --}}
    <div class="header">
        <label for="patient" style="color: white;">PATIENT NAME :</label>
        <select id="patient" name="patient">
            <option value="">-- Select Patient --</option>
            <option value="Althea Pascua">Jovilyn Esquerra</option>
        </select>

        <label for="date" style="color: white;">DATE :</label>
        <input class="date" type="date" id="date_selector" name="date" value="{{ session('selected_date') }}"
            onchange="this.form.submit()">
    </div>

    {{-- MAIN CONTAINER (Vital Signs Layout Style) --}}
    <div class="w-[80%] mx-auto flex justify-center items-start gap-1 mt-6">

        <div class="w-[68%] rounded-[15px] overflow-hidden">
            <table class="w-full table-fixed border-collapse border-spacing-y-0">
                <tr>
                    <th class="w-[20%] bg-dark-green text-white font-bold py-2 rounded-tl-[15px]">MEDICATION</th>
                    <th class="w-[15%] bg-dark-green text-white font-bold py-2">DOSE</th>
                    <th class="w-[15%] bg-dark-green text-white font-bold py-2">ROUTE</th>
                    <th class="w-[15%] bg-dark-green text-white font-bold py-2">FREQUENCY</th>
                    <th class="w-[20%] bg-dark-green text-white font-bold py-2">COMMENTS</th>
                    <th class="w-[15%] bg-dark-green text-white font-bold py-2 rounded-tr-[15px]">TIME</th>
                </tr>

                <tr class="border-b-2 border-line-brown/70 h-[100px]">
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Medication" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Dose" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Route" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Frequency" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Comments" class="w-full h-[45px] text-center">
                    </td>
                    <th class="bg-beige text-brown font-semibold">10:00 AM</th>
                </tr>

                <tr class="border-b-2 border-line-brown/70 h-[100px]">
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Medication" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Dose" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Route" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Frequency" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Comments" class="w-full h-[45px] text-center">
                    </td>
                    <th class="bg-beige text-brown font-semibold">2:00 PM</th>
                </tr>

                <tr>
                    <td class="bg-beige text-center h-[100px]">
                        <input type="text" placeholder="Medication" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Dose" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Route" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Frequency" class="w-full h-[45px] text-center">
                    </td>
                    <td class="bg-beige text-center">
                        <input type="text" placeholder="Comments" class="w-full h-[45px] text-center">
                    </td>
                    <th class="bg-beige text-brown font-semibold">6:00 PM</th>
                </tr>
            </table>
        </div>

        {{-- ALERTS TABLE --}}
        <div class="w-[25%] rounded-[15px] overflow-hidden">
            <div class="bg-dark-green text-white font-bold py-2 mb-0.5 text-center rounded-[15px]">
                ALERTS
            </div>

            <table class="w-full border-collapse text-center">
                <tr>
                    <td>
                        <div class="alert-box my-[3px] h-[90px] flex justify-center items-center">
                            <span class="opacity-70 text-white font-semibold">No Alerts</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="alert-box my-[3px] h-[90px] flex justify-center items-center">
                            <span class="opacity-70 text-white font-semibold">No Alerts</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="alert-box my-[3px] h-[90px] flex justify-center items-center">
                            <span class="opacity-70 text-white font-semibold">No Alerts</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BUTTONS --}}
    <div class="w-[70%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
        <button class="button-default" type="submit">SUBMIT</button>
    </div>

@endsection

@push('styles')
    @vite(['resources/css/medication-administration.css'])
@endpush
