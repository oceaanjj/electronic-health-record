@vite(['resources/css/app.css', 'resources/js/app.js'])

@extends('layouts.admin')

@section('content')
    <!-- SweetAlert  -->
    @if (session('sweetalert'))
        @push('scripts')
            <script>
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

    <section class="mb-8">
        <h2 class="text-dark-green font-alte mt-18 mb-8 text-center text-[50px] font-black">ADMIN OVERVIEW</h2>
        <center>
            <div class="grid h-[250px] w-[70%] grid-cols-1 gap-6 md:grid-cols-3">
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

    <section class="mx-auto my-12 mb-[200px] w-[100%] md:w-[90%] lg:w-[75%] xl:w-[70%]">
        <div class="overflow-hidden rounded-xl border border-gray-100 shadow-2xl">
            <div class="main-header px-6 py-3 pl-10 text-left text-xl tracking-wider text-white">
                <h2>RECENT AUDIT LOGS</h2>
            </div>

            <div class="overflow-x-auto bg-white p-6 sm:p-8">
                <table class="min-w-full border-collapse rounded-lg border border-gray-200">
                    <thead class="bg-gray-100 text-sm font-semibold text-gray-700 uppercase">
                        <tr>
                            <th class="w-1/6 border-b border-gray-200 px-4 py-3 text-left">User</th>
                            <th class="w-1/4 border-b border-gray-200 px-4 py-3 text-left">Action</th>
                            <th class="w-2/3 border-b border-gray-200 px-4 py-3 text-left">Details</th>
                            <th class="w-1/6 border-b border-gray-200 px-4 py-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                        @foreach (\App\Models\AuditLog::latest()->take(5)->get() as $log)
                            @php
                                $details = json_decode($log->details, true);
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $log->user->username ?? 'System' }}</td>

                                <td class="px-4 py-3">
                                    <span
                                        class="{{
                                            $log->action == 'Deleted'
                                                ? 'bg-red-100 text-red-800'
                                                : ($log->action == 'Updated'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-green-100 text-green-800')
                                        }} inline-block rounded-full px-3 py-1 text-xs font-semibold"
                                    >
                                        {{ $log->action }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 align-middle text-gray-700">
                                    @if (is_array($details))
                                        <div class="space-y-1 text-sm">
                                            @foreach ($details as $key => $value)
                                                @if ($key !== 'user_role' && $key !== 'details')
                                                    <div class="flex flex-wrap">
                                                        <span class="mr-1 font-semibold text-gray-700 capitalize">
                                                            {{ str_replace('_', ' ', $key) }}:
                                                        </span>
                                                        <span class="text-gray-800">{{ $value }}</span>
                                                    </div>
                                                @elseif ($key === 'details')
                                                    <span class="text-gray-800">{{ $value }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-800">{{ $log->details }}</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-gray-500">
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
        document.addEventListener('DOMContentLoaded', () => {
            const msg = document.getElementById('success-message');
            if (msg) {
                setTimeout(() => {
                    msg.style.transition = 'opacity 0.6s ease';
                    msg.style.opacity = 0;
                    setTimeout(() => msg.remove(), 600);
                }, 4000); // 4 seconds
            }
        });
    </script>
@endpush
