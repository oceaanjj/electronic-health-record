@extends('layouts.app')

@section('title', 'EHR - Components')

@section('content')

    <div class="min-h-screen bg-white text-gray-800 font-sans px-10 py-12">

        <div class="text-center mb-10">
            <h1 class="text-2xl font-bold mb-2">EHR - COMPONENTS</h1>
            <p class="text-sm text-gray-600">
                Our system offers a set of integrated components that make patient data documentation
                and healthcare management simple, secure, and accessible online.
            </p>
            <div class="border-t-2 border-green-700 w-[90%] mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-5 gap-6 max-w-[1200px] mx-auto">

            @php
                $cards = [
                    ['route' => 'patients.create', 'img' => 'register.png', 'title' => 'REGISTER', 'desc' => 'This is where new patients are registered into the system.'],
                    ['route' => 'patients.index', 'img' => 'demographic-profile.png', 'title' => 'DEMOGRAPHIC PROFILE', 'desc' => 'Store and manage patient information.'],
                    ['route' => 'medical-history', 'img' => 'medical-history.png', 'title' => 'MEDICAL HISTORY', 'desc' => 'Document past illnesses, surgeries, allergies, and family medical background.'],
                    ['route' => 'physical-exam.index', 'img' => 'physical-exam.png', 'title' => 'PHYSICAL EXAM', 'desc' => 'Record findings from clinical examinations and physical assessments.'],
                    ['route' => 'vital-signs.show', 'img' => 'vital-signs.png', 'title' => 'VITAL SIGNS', 'desc' => 'Track and update measurements such as temperature, blood pressure, pulse, and respiration.'],
                    ['route' => 'io.show', 'img' => 'intake-and-output.png', 'title' => 'INTAKE AND OUTPUT', 'desc' => 'Monitor and log a patient’s fluid intake and output for accurate care management.'],
                    ['route' => 'adl.show', 'img' => 'activities-of-daily-living.png', 'title' => 'ACTIVITIES OF DAILY LIVING', 'desc' => 'Assess a patient’s ability to perform daily tasks such as eating, bathing, and mobility.'],
                    ['route' => 'lab-values.index', 'img' => 'lab-values.png', 'title' => 'LAB VALUES', 'desc' => 'Record laboratory test results and integrate findings into the patient’s medical record.'],
                    ['route' => 'diagnostics.index', 'img' => 'diagnostics.png', 'title' => 'DIAGNOSTICS', 'desc' => 'Document diagnostic procedures and results such as imaging, scans, and other tests.'],
                    ['route' => 'ivs-and-lines', 'img' => 'ivs-and-lines.png', 'title' => "IV'S & LINES", 'desc' => 'Manage intravenous lines, infusions, and related treatments.'],
                    ['route' => 'medication-administration', 'img' => 'med-admini.png', 'title' => 'MEDICATION ADMINISTRATION', 'desc' => 'Track prescribed medicines and record their administration schedules.'],
                    ['route' => 'medication-reconciliation', 'img' => 'med-recon.png', 'title' => 'MEDICATION RECONCILIATION', 'desc' => 'Compare medications to ensure accuracy and prevent duplication or errors.'],
                    ['route' => 'discharge-planning', 'img' => 'discharge-planning.png', 'title' => 'DISCHARGE PLANNING', 'desc' => 'Plan and document the patient’s care instructions upon discharge.']
                ];
            @endphp

            @foreach ($cards as $card)
                <a href="{{ route($card['route']) }}"
                    class="group border border-gray-300 rounded-[20px] p-8 flex flex-col justify-between text-left bg-white shadow-sm hover:shadow-md transition duration-200 hover:-translate-y-1 hover:border-green-600">

                    <div>
                        <img src="{{ asset('img/sidebar/' . $card['img']) }}" class="w-14 h-14 mb-4 object-contain"
                            alt="{{ $card['title'] }}">
                        <h2 class="font-bold text-sm mb-2">{{ $card['title'] }}</h2>
                        <p class="text-xs text-gray-600 mb-6 leading-relaxed">
                            {{ $card['desc'] }}
                        </p>
                    </div>

                    <span class="text-green-700 text-xs font-semibold group-hover:underline flex items-center mt-auto">
                        PROCEED
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0-1.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                </a>
            @endforeach

        </div>
    </div>
@endsection