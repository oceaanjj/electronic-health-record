@extends('layouts.app')

@section('title', 'EHR - Components')

@section('content')

    <!-- ✅ SweetAlert Welcome Popup -->
    @if(session('sweetalert'))
        @push('scripts')
        <script>
            // Use setTimeout to ensure this doesn't block other JS initialization
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const opts = @json(session('sweetalert'));
                    if (typeof showSuccess === 'function' && opts.type === 'success') {
                        showSuccess(opts.text || opts.title, opts.title || 'Success!', opts.timer);
                    } else if (typeof showError === 'function' && opts.type === 'error') {
                        showError(opts.text || opts.title, opts.title || 'Error!', opts.timer);
                    } else if (typeof showWarning === 'function' && opts.type === 'warning') {
                        showWarning(opts.text || opts.title, opts.title || 'Warning!', opts.timer);
                    } else if (typeof showInfo === 'function' && opts.type === 'info') {
                        showInfo(opts.text || opts.title, opts.title || 'Info', opts.timer);
                    } else if (typeof Swal === 'function') {
                        Swal.fire({
                            icon: opts.type || 'info',
                            title: opts.title || '',
                            text: opts.text || '',
                            timer: opts.timer || 2000
                        });
                    }
                }, 100); // Small delay to ensure page is fully initialized
            });
        </script>
        @endpush
    @endif

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
                    ['route' => 'patients.create', 'icon' => 'account_box', 'title' => 'REGISTER', 'desc' => 'This is where new patients are registered into the system.'],
                    ['route' => 'patients.index', 'icon' => 'article_person', 'title' => 'DEMOGRAPHIC PROFILE', 'desc' => 'Store and manage patient information.'],
                    ['route' => 'medical-history', 'icon' => 'history', 'title' => 'MEDICAL HISTORY', 'desc' => 'Document past illnesses, surgeries, allergies, and family medical background.'],
                    ['route' => 'physical-exam.index', 'icon' => 'physical_therapy', 'title' => 'PHYSICAL EXAM', 'desc' => 'Record findings from clinical examinations and physical assessments.'],
                    ['route' => 'vital-signs.show', 'icon' => 'ecg_heart', 'title' => 'VITAL SIGNS', 'desc' => 'Track and update measurements such as temperature, blood pressure, pulse, and respiration.'],
                    ['route' => 'io.show', 'icon' => 'pill', 'title' => 'INTAKE AND OUTPUT', 'desc' => 'Monitor and log a patient’s fluid intake and output for accurate care management.'],
                    ['route' => 'adl.show', 'icon' => 'toys_and_games', 'title' => 'ACTIVITIES OF DAILY LIVING', 'desc' => 'Assess a patient’s ability to perform daily tasks such as eating, bathing, and mobility.'],
                    ['route' => 'lab-values.index', 'icon' => 'experiment', 'title' => 'LAB VALUES', 'desc' => 'Record laboratory test results and integrate findings into the patient’s medical record.'],
                    ['route' => 'diagnostics.index', 'icon' => 'diagnosis', 'title' => 'DIAGNOSTICS', 'desc' => 'Document diagnostic procedures and results such as imaging, scans, and other tests.'],
                    ['route' => 'ivs-and-lines', 'icon' => 'blood_pressure', 'title' => "IV'S & LINES", 'desc' => 'Manage intravenous lines, infusions, and related treatments.'],
                    ['route' => 'medication-administration', 'icon' => 'medication', 'title' => 'MEDICATION ADMINISTRATION', 'desc' => 'Track prescribed medicines and record their administration schedules.'],
                    ['route' => 'medication-reconciliation', 'icon' => 'admin_meds', 'title' => 'MEDICATION RECONCILIATION', 'desc' => 'Compare medications to ensure accuracy and prevent duplication or errors.'],
                   // ['route' => 'discharge-planning', 'icon' => 'discharge-planning.png', 'title' => 'DISCHARGE PLANNING', 'desc' => 'Plan and document the patient’s care instructions upon discharge.']
                ];
            @endphp

            @foreach ($cards as $card)
                <a href="{{ route($card['route']) }}"
                    class="group border border-gray-300 rounded-[20px] p-8 flex flex-col justify-between text-left bg-white shadow-sm hover:shadow-md transition duration-200 hover:-translate-y-1 hover:border-green-600">

                    <div>

                        <span class="material-symbols-outlined text-green-700 mb-4"  style="font-size: 50px;">
                            {{ $card['icon'] }}
                        </span>
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