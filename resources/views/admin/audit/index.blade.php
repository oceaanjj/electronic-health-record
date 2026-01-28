@vite(['resources/css/app.css', 'resources/js/app.js'])

@extends('layouts.admin')

@section('content')

{{-- Responsive Title Size --}}
<h2 class="text-[32px] md:text-[50px] font-black mb-8 text-dark-green mt-8 md:mt-18 text-center font-alte">
    AUDIT LOGS
</h2>

<div class="w-full md:w-[90%] lg:w-[85%] mx-auto my-12 px-4 md:px-0">

    {{-- 
       SEARCH & FILTER BAR 
       - Mobile/Tablet: Stacked (flex-col)
       - Laptop/Desktop: Row (lg:flex-row)
    --}}
    <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-10">

        {{-- Search Input Wrapper --}}
        <div class="relative w-full lg:w-auto lg:ml-10">
            <input 
                type="text" 
                id="audit-search" 
                placeholder="Search by username..."
                value="{{ request('username_search') }}"
                class="w-full lg:w-[300px] px-5 py-2 rounded-full border border-gray-300 
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

        {{-- Sort Buttons --}}
        <div class="flex flex-wrap justify-center lg:justify-end items-center gap-3 text-gray-700 w-full lg:w-auto lg:mr-10">
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


    {{-- TABLE CONTAINER --}}
    <div class="shadow-2xl rounded-[20px] overflow-hidden border border-gray-100">
        
       <div class="main-header font-bold tracking-wider py-4 bg-white"> {{-- Added bg-white to ensure header background --}}
            <h2 class="text-center text-xl">AUDIT LOG ENTRIES</h2>
        </div>

        <div class="bg-white overflow-x-auto">
            {{-- md:whitespace-normal allows wrapping on desktop, whitespace-nowrap forces scroll on mobile --}}
            <table class="min-w-full border-collapse whitespace-nowrap md:whitespace-normal">
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
                            
                            {{-- ID --}}
                            <td class="px-4 py-3 text-center font-extrabold text-[15px] text-dark-green">
                                {{ $log->id }}
                            </td>
                            
                            {{-- User --}}
                            <td class="px-4 py-3 text-left font-semibold text-gray-700">
                                {{ $log->user->username ?? 'System' }}
                            </td>
                            
                            {{-- Action Badge --}}
                            <td class="px-4 py-3 text-left">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                    {{ $log->action == 'Deleted' ? 'bg-red-100 text-red-800' : 
                                      ($log->action == 'Updated' ? 'bg-yellow-100 text-yellow-800' : 
                                      'bg-green-100 text-green-800') }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Details --}}
                            {{-- min-w ensures this column doesn't collapse on mobile --}}
                            <td class="px-4 py-3 text-gray-700 text-[13px] min-w-[250px] md:min-w-0">
                                @if ($details)
                                    @if (is_array($details))
                                        @if(isset($details['details']))
                                            <div class="text-gray-800 mb-1 font-medium">{{ $details['details'] }}</div>
                                        @endif
                                        <div class="space-y-1">
                                            @foreach($details as $key => $value)
                                                @if($key !== 'details' && $key !== 'user_role')
                                                    <div class="flex flex-wrap text-[13px] leading-relaxed text-left">
                                                        <span class="font-semibold text-gray-600 capitalize mr-1">
                                                            {{ str_replace('_', ' ', $key) }}:
                                                        </span>
                                                        <span class="text-gray-800 break-all">
                                                            {{ is_array($value) ? json_encode($value) : $value }}
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-800">{{ $log->details }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">No details provided.</span>
                                @endif
                            </td>
                            
                            {{-- Date & Time --}}
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


    {{-- BOTTOM ACTIONS --}}
    <div class="mt-12 flex flex-col items-center pb-20 w-full">
        
        {{-- Pagination Wrapper --}}
        <div class="flex flex-col items-center justify-center w-full mb-8 custom-pagination">
            {{ $logs->links('pagination::tailwind') }}
        </div>

        
      {{-- Back Button --}}
            <a href="{{ route('admin-home') }}" 
            class="button-default w-[220px] text-center">
                BACK TO DASHBOARD
            </a>
    </div>

</div>

@endsection

@push('styles')
    @vite(['resources/css/admin.css'])
    
    {{-- CSS Fix for Pagination Spacing --}}
    <style>
        /* Forces space between "Showing X to Y" and the buttons */
        .custom-pagination nav {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }
        
        @media (min-width: 300px) {
            .custom-pagination nav {
                flex-direction: row;
                justify-content: space-between;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    @vite(['resources/js/audit-search.js'])
@endpush