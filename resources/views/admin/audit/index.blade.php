@vite(['resources/css/app.css', 'resources/js/app.js'])

@extends('layouts.admin')

@section('content')
    <h2 class="text-dark-green font-alte mt-18 mb-8 text-center text-[50px] font-black">AUDIT LOGS</h2>

    <div class="mx-auto my-12 w-[100%] md:w-[90%] lg:w-[85%]">
        <div class="mb-10 flex flex-col items-center justify-between gap-4 sm:flex-row">
            <div class="relative ml-10 w-full sm:w-auto">
                <input
                    type="text"
                    id="audit-search"
                    placeholder="Search by username..."
                    value="{{ request('username_search') }}"
                    class="focus:ring-dark-green focus:border-dark-green w-full rounded-full border border-gray-300 px-5 py-2 text-gray-700 shadow-sm transition duration-300 ease-in-out outline-none focus:ring-2 sm:w-[300px]"
                />

                <div id="audit-loading" class="absolute top-1/2 right-4 hidden -translate-y-1/2">
                    <svg
                        class="text-dark-green h-5 w-5 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l3 3-3 3v-4a8 8 0 01-8-8z"
                        ></path>
                    </svg>
                </div>
            </div>

            <div class="mr-10 flex flex-wrap items-center justify-center gap-3 text-gray-700 sm:justify-end">
                <span class="text-sm font-medium tracking-wide">SORT BY DATE:</span>
                <a
                    href="{{ route('audit.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_direction' => 'asc'])) }}"
                    class="rounded-full border border-gray-300 px-4 py-1.5 text-sm transition-all duration-300 hover:bg-gray-100"
                >
                    Oldest
                </a>
                <a
                    href="{{ route('audit.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_direction' => 'desc'])) }}"
                    class="rounded-full border border-gray-300 px-4 py-1.5 text-sm transition-all duration-300 hover:bg-gray-100"
                >
                    Newest
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-[20px] border border-gray-100 shadow-2xl">
            <div class="main-header font-bold tracking-wider">
                <h2 class="text-center">AUDIT LOG ENTRIES</h2>
            </div>

            <div class="overflow-x-auto bg-white">
                <table class="min-w-full border-collapse">
                    <thead class="bg-gray-100 text-sm font-semibold text-gray-700 uppercase">
                        <tr>
                            <th class="w-[10%] border-b border-gray-200 px-4 py-3 text-center">AUDIT ID</th>
                            <th class="w-[13%] border-b border-gray-200 px-4 py-3 text-center">USER</th>
                            <th class="w-[30%] border-b border-gray-200 px-4 py-3 text-left">ACTION</th>
                            <th class="w-[30%] border-b border-gray-200 px-4 py-3 text-left">DETAILS</th>
                            <th class="w-[20%] border-b border-gray-200 px-4 py-3 text-center">DATE & TIME</th>
                        </tr>
                    </thead>

                    <tbody id="audit-table-body" class="divide-y divide-gray-200 text-sm text-gray-700">
                        @forelse ($logs as $log)
                            @php
                                $details = json_decode($log->details, true);
                            @endphp

                            <tr class="transition duration-300 hover:bg-gray-50">
                                <td class="text-dark-green px-4 py-3 text-center text-[15px] font-extrabold">
                                    {{ $log->id }}
                                </td>
                                <td class="px-4 py-3 text-left font-semibold text-gray-700">
                                    {{ $log->user_name ?? 'System' }}
                                </td>
                                <td class="px-4 py-3 text-left">
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
                                <td class="px-4 py-3 text-[13px] text-gray-700">
                                    @if ($details)
                                        @if (is_array($details))
                                            @if (isset($details['details']))
                                                <div class="mb-1 text-gray-800">{{ $details['details'] }}</div>
                                            @endif

                                            @foreach ($details as $key => $value)
                                                @if ($key !== 'details' && $key !== 'user_role')
                                                    <div class="flex flex-wrap text-left text-[13px] leading-relaxed">
                                                        <span class="mr-1 font-semibold text-gray-600 capitalize">
                                                            {{ str_replace('_', ' ', $key) }}:
                                                        </span>
                                                        <span class="text-gray-800">
                                                            {{ is_array($value) ? json_encode($value) : $value }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-gray-800">{{ $log->details }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">No details provided.</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-[13px] whitespace-nowrap text-gray-600">
                                    <div>{{ $log->created_at->format('Y-m-d') }}</div>
                                    <div class="text-[12px] text-gray-400">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500 italic">No audit logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center space-y-6">
            <div class="flex justify-center">
                {{ $logs->links('pagination::tailwind') }}
            </div>

            <a href="{{ route('admin-home') }}" class="button-default w-[220px] text-center">BACK TO DASHBOARD</a>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/audit-search.js'])
@endpush
