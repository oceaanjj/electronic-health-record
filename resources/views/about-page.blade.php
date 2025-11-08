@extends('layouts.app')

@section('title', 'About Our EHR System')

@section('content')
    <div class="w-[85%] mx-auto my-10 p-8 bg-white rounded-xl shadow-2xl relative overflow-hidden">
        
        {{-- Decorative elements --}}
        <div class="absolute top-0 left-0 w-48 h-48 bg-gradient-to-br from-green-200 to-transparent rounded-full opacity-30 -translate-x-1/4 -translate-y-1/4"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-gradient-to-tl from-yellow-100 to-transparent rounded-full opacity-30 translate-x-1/4 translate-y-1/4"></div>

        <h1 class="text-5xl font-extrabold text-dark-green mb-10 text-center relative z-10 animate-fade-in-down">
            Unveiling Our Vision: The Future of Health Records
        </h1>

        <div class="flex flex-col md:flex-row items-center justify-center gap-12 mb-16 relative z-10">
            <div class="md:w-1/2 lg:w-2/5 relative group">
                <img src="{{ asset('img/doctor-kids.png') }}" alt="About Us" class="rounded-xl shadow-xl w-full transform transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-dark-green opacity-20 rounded-xl group-hover:opacity-0 transition-opacity duration-500"></div>
            </div>
            <div class="md:w-1/2 lg:w-3/5 text-xl text-gray-700 leading-relaxed space-y-6 animate-fade-in-right">
                <p class="font-semibold">At the heart of our mission is a commitment to revolutionize healthcare through intelligent technology. Our Electronic Health Record (EHR) system isn't just software; it's a dedicated partner for medical professionals and a guardian of patient well-being.</p>
                <p>We've engineered a platform that transcends traditional record-keeping, offering a seamless, secure, and intuitive experience. From the moment a patient enters care to their long-term health journey, every piece of information is meticulously managed, instantly accessible, and profoundly impactful.</p>
                <p>Imagine a world where administrative burdens are minimized, diagnostic processes are accelerated, and patient care is more personalized than ever before. This is the world our EHR system is building, one secure, efficient, and compassionate interaction at a time.</p>
            </div>
        </div>

        <div class="text-center mt-16 mb-16 relative z-10">
            <h2 class="text-4xl font-bold text-dark-green mb-8 animate-fade-in-up">Our Guiding Principles</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="p-6 bg-yellow-50 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <h3 class="text-2xl font-bold text-brown mb-3">Integrity & Security</h3>
                    <p class="text-gray-700">Upholding the highest standards of data protection and privacy for every patient record.</p>
                </div>
                <div class="p-6 bg-green-50 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <h3 class="text-2xl font-bold text-brown mb-3">Innovation & Efficiency</h3>
                    <p class="text-gray-700">Continuously evolving to provide cutting-edge tools that streamline workflows.</p>
                </div>
                <div class="p-6 bg-blue-50 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <h3 class="text-2xl font-bold text-brown mb-3">Patient-Centric Care</h3>
                    <p class="text-gray-700">Facilitating better communication and personalized treatment plans.</p>
                </div>
            </div>
        </div>

        <div class="mt-16 relative z-10">
            <h2 class="text-4xl font-bold text-dark-green mb-10 text-center animate-fade-in-up">Who Benefits?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                
                <div class="flex flex-col items-center text-center p-6 bg-beige rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <img src="{{ asset('img/DOCTOR.png') }}" alt="Doctor" class="w-40 h-40 mx-auto mb-5 rounded-full object-cover border-4 border-dark-green shadow-md">
                    <h3 class="text-2xl font-bold text-dark-green mb-3">For Visionary Doctors</h3>
                    <p class="text-gray-700 leading-relaxed">Gain immediate access to comprehensive patient histories, diagnostic results, and treatment plans. Our system empowers you to make informed decisions swiftly, enhancing diagnostic accuracy and treatment efficacy.</p>
                </div>

                <div class="flex flex-col items-center text-center p-6 bg-beige rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <img src="{{ asset('img/NURSE.png') }}" alt="Nurse" class="w-40 h-40 mx-auto mb-5 rounded-full object-cover border-4 border-dark-green shadow-md">
                    <h3 class="text-2xl font-bold text-dark-green mb-3">For Dedicated Nurses</h3>
                    <p class="text-gray-700 leading-relaxed">Streamline daily tasks from medication administration to vital sign tracking and activity logging. Our intuitive interface reduces paperwork, allowing more time for direct patient care and improved coordination.</p>
                </div>

                <div class="flex flex-col items-center text-center p-6 bg-beige rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 transform hover:-translate-y-2">
                    <img src="{{ asset('img/ehr-logo.png') }}" alt="EHR Logo" class="w-40 h-40 mx-auto mb-5 object-contain">
                    <h3 class="text-2xl font-bold text-dark-green mb-3">For Empowered Patients</h3>
                    <p class="text-gray-700 leading-relaxed">Benefit from a healthcare system that prioritizes your well-being. Secure access to your health information, better communication with your care team, and a more cohesive treatment journey are now a reality.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-20 relative z-10">
            <h2 class="text-4xl font-bold text-dark-green mb-8 animate-fade-in-up">Ready to Transform Healthcare?</h2>
            <p class="text-xl text-gray-600 max-w-4xl mx-auto mb-8">Join us in shaping the future of electronic health records. Experience a system built on precision, security, and a profound understanding of healthcare needs.</p>
            <a href="#" class="inline-block bg-dark-green text-white text-xl font-semibold px-10 py-4 rounded-full shadow-lg hover:bg-green-700 transform hover:scale-105 transition-all duration-300">Learn More & Contact Us</a>
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
