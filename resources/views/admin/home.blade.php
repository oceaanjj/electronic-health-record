@extends('layouts.admin')

@section('content')
    {{-- SweetAlert Logic (Kept as is) --}}
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

    <section class="mb-8">
        {{-- Responsive Title Size --}}
        <h2
            class="text-dark-green font-alte mt-8 mb-8 text-center text-[32px] leading-tight font-black md:mt-18 md:text-[50px]"
        >
            ADMIN OVERVIEW
        </h2>

        <center>
            {{-- Responsive Grid & Width --}}
            <div class="grid min-h-[auto] w-full grid-cols-1 gap-6 md:h-[250px] md:w-[90%] md:grid-cols-3 lg:w-[70%]">
                {{-- User Stats --}}
                <div
                    class="group gradient-gray justify-between rounded-[20px] border border-gray-300 text-left shadow-sm"
                >
                    <h3 class="font-neometric pt-10 pb-5 pl-10 text-sm leading-4 font-black text-gray-500">
                        TOTAL OF
                        <br />
                        <span class="text-dark-green/80 font-creato-bold text-[20px] font-black">USERS</span>
                    </h3>
                    <p class="text-dark-green/70 p-0 text-center text-[70px] font-extrabold">
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

    <section class="mx-auto my-12 mb-[100px] w-full md:mb-[200px] md:w-[90%] lg:w-[75%] xl:w-[70%]">
        <div class="overflow-hidden rounded-xl border border-gray-100 shadow-2xl">
            <div class="main-header px-6 py-4 pl-6 text-left text-lg tracking-wider text-white md:pl-10 md:text-xl">
                <h2>RECENT AUDIT LOGS</h2>
            </div>

            <div class="bg-white p-4 sm:p-6 md:p-8">
                {{-- Responsive Table Container --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse rounded-lg border border-gray-200 whitespace-nowrap">
                        <thead class="bg-gray-100 text-sm font-semibold text-gray-700 uppercase">
                            <tr>
                                <th class="w-1/6 border-b px-4 py-3 text-left">User</th>
                                <th class="w-1/4 border-b px-4 py-3 text-left">Action</th>
                                <th class="w-2/3 border-b px-4 py-3 text-left">Details</th>
                                <th class="w-1/6 border-b px-4 py-3 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @foreach (\App\Models\AuditLog::latest()->take(5)->get() as $log)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $log->user->username ?? 'System' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800"
                                        >
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="max-w-[200px] truncate px-4 py-3 md:max-w-none">
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
