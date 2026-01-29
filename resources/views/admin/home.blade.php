@vite(['resources/css/app.css', 'resources/js/app.js'])

@extends('layouts.admin')

@section('content')

    <!-- SweetAlert  -->
    @if(session('sweetalert'))
        @push('scripts')
        <script>
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

    <section class="mb-8">
        <h2 class="text-[50px] font-black mb-8 text-dark-green mt-18 text-center font-alte">ADMIN OVERVIEW</h2>
        <center>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-[70%] h-[250px]">
                <div class="group border border-gray-300 rounded-[20px] justify-between text-left gradient-gray shadow-sm">
                    <h3 class="text-sm text-gray-500 font-neometric font-black pl-10 pt-10 pb-5 leading-4">TOTAL OF <br> <span class="text-dark-green/80 text-[20px] font-creato-bold font-black">USERS</span></h3>
                    <p class="text-[70px] font-extrabold text-dark-green/70 text-center p-0">
                        {{ \App\Models\User::where('role', '!=', 'admin')->count() }}
                    </p>
                </div>
                
                <div class="group border border-gray-300 rounded-[20px] justify-between text-left gradient-gray shadow-sm">
                    <h3 class="text-sm text-gray-500 font-neometric font-black pl-10 pt-10 pb-5 leading-4">TOTAL OF <br> <span class="text-dark-green/80 text-[20px] font-creato-bold font-black">DOCTORS</span></h3>
                    <p class="text-[70px] font-extrabold text-dark-green/70 text-center p-0">
                        {{ \App\Models\User::where('role', 'doctor')->count() }}
                    </p>
                </div>
                
                
                <div class="group border border-gray-300 rounded-[20px] justify-between text-left gradient-gray shadow-sm">
                    <h3 class="text-sm text-gray-500 font-neometric font-black pl-10 pt-10 pb-5 leading-4">TOTAL OF <br> <span class="text-dark-green/80 text-[20px] font-creato-bold font-black">NURSES</span></h3>
                    <p class="text-[70px] font-extrabold text-dark-green/70 text-center p-0">
                        {{ \App\Models\User::where('role', 'nurse')->count() }}
                    </p>
                </div>
            </div>
        </center>
    </section>


            <section class="w-[100%] md:w-[90%] lg:w-[75%] xl:w-[70%] mx-auto my-12 mb-[200px]">
                <div class="shadow-2xl rounded-xl overflow-hidden border border-gray-100">

                    <div class="main-header text-white text-left py-3 pl-10 px-6 text-xl tracking-wider">
                        <h2>RECENT AUDIT LOGS</h2>
                    </div>

                    <div class="bg-white p-6 sm:p-8 overflow-x-auto">
                        <table class="min-w-full border-collapse border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                                <tr>
                                    <th class="px-4 py-3 text-left border-b border-gray-200 w-1/6">User</th>
                                    <th class="px-4 py-3 text-left border-b border-gray-200 w-1/4">Action</th>
                                    <th class="px-4 py-3 text-left border-b border-gray-200 w-2/3">Details</th>
                                    <th class="px-4 py-3 text-left border-b border-gray-200 w-1/6">Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                                @foreach(\App\Models\AuditLog::latest()->take(5)->get() as $log)
                                    @php
                                        $details = json_decode($log->details, true);
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 font-medium">{{ $log->user->username ?? 'System' }}</td>

                                        <td class="px-4 py-3">
                                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                                {{ $log->action == 'Deleted' ? 'bg-red-100 text-red-800' : 
                                                ($log->action == 'Updated' ? 'bg-yellow-100 text-yellow-800' : 
                                                'bg-green-100 text-green-800') }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>


                                        <td class="px-4 py-3 text-gray-700 align-middle">
                                            @if (is_array($details))
                                                <div class="space-y-1 text-sm">
                                                    @foreach($details as $key => $value)
                                                        @if($key !== 'user_role' && $key !== 'details')
                                                            <div class="flex flex-wrap">
                                                                <span class="font-semibold text-gray-700 capitalize mr-1">
                                                                    {{ str_replace('_', ' ', $key) }}:
                                                                </span>
                                                                <span class="text-gray-800">{{ $value }}</span>
                                                            </div>
                                                        @elseif($key === 'details')
                                                            <span class="text-gray-800">{{ $value }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-gray-800">{{ $log->details }}</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                            {{ $log->created_at->format('M d, Y h:i A') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>




@endsection

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const msg = document.getElementById("success-message");
        if (msg) {
            setTimeout(() => {
                msg.style.transition = "opacity 0.6s ease";
                msg.style.opacity = 0;
                setTimeout(() => msg.remove(), 600);
            }, 4000); // 4 seconds
        }
    });
</script>
@endpush
