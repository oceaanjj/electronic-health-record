@vite(['resources/css/app.css', 'resources/js/app.js'])

@extends('layouts.admin')

@section('content')

<h2 class="text-[50px] font-black mb-8 text-dark-green mt-18 text-center font-alte">AUDIT LOGS</h2>

<div class="w-[100%] md:w-[90%] lg:w-[85%] mx-auto my-12">

    

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-10">

        <div class="relative w-full sm:w-auto ml-10">
            <input 
                type="text" 
                id="audit-search" 
                placeholder="Search by username..."
                value="{{ request('username_search') }}"
                class="w-full sm:w-[300px] px-5 py-2 rounded-full border border-gray-300 
                       focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none 
                       shadow-sm transition duration-300 ease-in-out text-gray-700"
            >

            <div id="audit-loading" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                <svg class="w-5 h-5 text-dark-green animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l3 3-3 3v-4a8 8 0 01-8-8z"></path>
                </svg>
            </div>
        </div>

        <div class="flex flex-wrap justify-center sm:justify-end items-center gap-3 text-gray-700 mr-10">
            <span class="font-medium text-sm tracking-wide">SORT BY DATE:</span>
            <a href="{{ route('audit.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_direction' => 'asc'])) }}"
               class="px-4 py-1.5 border border-gray-300 rounded-full hover:bg-gray-100 text-sm transition-all duration-300">
               Oldest
            </a>
            <a href="{{ route('audit.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_direction' => 'desc'])) }}"
               class="px-4 py-1.5 border border-gray-300 rounded-full hover:bg-gray-100 text-sm transition-all duration-300">
               Newest
            </a>
        </div>
    </div>


    <div class="shadow-2xl rounded-[20px] overflow-hidden border border-gray-100">
        

        <div class="main-header font-bold tracking-wider">
            <h2 class="text-center">AUDIT LOG ENTRIES</h2>
        </div>

        <div class="bg-white overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-center border-b border-gray-200 w-[10%]">AUDIT ID</th>
                        <th class="px-4 py-3 text-center border-b border-gray-200 w-[13%]">USER</th>
                        <th class="px-4 py-3 text-left border-b border-gray-200 w-[30%]">ACTION</th>
                        <th class="px-4 py-3 text-left border-b border-gray-200 w-[30%]">DETAILS</th>
                        <th class="px-4 py-3 text-center border-b border-gray-200 w-[20%]">DATE & TIME</th>
                    </tr>
                </thead>

                <tbody id="audit-table-body" class="text-gray-700 text-sm divide-y divide-gray-200">
                    @forelse($logs as $log)
                        @php $details = json_decode($log->details, true); @endphp
                        <tr class="hover:bg-gray-50 transition duration-300">
                            <td class="px-4 py-3 text-center font-extrabold text-[15px] text-dark-green">
                                {{ $log->id }}
                            </td>
                            <td class="px-4 py-3 text-left font-semibold text-gray-700">
                                {{ $log->user_name ?? 'System' }}
                            </td>
                            <td class="px-4 py-3 text-left">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $log->action == 'Deleted' ? 'bg-red-100 text-red-800' : 
                                       ($log->action == 'Updated' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-green-100 text-green-800') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 text-[13px]">
                                @if ($details)
                                    @if (is_array($details))
                                        @if(isset($details['details']))
                                            <div class="text-gray-800 mb-1">{{ $details['details'] }}</div>
                                        @endif
                                        @foreach($details as $key => $value)
                                            @if($key !== 'details' && $key !== 'user_role')
                                                <div class="flex flex-wrap text-[13px] leading-relaxed text-left">
                                                    <span class="font-semibold text-gray-600 capitalize mr-1">
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
                            <td class="px-4 py-3 text-center text-gray-600 text-[13px] whitespace-nowrap">
                                <div>{{ $log->created_at->format('Y-m-d') }}</div>
                                <div class="text-gray-400 text-[12px]">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500 italic">
                                No audit logs found.
                            </td>
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

            <a href="{{ route('admin-home') }}" 
            class="button-default w-[220px] text-center">
                BACK TO DASHBOARD
            </a>
        </div>

</div>

@endsection

@push('scripts')
    @vite(['resources/js/audit-search.js'])
@endpush
