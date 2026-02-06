@extends('layouts.app')

@section('title', 'EHR - Components')

@section('content')
    <!-- ✅ SweetAlert Welcome Popup -->
    @if (session('sweetalert'))
        @push('scripts')
            <script>
                // Use setTimeout to ensure this doesn't block other JS initialization
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(function () {
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
                                timer: opts.timer || 2000,
                            });
                        }
                    }, 100); // Small delay to ensure page is fully initialized
                });
            </script>
        @endpush
    @endif

    <div class="min-h-screen bg-white px-10 py-12 font-sans text-gray-800">
        <div class="mb-10 text-center">
            <h1 class="mb-2 text-2xl font-bold">EHR - COMPONENTS</h1>
            <p class="text-sm text-gray-600">
                Our system offers a set of integrated components that make patient data documentation and healthcare
                management simple, secure, and accessible online.
            </p>
            <div class="mx-auto mt-4 w-[90%] border-t-2 border-green-700"></div>
        </div>

        <div class="mx-auto grid max-w-[1200px] grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
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
                    //['route' => 'discharge-planning', 'icon' => 'discharge-planning.png', 'title' => 'DISCHARGE PLANNING', 'desc' => 'Plan and document the patient’s care instructions upon discharge.']
                ];
            @endphp

            @foreach ($cards as $card)
                <a
                    href="{{ route($card['route']) }}"
                    class="group flex flex-col justify-between rounded-[20px] border border-gray-300 bg-white p-8 text-left shadow-sm transition duration-200 hover:-translate-y-1 hover:border-green-600 hover:shadow-md"
                >
                    <div>
                        <span class="material-symbols-outlined mb-4 text-green-700" style="font-size: 50px">
                            {{ $card['icon'] }}
                        </span>
                        <h2 class="mb-2 text-sm font-bold">{{ $card['title'] }}</h2>
                        <p class="mb-6 text-xs leading-relaxed text-gray-600">
                            {{ $card['desc'] }}
                        </p>
                    </div>

                    <span class="mt-auto flex items-center text-xs font-semibold text-green-700 group-hover:underline">
                        PROCEED
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="ml-1 h-3 w-3"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0-1.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
