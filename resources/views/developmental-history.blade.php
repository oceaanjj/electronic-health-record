@extends('layouts.app')

@section('title', 'Patient Medical History')

@section('content')

    <form action="{{ route('medical-history.select') }}" method="POST">
        @csrf
            <div class="header">
                    <label for="patient_id" style="color: white;">PATIENT NAME :</label>
                    <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                        <option value="">-- Select Patient --</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->patient_id }}" {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                                {{ $patient->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

    </form>


    {{-- FORM for data submission (submits with POST) --}}
    <form action="{{ route('medical.store') }}" method="POST">
        @csrf

        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

        <center>
            <p class="mb-1 w-[72%] flex justify-center bg-dark-green text-white rounded-lg">DEVELOPMENTAL HISTORY </p>
        </center>
        

        <center>
            {{-- DEVELOPMENTAL HISTORY --}}
            <table class="mb-1.5 w-[72%] border-separate border-spacing-0">
            
            
                

                {{-- GROSS MOTOR --}}
                <tr>
                    <th rowspan="2" class="w-[200px] bg-dark-green text-white rounded-l-lg">GROSS MOTOR</th>
                    <th
                        class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-t-2 border-line-brown rounded-tr-lg">
                        FINDINGS</th>
                </tr>

                <tr>
                    <td class="rounded-br-lg">
                        <textarea class="notepad-lines h-[100px]" name="gross_motor"
                            placeholder="Type here...">{{ $developmentalHistory->gross_motor ?? '' }}</textarea>
                    </td>
                </tr>
            </table>


            {{-- FINE MOTOR --}}
            <table class="mb-1.5 w-[72%] border-separate border-spacing-0">
                <tr>
                    <th rowspan="2" class="w-[200px] bg-dark-green text-white rounded-l-lg">FINE MOTOR</th>
                    <th
                        class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-t-2 border-line-brown rounded-tr-lg">
                        FINDINGS</th>
                </tr>

                <tr>
                    <td class="rounded-br-lg">
                        <textarea class="notepad-lines h-[100px]" name="fine_motor"
                            placeholder="Type here...">{{ $developmentalHistory->fine_motor ?? '' }}</textarea>
                    </td>
                </tr>
            </table>


            {{-- LANGUAGE --}}
            <table class="mb-1.5 w-[72%] border-separate border-spacing-0">
                <tr>
                    <th rowspan="2" class="w-[200px] bg-dark-green text-white rounded-l-lg">LANGUAGE</th>
                    <th
                        class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-t-2 border-line-brown rounded-tr-lg">
                        FINDINGS</th>
                </tr>

                <tr>
                    <td class="rounded-br-lg">
                        <textarea class="notepad-lines h-[100px]" name="language"
                            placeholder="Type here...">{{ $developmentalHistory->language ?? '' }}</textarea>
                    </td>
                </tr>
            </table>

            {{-- COGNITIVE --}}
            <table class="mb-1.5 w-[72%] border-separate border-spacing-0">
                <tr>
                    <th rowspan="2" class="w-[200px] bg-dark-green text-white rounded-l-lg">COGNITIVE</th>
                    <th
                        class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-t-2 border-line-brown rounded-tr-lg">
                        FINDINGS</th>
                </tr>

                <tr>
                    <td class="rounded-br-lg">
                        <textarea class="notepad-lines h-[100px]" name="cognitive"
                            placeholder="Type here...">{{ $developmentalHistory->cognitive ?? '' }}</textarea>
                    </td>
                </tr>
            </table>

            {{-- SOCIAL --}}
            <table class="mb-1.5 w-[72%] border-separate border-spacing-0">
                <tr>
                    <th rowspan="2" class="w-[200px] bg-dark-green text-white rounded-l-lg">SOCIAL</th>
                    <th
                        class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-t-2 border-line-brown rounded-tr-lg">
                        FINDINGS</th>
                </tr>

                <tr>
                    <td class="rounded-br-lg">
                        <textarea class="notepad-lines h-[100px]"
                            name="social"
                            placeholder="Type here..."
                            >{{ $developmentalHistory->social ?? '' }}</textarea>
                    </td>
                </tr>
            </table>

            
        </center>


            <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30 space-x-4">
                {{-- paayos ako ng routing here, dapat babalik sa medical history --}}
                <a href="{{ route('medical-history') }}">
                    <button type="button" class="button-default">BACK</button>
                </a>

                {{-- mapupunta na dapat sa database lahat ng input sa medical history & developmental history --}}
                <button type="submit" class="button-default">SUBMIT</button>
            </div>
        </div>




        <div class="buttons">
            <button type="submit" class="btn">Submit</button>
        </div>
    </form>




@endsection