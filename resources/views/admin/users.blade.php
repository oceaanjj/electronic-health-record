@extends('layouts.admin')

@section('content')
    <h2 class="text-dark-green font-alte mt-18 mb-8 text-center text-[50px] font-black">USER MANAGEMENT</h2>

    <div class="mx-auto my-12 w-[100%] md:w-[90%] lg:w-[85%]">
        <div class="overflow-hidden rounded-[20px] border border-gray-100 bg-white shadow-2xl">
            <div class="main-header font-bold tracking-wider">
                <h2 class="py-1 text-center">REGISTERED USERS</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-gray-700">
                    <thead class="bg-gray-100 text-sm font-semibold text-gray-700 uppercase">
                        <tr>
                            <th class="w-[25%] border-b border-gray-200 px-6 py-3 text-center">USERNAME</th>
                            <th class="w-[25%] border-b border-gray-200 px-6 py-3 text-center">EMAIL</th>
                            <th class="w-[20%] border-b border-gray-200 px-6 py-3 text-center">ROLE</th>
                            <th class="w-[30%] border-b border-gray-200 px-6 py-3 text-center">CHANGE ROLE</th>
                            <th class="w-[10%] border-b border-gray-200 px-6 py-3 text-center">ACTION</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 text-[15px]">
                        @foreach ($users as $user)
                            <tr class="transition duration-300 ease-in-out hover:bg-gray-50">
                                <td class="px-6 py-3 text-center font-semibold">{{ $user->username }}</td>
                                <td class="px-6 py-3 text-center">{{ $user->email }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span
                                        class="{{ strtolower($user->role) == 'doctor' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} inline-block rounded-full px-3 py-1 text-xs font-semibold"
                                    >
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <form
                                        method="POST"
                                        action="{{ route('users.role.update', $user->id) }}"
                                        class="role-form flex items-center justify-center gap-3"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <select
                                            name="role"
                                            data-original-role="{{ $user->role }}"
                                            class="role-select focus:ring-dark-green focus:border-dark-green rounded-lg border border-gray-300 px-3 py-1 text-sm outline-none focus:ring-2"
                                        >
                                            <option value="Doctor" {{ $user->role == 'Doctor' ? 'selected' : '' }}>
                                                Doctor
                                            </option>
                                            <option value="Nurse" {{ $user->role == 'Nurse' ? 'selected' : '' }}>
                                                Nurse
                                            </option>
                                        </select>
                                        <button
                                            type="submit"
                                            disabled
                                            class="update-btn cursor-not-allowed rounded-lg bg-gray-400 px-4 py-1.5 font-semibold text-white transition-all duration-300"
                                        >
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <a href="{{ route('users.edit', $user->id) }}" class="text-blue-500 hover:text-blue-700 transition-all duration-300 transform hover:scale-110" title="Edit User">
                                            <span class="material-symbols-outlined text-[28px]">edit</span>
                                        </a>

                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-user-form inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="delete-btn cursor-pointer text-red-500 hover:text-red-700 transition-all duration-300 transform hover:scale-110" title="Archive User">
                                                <span class="material-symbols-outlined pointer-events-none text-[28px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-center gap-6 md:flex-row">
            <a href="{{ route('register') }}" class="button-default w-[230px] text-center">REGISTER NEW USER</a>
            <a href="{{ route('home') }}" class="button-default w-[230px] text-center">BACK TO DASHBOARD</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Lucide icons initialization
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            const forms = document.querySelectorAll('.role-form');

            forms.forEach((form) => {
                const select = form.querySelector('.role-select');
                const button = form.querySelector('.update-btn');
                const original = select.dataset.originalRole;

                select.addEventListener('change', () => {
                    if (select.value === original) {
                        button.disabled = true;
                        button.classList.remove('bg-dark-green', 'hover:bg-green-800', 'cursor-pointer');
                        button.classList.add('bg-gray-400', 'cursor-not-allowed');
                    } else {
                        button.disabled = false;
                        button.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        button.classList.add('bg-dark-green', 'hover:bg-green-800', 'cursor-pointer');
                    }
                });
            });

            // Delete User Confirmation
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.onclick = function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    
                    if (typeof window.showConfirm === 'function') {
                        window.showConfirm("This user will be archived and won't be able to log in.", 'Are you sure?', 'Yes, archive', 'Cancel').then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This user will be archived and won't be able to log in.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#1a6a24',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, archive!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                };
            });
        });
    </script>
@endsection
