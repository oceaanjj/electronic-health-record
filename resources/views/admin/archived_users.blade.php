@extends('layouts.admin')

@section('content')
    <h2 class="text-dark-green font-alte mt-18 mb-8 text-center text-[50px] font-black uppercase">Archived Users</h2>

    <div class="mx-auto my-12 w-[100%] md:w-[90%] lg:w-[85%]">
        <div class="overflow-hidden rounded-[20px] border border-gray-100 bg-white shadow-2xl">
            <div class="main-header font-bold tracking-wider">
                <h2 class="py-1 text-center">ARCHIVED ACCOUNTS</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-gray-700">
                    <thead class="bg-gray-100 text-sm font-semibold text-gray-700 uppercase">
                        <tr>
                            <th class="border-b border-gray-200 px-6 py-3 text-center">USERNAME</th>
                            <th class="border-b border-gray-200 px-6 py-3 text-center">EMAIL</th>
                            <th class="border-b border-gray-200 px-6 py-3 text-center">ROLE</th>
                            <th class="border-b border-gray-200 px-6 py-3 text-center">DELETED AT</th>
                            <th class="border-b border-gray-200 px-6 py-3 text-center">ACTION</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 text-[15px]">
                        @forelse ($users as $user)
                            <tr class="transition duration-300 ease-in-out hover:bg-gray-50">
                                <td class="px-6 py-3 text-center font-semibold">{{ $user->username }}</td>
                                <td class="px-6 py-3 text-center">{{ $user->email }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="{{ strtolower($user->role) == 'doctor' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} inline-block rounded-full px-3 py-1 text-xs font-semibold">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center text-gray-500 italic">{{ $user->deleted_at->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-3 text-center">
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" class="restore-user-form inline-block">
                                        @csrf
                                        <button type="button" class="restore-btn cursor-pointer text-green-600 hover:text-green-800 transition-all duration-300 transform hover:scale-110" title="Restore User">
                                            <span class="material-symbols-outlined text-[28px]">restore_from_trash</span>
                                        </button>
                                    </form>
                                </td>
                                </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No archived users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-center gap-6 md:flex-row">
            <a href="{{ route('users') }}" class="button-default w-[230px] text-center">BACK TO USERS</a>
            <a href="{{ route('home') }}" class="button-default w-[230px] text-center">BACK TO DASHBOARD</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lucide icons initialization
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Restore User Confirmation
            const restoreButtons = document.querySelectorAll('.restore-btn');
            restoreButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('.restore-user-form');
                    
                    if (typeof window.showConfirm === 'function') {
                        window.showConfirm("This user will be able to log in again.", 'Restore User?', 'Yes, restore', 'Cancel').then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Restore User?',
                            text: "This user will be able to log in again.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#1a6a24',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, restore!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
