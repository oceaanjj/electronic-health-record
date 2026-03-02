@extends('layouts.doctor')
@section('title', 'Doctor Home')
@section('content')

    @if (session('sweetalert'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(function () {
                        const opts = @json(session('sweetalert'));
                        if (typeof Swal === 'function') {
                            Swal.fire({
                                icon: opts.type || 'info',
                                title: opts.title || '',
                                text: opts.text || '',
                                timer: opts.timer || 2000,
                            });
                        }
                    }, 100);
                });
            </script>
        @endpush
    @endif

    <div class="min-h-screen bg-white px-10 py-12  text-gray-800">

        <section class="flex flex-col md:flex-row items-center justify-between mb-16 gap-10">
            <div class="max-w-2xl text-left">
                <h3 class="text-green-700 font-bold tracking-widest text-sm mb-2">WELCOME TO</h3>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                    ELECTRONIC HEALTH<br />
                    RECORD SYSTEM<br />
                    <span class="text-green-600">DOCTOR'S PORTAL</span>
                </h1>
                <p class="text-gray-600 leading-relaxed text-lg">
                    This system is designed to simplify healthcare documentation by providing a secure platform for
                    recording and managing patient information. It supports the complete documentation of medical data
                    including patient profiles, vital signs, treatments, and medication records.
                </p>
            </div>
            <div class="w-full ">
                <img src="{{ asset('img/doctor-kids.png') }}" alt="Doctor with Kids" class="w-full h-auto rounded-2xl">
            </div>
        </section>

        <div class="mb-10 text-center">
            <h2 class="mb-2 text-2xl font-bold uppercase tracking-wide">EHR - COMPONENTS</h2>
            <p class="text-sm text-gray-600 max-w-2xl mx-auto">
                Our system offers a set of integrated components that make patient data documentation and healthcare
                management simple, secure, and accessible online.
            </p>
            <div class="mx-auto mt-4 w-[90%] border-t-2 border-green-700"></div>
        </div>

        <div
            class="mx-auto grid max-w-[1000px] h-[300px] grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:ml-20 mb-5">
            @php
                $cards = [
                    [
                        'route' => 'doctor.patient-report',
                        'icon' => 'analytics', // Material symbol name
                        'title' => 'GENERATE PATIENT REPORT',
                        'desc' => 'Generate a comprehensive report of a patient\'s medical history and records.'
                    ],
                    // Add more doctor-specific cards here following the same structure
                ];
            @endphp

            @foreach ($cards as $card)
                <a href="{{ route($card['route']) }}"
                    class="group flex flex-col justify-between rounded-[20px] border border-gray-300 bg-white p-8 text-left shadow-sm transition duration-200 hover:-translate-y-1 hover:border-green-600 hover:shadow-md">
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
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

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    @vite(['resources/css/home-style.css'])
@endpush