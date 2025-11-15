@extends('layouts.admin')

@section('content')

<h2 class="text-[50px] font-black mb-8 text-dark-green mt-18 text-center font-alte">USER MANAGEMENT</h2>

<div class="w-[100%] md:w-[90%] lg:w-[85%] mx-auto my-12">


    <div class="shadow-2xl rounded-[20px] overflow-hidden border border-gray-100 bg-white">
        
   
        <div class="main-header font-bold tracking-wider">
            <h2 class="text-center py-1">REGISTERED USERS</h2>
        </div>

 
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse text-gray-700">
                <thead class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3 text-center border-b border-gray-200 w-[25%]">USERNAME</th>
                        <th class="px-6 py-3 text-center border-b border-gray-200 w-[25%]">EMAIL</th>
                        <th class="px-6 py-3 text-center border-b border-gray-200 w-[20%]">ROLE</th>
                        <th class="px-6 py-3 text-center border-b border-gray-200 w-[30%]">CHANGE ROLE</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 text-[15px]">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-300 ease-in-out">
                            <td class="px-6 py-3 text-center font-semibold">{{ $user->username }}</td>
                            <td class="px-6 py-3 text-center">{{ $user->email }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                    {{ strtolower($user->role) == 'doctor' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <form method="POST" action="{{ route('users.role.update', $user->id) }}" 
                                      class="role-form flex justify-center items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" 
                                            data-original-role="{{ $user->role }}" 
                                            class="role-select px-3 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green text-sm outline-none">
                                        <option value="Doctor" {{ $user->role == 'Doctor' ? 'selected' : '' }}>Doctor</option>
                                        <option value="Nurse" {{ $user->role == 'Nurse' ? 'selected' : '' }}>Nurse</option>
                                    </select>
                                    <button type="submit" 
                                        disabled
                                        class="update-btn bg-gray-400 text-white font-semibold px-4 py-1.5 rounded-lg transition-all duration-300 cursor-not-allowed">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <div class="mt-12 flex flex-col md:flex-row justify-center items-center gap-6">
        <a href="{{ route('register') }}" 
           class="button-default w-[230px] text-center">
           REGISTER NEW USER
        </a>

        <a href="{{ route('home') }}" 
           class="button-default w-[230px] text-center">
           BACK TO DASHBOARD
        </a>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.role-form');

        forms.forEach(form => {
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
    });
</script>

@endsection
