@extends('layouts.admin')

@section('content')

    {{-- SweetAlert Logic (Kept as is) --}}
    @if(session('sweetalert'))
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const opts = @json(session('sweetalert'));
                    if (typeof Swal === 'function') {
                        Swal.fire({
                            icon: opts.type || 'info',
                            title: opts.title || '',
                            text: opts.text || '',
                            timer: opts.timer || 2000
                        });
                    }
                }, 100); 
            });
        </script>
        @endpush
    @endif

    <section class="mb-8">
        {{-- Responsive Title Size --}}
        <h2 class="text-[32px] md:text-[50px] font-black mb-8 text-dark-green mt-8 md:mt-18 text-center font-alte leading-tight">
            ADMIN OVERVIEW
        </h2>
        
        <center>
            {{-- Responsive Grid & Width --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full md:w-[90%] lg:w-[70%] min-h-[auto] md:h-[250px]">
                
                {{-- User Stats --}}
                <div class="group border border-gray-300 rounded-[20px] justify-between text-left gradient-gray shadow-sm">
                    <h3 class="text-sm text-gray-500 font-neometric font-black pl-10 pt-10 pb-5 leading-4">TOTAL OF <br> <span class="text-dark-green/80 text-[20px] font-creato-bold font-black">USERS</span></h3>
                    <p class="text-[70px] font-extrabold text-dark-green/70 text-center p-0">
                        {{ \App\Models\User::where('role', '!=', 'admin')->count() }}
                    </p>
                </div>

                <div
                    class="group gradient-gray justify-between rounded-[20px] border border-gray-300 text-left shadow-sm"
                >
                    <h3 class="font-neometric pt-10 pb-5 pl-10 text-sm leading-4 font-black text-gray-500">
                        TOTAL OF
                        <br />
                        <span class="text-dark-green/80 font-creato-bold text-[20px] font-black">DOCTORS</span>
                    </h3>
                    <p class="text-dark-green/70 p-0 text-center text-[70px] font-extrabold">
                        {{ \App\Models\User::where('role', 'doctor')->count() }}
                    </p>
                </div>

                <div
                    class="group gradient-gray justify-between rounded-[20px] border border-gray-300 text-left shadow-sm"
                >
                    <h3 class="font-neometric pt-10 pb-5 pl-10 text-sm leading-4 font-black text-gray-500">
                        TOTAL OF
                        <br />
                        <span class="text-dark-green/80 font-creato-bold text-[20px] font-black">NURSES</span>
                    </h3>
                    <p class="text-dark-green/70 p-0 text-center text-[70px] font-extrabold">
                        {{ \App\Models\User::where('role', 'nurse')->count() }}
                    </p>
                </div>
            </div>
        </center>
    </section>

    <section class="w-full md:w-[90%] lg:w-[75%] xl:w-[70%] mx-auto my-12 mb-[100px] md:mb-[200px]">
        <div class="shadow-2xl rounded-xl overflow-hidden border border-gray-100">

            <div class="main-header text-white text-left py-4 pl-6 md:pl-10 px-6 text-lg md:text-xl tracking-wider">
                <h2>RECENT AUDIT LOGS</h2>
            </div>

            <div class="bg-white p-4 sm:p-6 md:p-8">
                {{-- Responsive Table Container --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-200 rounded-lg whitespace-nowrap">
                        <thead class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3 text-left border-b w-1/6">User</th>
                                <th class="px-4 py-3 text-left border-b w-1/4">Action</th>
                                <th class="px-4 py-3 text-left border-b w-2/3">Details</th>
                                <th class="px-4 py-3 text-left border-b w-1/6">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                            @foreach(\App\Models\AuditLog::latest()->take(5)->get() as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 font-medium">{{ $log->user->username ?? 'System' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 max-w-[200px] md:max-w-none truncate">
                                        {{ $log->details }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush
