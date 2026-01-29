@extends('layouts.app')

@section('title', 'About Our EHR System')

@section('content')
    <div class="relative mx-auto my-10 w-[85%] overflow-hidden rounded-xl bg-white p-8 shadow-2xl">
        {{-- Decorative elements --}}
        <div
            class="absolute top-0 left-0 h-48 w-48 -translate-x-1/4 -translate-y-1/4 rounded-full bg-gradient-to-br from-green-200 to-transparent opacity-30"
        ></div>
        <div
            class="absolute right-0 bottom-0 h-64 w-64 translate-x-1/4 translate-y-1/4 rounded-full bg-gradient-to-tl from-yellow-100 to-transparent opacity-30"
        ></div>

        <h1 class="text-dark-green animate-fade-in-down relative z-10 mb-10 text-center text-5xl font-extrabold">
            Unveiling Our Vision: The Future of Health Records
        </h1>

        <div class="relative z-10 mb-16 flex flex-col items-center justify-center gap-12 md:flex-row">
            <div class="group relative md:w-1/2 lg:w-2/5">
                <img
                    src="{{ asset('img/doctor-kids.png') }}"
                    alt="About Us"
                    class="w-full transform rounded-xl shadow-xl transition-transform duration-500 group-hover:scale-105"
                />
                <div
                    class="bg-dark-green absolute inset-0 rounded-xl opacity-20 transition-opacity duration-500 group-hover:opacity-0"
                ></div>
            </div>
            <div class="animate-fade-in-right space-y-6 text-xl leading-relaxed text-gray-700 md:w-1/2 lg:w-3/5">
                <p class="font-semibold">
                    At the heart of our mission is a commitment to revolutionize healthcare through intelligent
                    technology. Our Electronic Health Record (EHR) system isn't just software; it's a dedicated partner
                    for medical professionals and a guardian of patient well-being.
                </p>
                <p>
                    We've engineered a platform that transcends traditional record-keeping, offering a seamless, secure,
                    and intuitive experience. From the moment a patient enters care to their long-term health journey,
                    every piece of information is meticulously managed, instantly accessible, and profoundly impactful.
                </p>
                <p>
                    Imagine a world where administrative burdens are minimized, diagnostic processes are accelerated,
                    and patient care is more personalized than ever before. This is the world our EHR system is
                    building, one secure, efficient, and compassionate interaction at a time.
                </p>
            </div>
        </div>

        <div class="relative z-10 mt-16 mb-16 text-center">
            <h2 class="text-dark-green animate-fade-in-up mb-8 text-4xl font-bold">Our Guiding Principles</h2>
            <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 md:grid-cols-3">
                <div
                    class="transform rounded-lg bg-yellow-50 p-6 shadow-lg transition-shadow duration-300 hover:-translate-y-2 hover:shadow-xl"
                >
                    <h3 class="text-brown mb-3 text-2xl font-bold">Integrity & Security</h3>
                    <p class="text-gray-700">
                        Upholding the highest standards of data protection and privacy for every patient record.
                    </p>
                </div>
                <div
                    class="transform rounded-lg bg-green-50 p-6 shadow-lg transition-shadow duration-300 hover:-translate-y-2 hover:shadow-xl"
                >
                    <h3 class="text-brown mb-3 text-2xl font-bold">Innovation & Efficiency</h3>
                    <p class="text-gray-700">
                        Continuously evolving to provide cutting-edge tools that streamline workflows.
                    </p>
                </div>
                <div
                    class="transform rounded-lg bg-blue-50 p-6 shadow-lg transition-shadow duration-300 hover:-translate-y-2 hover:shadow-xl"
                >
                    <h3 class="text-brown mb-3 text-2xl font-bold">Patient-Centric Care</h3>
                    <p class="text-gray-700">Facilitating better communication and personalized treatment plans.</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-16">
            <h2 class="text-dark-green animate-fade-in-up mb-10 text-center text-4xl font-bold">Who Benefits?</h2>
            <div class="grid grid-cols-1 gap-12 md:grid-cols-2 lg:grid-cols-3">
                <div
                    class="bg-beige flex transform flex-col items-center rounded-lg p-6 text-center shadow-lg transition-shadow duration-300 hover:-translate-y-2 hover:shadow-xl"
                >
                    <img
                        src="{{ asset('img/DOCTOR.png') }}"
                        alt="Doctor"
                        class="border-dark-green mx-auto mb-5 h-40 w-40 rounded-full border-4 object-cover shadow-md"
                    />
                    <h3 class="text-dark-green mb-3 text-2xl font-bold">For Visionary Doctors</h3>
                    <p class="leading-relaxed text-gray-700">
                        Gain immediate access to comprehensive patient histories, diagnostic results, and treatment
                        plans. Our system empowers you to make informed decisions swiftly, enhancing diagnostic accuracy
                        and treatment efficacy.
                    </p>
                </div>

                <div
                    class="bg-beige flex transform flex-col items-center rounded-lg p-6 text-center shadow-lg transition-shadow duration-300 hover:-translate-y-2 hover:shadow-xl"
                >
                    <img
                        src="{{ asset('img/NURSE.png') }}"
                        alt="Nurse"
                        class="border-dark-green mx-auto mb-5 h-40 w-40 rounded-full border-4 object-cover shadow-md"
                    />
                    <h3 class="text-dark-green mb-3 text-2xl font-bold">For Dedicated Nurses</h3>
                    <p class="leading-relaxed text-gray-700">
                        Streamline daily tasks from medication administration to vital sign tracking and activity
                        logging. Our intuitive interface reduces paperwork, allowing more time for direct patient care
                        and improved coordination.
                    </p>
                </div>

                <div
                    class="bg-beige flex transform flex-col items-center rounded-lg p-6 text-center shadow-lg transition-shadow duration-300 hover:-translate-y-2 hover:shadow-xl"
                >
                    <img
                        src="{{ asset('img/ehr-logo.png') }}"
                        alt="EHR Logo"
                        class="mx-auto mb-5 h-40 w-40 object-contain"
                    />
                    <h3 class="text-dark-green mb-3 text-2xl font-bold">For Empowered Patients</h3>
                    <p class="leading-relaxed text-gray-700">
                        Benefit from a healthcare system that prioritizes your well-being. Secure access to your health
                        information, better communication with your care team, and a more cohesive treatment journey are
                        now a reality.
                    </p>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-20 text-center">
            <h2 class="text-dark-green animate-fade-in-up mb-8 text-4xl font-bold">Ready to Transform Healthcare?</h2>
            <p class="mx-auto mb-8 max-w-4xl text-xl text-gray-600">
                Join us in shaping the future of electronic health records. Experience a system built on precision,
                security, and a profound understanding of healthcare needs.
            </p>
            <a
                href="#"
                class="bg-dark-green inline-block transform rounded-full px-10 py-4 text-xl font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:bg-green-700"
            >
                Learn More & Contact Us
            </a>
        </div>
    </div>

    {{-- Simple CSS for animations --}}
    <style>
        @keyframes fade-in-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in-right {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 1s ease-out forwards;
        }

        .animate-fade-in-right {
            animation: fade-in-right 1s ease-out forwards;
            animation-delay: 0.3s;
        }

        .animate-fade-in-up {
            animation: fade-in-up 1s ease-out forwards;
            animation-delay: 0.5s;
        }
    </style>
@endsection
