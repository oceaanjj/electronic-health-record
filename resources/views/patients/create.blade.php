@extends('layouts.app')

@section('title', 'Register Patient')

@section('content')
 

    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-200 dark:text-green-900" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif


    <div class="w-[100%] md:w-[90%] lg:w-[75%] xl:w-[65%] mx-auto my-12">

        <form action="{{ route('patients.store') }}" method="POST" novalidate>
            @csrf


            <h1 class="text-dark-green text-4xl font-extrabold mb-5 pb-1 tracking-tight">
                REGISTER PATIENT
            </h1>


            <div class="shadow-2xl rounded-xl overflow-hidden mb-10 border border-gray-100">


                <div class="main-header tracking-wider flex justify-between items-center pl-10">
                    <h1>PATIENT DETAILS</h1>
                </div>


                <div class="bg-white p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">


                    {{-- ** first name  --}}
                       <div class="col-span-6 md:col-span-2">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1">First Name <span
                                    class="text-red-500">*</span>
                            </label>
                            
                            <div class="relative js-error-container"> {{-- Added js-error-container --}}
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                    class="w-full text-base px-4 py-2 rounded-lg shadow-sm pr-10 js-error-field
                                    
                                    @error('first_name') 
                                        outline-none
                                        border-2 border-red-500 has-server-error 
                                        focus:ring-red-500 focus:border-red-500
                                    @else  
                                        {{-- ** para bumalik sa green kemerut ** --}}     
                                        outline-none border border-gray-300 
                                        focus:ring-green-200 focus:border-green-500 
                                    @enderror
                                    transition duration-150 ease-in-out"
                                placeholder="e.g. Juan">

                                @error('first_name')
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none js-error-icon"> {{-- Added js-error-icon --}}
                                        <span class="material-symbols-outlined text-red-500"> 
                                            error
                                        </span>
                                    </div>
                                @enderror
                            </div>
                            
                            {{-- Error Message Display --}}
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1 js-error-message"> {{-- Added js-error-message --}}
                                    
                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    {{-- ** end first name  --}}

                    

                    {{-- ** middle name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-1">Middle
                                Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm outline-none
                                focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out"
                                placeholder="Optional">
                        </div>
                    {{-- ** end middle name --}}

                         
                    
                    {{-- ** last name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span
                                    class="text-red-500">*</span>
                            </label>
                            
                            <div class="relative js-error-container"> {{-- Added js-error-container --}}
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                    class="w-full text-base px-4 py-2 rounded-lg shadow-sm pr-10 js-error-field
                                    
                                    @error('last_name') 
                                        outline-none
                                        border-2 border-red-500 has-server-error 
                                        focus:ring-red-500 focus:border-red-500
                                    @else  
                                        {{-- ** para bumalik sa green kemerut ** --}}     
                                        outline-none border border-gray-300 
                                        focus:ring-green-200 focus:border-green-500 
                                    @enderror
                                    transition duration-150 ease-in-out"
                                placeholder="e.g. Dela Cruz">

                                @error('last_name')
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none js-error-icon"> {{-- Added js-error-icon --}}
                                        <span class="material-symbols-outlined text-red-500 text-lg"> 
                                            error
                                        </span>
                                    </div>
                                @enderror
                            </div>
                            
                            {{-- Error Message Display --}}
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1 js-error-message"> {{-- Added js-error-message --}}
                                    
                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    {{-- ** end last name --}}


                            

                    {{-- ** birthdate --}} 
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthdate" class="block text-sm font-semibold text-gray-700 mb-1">Birthdate <span
                                    class="text-red-500">*</span>
                            </label>

                            <div class="relative js-error-container">
                                <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}"
                                    class="w-full text-base px-4 py-2 rounded-lg shadow-sm pr-5 js-error-field
                                    
                                    @error('birthdate') 
                                        outline-none
                                        border-2 border-red-500 has-server-error 
                                        focus:ring-red-500 focus:border-red-500
                                    @else  
                                        {{-- ** para bumalik sa green kemerut ** --}}     
                                        outline-none border border-gray-300 
                                        focus:ring-green-200 focus:border-green-500 
                                    @enderror
                                    transition duration-150 ease-in-out">   
                            </div>
                            
                            @error('birthdate')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1 js-error-message">
                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    {{-- ** end birthdate ** --}}

                    
                    {{-- ** age --}} 
                        <div class="col-span-6 md:col-span-2">
                            <label for="age" class="block text-sm font-semibold text-gray-700 mb-1">Age <span
                                    class="text-red-500">*</span>
                            </label>


                            <div class="relative js-error-container">
                                <input type="number" id="age" name="age" value="{{ old('age') }}"
                                    class="w-full text-base px-4 py-2 rounded-lg shadow-sm pr-5 js-error-field
                                        
                                        @error('birthdate') 
                                            outline-none
                                            border-2 border-red-500 has-server-error 
                                            focus:ring-red-500 focus:border-red-500
                                        @else  
                                            {{-- ** para bumalik sa green kemerut ** --}}     
                                            outline-none border border-gray-300 
                                            focus:ring-green-200 focus:border-green-500 
                                        @enderror
                                        transition duration-150 ease-in-out cursor-not-allowed" placeholder="Age" readonly disabled>
                            </div>
                        </div>
                    {{-- ** end age ** --}}



                    {{-- ** dropdown for sex ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label id="sex_label" for="sex_input" class="block text-sm font-semibold text-gray-700 mb-1">Sex <span
                                    class="text-red-500">*</span></label>
                            
                         
                            <input type="hidden" id="sex_input" name="sex" value="{{ old('sex') }}"> 

                       
                                <div class="relative custom-dropdown-container js-error-container"> 
                                    
                               
                                    <button type="button" 
                                        class="custom-dropdown-button w-full text-base px-4 py-2 rounded-lg shadow-sm pr-10 js-error-field text-left flex justify-between items-center transition duration-150 ease-in-out 
                                                
                                                @error('sex') 
                                                    outline-none
                                                    border-2 border-red-500 has-server-error 
                                                    focus:ring-red-500 focus:border-red-500
                                                @else  
                                                    {{-- ** para bumalik sa green kemerut ** --}}     
                                                    outline-none border border-gray-300 
                                                    focus:ring-green-200 focus:border-green-500 
                                                    {{ old('sex') ? 'text-gray-700' : 'text-gray-400' }}
                                                @enderror
                                                transition duration-150 ease-in-out" data-value="{{ old('sex') }}">   
                                                                

                                            <span class="dropdown-selected-text text-gray-400">
                                                {{ old('sex') ?: 'Select Sex' }}
                                            </span>
            
                                        <span class="material-symbols-outlined text-gray-400 text-lg dropdown-arrow"> 
                                            arrow_drop_down
                                        </span>
                                    </button>

                                @error('sex')
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none js-error-icon">
                                        <span class="material-symbols-outlined text-red-500 text-lg"> 
                                                error
                                        </span>
                                    </div>
                                @enderror
                                
                              
                                <div class="custom-dropdown-menu hidden absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200">
                                    <ul class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                        <li class="px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer" data-value="Female">Female</li>
                                        <li class="px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer" data-value="Male">Male</li>
                                    </ul>
                                </div>
                            </div>

                             @error('sex')
                                <p class="mt-1 text-xs text-red-600 flex items-center gap-1 js-error-message">
                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    {{-- **end of sex dropdown** --}}



                    {{-- ** address ** --}}
                        <div class="col-span-6">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out"
                                placeholder="Street, City, Province/State, Country">
                        </div>
                    {{-- ** end address ** --}}


                    {{-- ** birthplace ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthplace" class="block text-sm font-semibold text-gray-700 mb-1">Birth
                                Place</label>
                            <input type="text" id="birthplace" name="birthplace" value="{{ old('birthplace') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out"
                                placeholder="City/Municipality">
                        </div>
                    {{-- ** end birthplace ** --}}
                    

                    {{-- ** dropdown for religion ** --}}
                        <div class="col-span-6 md:col-span-2">
                                <label id="religion_label" for="religion_input" class="block text-sm font-semibold text-gray-700 mb-1">Religion</label>
                                
                                <input type="hidden" id="religion_input" name="religion" value="{{ old('religion') }}"> 

                                <div class="relative custom-dropdown-container js-error-container"> 
                                    <button type="button" 
                                        class="custom-dropdown-button w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out 
                                            text-left flex justify-between items-center text-gray-300" 
                                        data-value="{{ old('religion') }}"
                                    >
                                        <span class="dropdown-selected-text">{{ old('religion') ?: 'Select Religion' }}</span>
                                        <span class="material-symbols-outlined text-gray-400 text-lg dropdown-arrow"> 
                                            arrow_drop_down
                                        </span>
                                    </button>
                                    
                                    <div class="custom-dropdown-menu hidden absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200 max-h-52 overflow-y-auto">
                                        <ul class="py-1" role="menu">
                                            @php
                                                $religions = ['Roman Catholic', 'Islam', 'Iglesia ni Cristo', 'Protestant', 'Born Again', 'Buddhist', 'Hindu', 'Other'];
                                            @endphp
                                            @foreach ($religions as $r)
                                                <li class="px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer" data-value="{{ $r }}">{{ $r }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        {{-- ** end of religion dropdown ** --}}


                        {{-- ** dropdown for ethnicity ** --}}
                            <div class="col-span-6 md:col-span-2">
                                <label id="ethnicity_label" for="ethnicity_input" class="block text-sm font-semibold text-gray-700 mb-1">Ethnicity</label>
                                
                                <input type="hidden" id="ethnicity_input" name="ethnicity" value="{{ old('ethnicity') }}"> 

                                <div class="relative custom-dropdown-container js-error-container"> 
                                    <button type="button" 
                                        class="custom-dropdown-button w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out 
                                            text-left flex justify-between items-center text-gray-300" 
                                        data-value="{{ old('ethnicity') }}"
                                    >
                                        <span class="dropdown-selected-text">{{ old('ethnicity') ?: 'Select Ethnicity' }}</span>
                                        <span class="material-symbols-outlined text-gray-400 text-lg dropdown-arrow"> 
                                            arrow_drop_down
                                        </span>
                                    </button>
                                    
                                    <div class="custom-dropdown-menu hidden absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-200 max-h-52 overflow-y-auto">
                                        <ul class="py-1" role="menu">
                                            @php
                                                $ethnicities = ['Tagalog', 'Cebuano', 'Ilocano', 'Bisaya', 'Bicolano', 'Kapampangan', 'Ibanag', 'Other'];
                                            @endphp
                                            @foreach ($ethnicities as $e)
                                                <li class="px-4 py-2 text-gray-700 hover:bg-gray-100 cursor-pointer" data-value="{{ $e }}">{{ $e }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        {{-- ** end of ethnicity dropdown ** --}}


                        {{-- ** chief complaints ** --}}
                            <div class="col-span-6">
                                <label for="chief_complaints" class="block text-sm font-semibold text-gray-700 mb-1">Chief of
                                    Complaints</label>
                                <textarea id="chief_complaints" name="chief_complaints" rows="4"
                                    class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out resize-none"
                                    placeholder="Describe the patient's primary symptoms or issues.">{{ old('chief_complaints') }}</textarea>
                            </div>
                        {{-- ** end chief complaints ** --}}


                        {{-- ** admission date ** --}}
                            <div class="col-span-6 md:col-span-2">
                                <label for="admission_date" class="block text-sm font-semibold text-gray-700 mb-1">Admission
                                    Date</label>
                                <input type="date" id="admission_date" name="admission_date" value="{{ $currentDate }}"
                                    class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 cursor-not-allowed
                                    outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out" readonly>
                            </div>
                        {{-- ** end admission date ** --}}


                        {{-- ** room number ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="room_no" class="block text-sm font-semibold text-gray-700 mb-1">Room No.</label>
                            <input type="text" id="room_no" name="room_no" value="{{ old('room_no') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm outline-none
                                focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out "
                                placeholder="Enter room number">
                        </div>
                        {{-- ** end room number ** --}}


                        {{-- ** bed number ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="bed_no" class="block text-sm font-semibold text-gray-700 mb-1">Bed No.</label>
                            <input type="text" id="bed_no" name="bed_no" value="{{ old('bed_no') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm outline-none
                                focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out "
                                placeholder="Enter bed number">
                        </div>
                        {{-- ** end bed number ** --}}

                    </div>
                </div>
            </div>


            <div class="shadow-2xl rounded-xl overflow-hidden mb-10 border border-gray-100">
                <div class="main-header text-white pr-4 pl-10 tracking-wider flex justify-between items-center">
                    <h1>EMERGENCY CONTACT</h1>
                    <button type="button" id="add-contact" 
                            class="px-3 py-1 rounded-md font-bold text-white hover:text-yellow-400 cursor-pointer flex items-center gap-1 transition duration-150">

                        <span class="material-symbols-outlined text-base">
                            note_stack_add
                        </span>
                    </button>
                </div>

                <div class="bg-white p-6 sm:p-8 space-y-4" id="contact-container">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 contact-entry animate-fadeIn">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                        <input type="text" name="contact_name[]"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm outline-none focus:ring-green-200 focus:border-green-500"
                            placeholder="Full Name">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
                        <input type="text" name="contact_relationship[]"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out"
                            placeholder="e.g. Parent">
                    </div>


                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Contact Number</label>
                            
                            <div class="flex-1"> 
                                <input type="text" name="contact_number[]"
                                    class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                        outline-none focus:ring-green-200 focus:border-green-500 transition duration-150 ease-in-out placeholder-gray-400"
                                    placeholder="e.g. 0912-345-6789" autocomplete="tel">
                            </div>
                        </div>
                        
                        <button type="button"
                            class="remove-contact hidden text-red-600 font-bold hover:text-red-800 p-2 leading-none">
                            <span class="remove-contact material-symbols-outlined text-lg">
                                cancel
                            </span>
                        </button>
                    </div>

                </div>
        </div>
</div>



            <div class="flex justify-end items-center mt-10 space-x-4">
                <button type="button" onclick="window.history.back()" class="button-default">BACK</button>
                <button type="submit" class="button-default">SAVE</button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
        @vite(['resources/js/compute-age.js'])

        <script>
            document.getElementById('add-contact').addEventListener('click', function() {
                const container = document.getElementById('contact-container');
                const entry = container.querySelector('.contact-entry'); 
                const clone = entry.cloneNode(true);

                clone.querySelectorAll('input').forEach(input => input.value = '');
                clone.querySelector('.remove-contact').classList.remove('hidden'); 
                container.appendChild(clone);
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-contact')) {
                    e.target.closest('.contact-entry').remove();
                }
            });
            
            // --- dropdown handling ( customize ) ---
            document.addEventListener('DOMContentLoaded', function() {
                
                const errorFields = document.querySelectorAll('.js-error-field.has-server-error');

                const ERROR_BORDER_CLASSES = ['border-2', 'border-red-500', 'focus:ring-red-500', 'focus:border-red-500', 'has-server-error'];
                const DEFAULT_BORDER_CLASSES = ['border', 'border-gray-300', 'focus:ring-green-200', 'focus:border-green-500'];
                
                const iconTemplate = `
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none js-error-icon">
                        <span class="material-symbols-outlined text-red-500 text-lg">
                            error
                        </span>
                    </div>
                `;

                const messageTemplate = (message) => `
                    <p class="mt-1 text-xs text-red-600 flex items-center gap-1 js-error-message">
                        ${message}
                    </p>
                `;

                function updateErrorState(input, isError) {
                    const container = input.closest('.col-span-6');
                    const relativeWrapper = input.closest('.js-error-container');
                    let errorMessage = container.querySelector('.js-error-message');
                    let errorIcon = relativeWrapper ? relativeWrapper.querySelector('.js-error-icon') : null;

                    if (errorMessage) errorMessage.remove();
                    if (errorIcon) errorIcon.remove();

                    input.classList.remove(...ERROR_BORDER_CLASSES, ...DEFAULT_BORDER_CLASSES);

                    if (isError) {
                        input.classList.add(...ERROR_BORDER_CLASSES);
                        if (relativeWrapper) {
                            relativeWrapper.insertAdjacentHTML('beforeend', iconTemplate);
                        }
                        container.insertAdjacentHTML('beforeend', messageTemplate("This field is required."));
                    } else {
                        input.classList.add(...DEFAULT_BORDER_CLASSES);
                    }
                }

                errorFields.forEach(input => {
                    input.addEventListener('input', function() {
                        const fieldIsEmpty = this.value.trim() === '';
                        updateErrorState(this, fieldIsEmpty);
                    });
                    if (input.value.trim() !== '') {
                        updateErrorState(input, false);
                    }
                });

                
                function toggleDropdown(button) {
                    const container = button.closest('.custom-dropdown-container');
                    const menu = container.querySelector('.custom-dropdown-menu');
                    const arrow = container.querySelector('.dropdown-arrow'); 
                    
                    // Close all other open dropdowns and reset their arrows
                    document.querySelectorAll('.custom-dropdown-container').forEach(otherContainer => {
                        const otherMenu = otherContainer.querySelector('.custom-dropdown-menu');
                        const otherArrow = otherContainer.querySelector('.dropdown-arrow');
                        
                        if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                            otherMenu.classList.add('hidden');
                            if (otherArrow) otherArrow.textContent = 'arrow_drop_down'; 
                        }
                    });

                    // Toggle the current dropdown menu
                    menu.classList.toggle('hidden');
                    
                    // Toggle the current dropdown arrow based on the new menu state
                    if (menu.classList.contains('hidden')) {
                        arrow.textContent = 'arrow_drop_down';
                    } else {
                        arrow.textContent = 'arrow_drop_up';
                    }
                }
                
                document.querySelectorAll('.custom-dropdown-container').forEach(container => {
                    const button = container.querySelector('.custom-dropdown-button');
                    const menu = container.querySelector('.custom-dropdown-menu');
                    const hiddenInput = container.closest('.col-span-6').querySelector('input[type="hidden"]');
                    const selectedTextSpan = container.querySelector('.dropdown-selected-text');

                    // Opens/Closes the menu
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        toggleDropdown(this);
                    });

                    // Selects the value and closes the menu
                    menu.querySelectorAll('li').forEach(option => {
                        option.addEventListener('click', function() {
                            const selectedValue = this.getAttribute('data-value');
                            const selectedText = this.textContent.trim();
                            const arrow = container.querySelector('.dropdown-arrow');

                            hiddenInput.value = selectedValue;
                            selectedTextSpan.textContent = selectedText;
                            
                            // Remove gray placeholder color and add dark text color
                            selectedTextSpan.classList.remove('text-gray-500'); 
                            selectedTextSpan.classList.add('text-gray-900'); 
                            
                            // Close the menu and reset arrow
                            menu.classList.add('hidden');
                            arrow.textContent = 'arrow_drop_down';

                            updateErrorState(button, false);
                        });
                    });

                    // Closes the menu and resets arrow
                    document.addEventListener('click', function(e) {
                        const menu = container.querySelector('.custom-dropdown-menu');
                        const arrow = container.querySelector('.dropdown-arrow');
                        
                        if (!container.contains(e.target) && !menu.classList.contains('hidden')) {
                            menu.classList.add('hidden');
                            arrow.textContent = 'arrow_drop_down';
                        }
                    });
                    
                    // Set initial state if old() value exists on page load
                    if (hiddenInput.value) {
                        // Find the correct displayed text, or default to value
                        let initialText = hiddenInput.value;
                        menu.querySelectorAll('li').forEach(option => {
                            if (option.getAttribute('data-value') === hiddenInput.value) {
                                initialText = option.textContent.trim();
                            }
                        });

                        selectedTextSpan.textContent = initialText;
                        selectedTextSpan.classList.remove('text-gray-400'); // Assuming text-gray-400 is placeholder
                        selectedTextSpan.classList.add('text-gray-700'); // Apply dark color on load
                        
                        // On page load, also ensure that if a value is present, the error state is cleared 
                        // from the button, in case the error was for a different field.
                        updateErrorState(button, false); 
                    }
                });
                
            });
        </script>
@endpush
