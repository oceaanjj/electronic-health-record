@extends("layouts.app")

@section("title", "Register Patient")

@section("content")
    @if (session("success"))
        <div
            class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-800 dark:bg-green-200 dark:text-green-900"
            role="alert"
        >
            <span class="font-medium">Success!</span>
            {{ session("success") }}
        </div>
    @endif

    <div class="mx-auto my-12 w-[100%] md:w-[90%] lg:w-[75%] xl:w-[65%]">
        <form action="{{ route("patients.store") }}" method="POST" novalidate>
            @csrf

            <h1 class="text-dark-green mb-5 pb-1 text-4xl font-extrabold tracking-tight">REGISTER PATIENT</h1>

            <div class="mb-10 overflow-hidden rounded-xl border border-gray-100 shadow-2xl">
                <div class="main-header flex items-center justify-between pl-10 tracking-wider">
                    <h1>PATIENT DETAILS</h1>
                </div>

                <div class="bg-white p-6 sm:p-8">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-6">
                        {{-- ** first name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="first_name" class="mb-1 block text-sm font-semibold text-gray-700">
                                First Name
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="js-error-container relative">
                                {{-- Added js-error-container --}}
                                <input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    value="{{ old("first_name") }}"
                                    class="js-error-field @error("first_name")
                                        outline-none
                                        border-2
                                        border-red-500
                                        has-server-error
                                        focus:ring-red-500
                                        focus:border-red-500
                                    @else
                                        {{-- ** para bumalik sa green kemerut ** --}}
                                        outline-none
                                        border
                                        border-gray-300
                                        focus:ring-green-200
                                        focus:border-green-500
                                    @enderror w-full rounded-lg px-4 py-2 pr-10 text-base shadow-sm transition duration-150 ease-in-out"
                                    placeholder="e.g. Juan"
                                />

                                @error("first_name")
                                    <div
                                        class="js-error-icon pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                                    >
                                        {{-- Added js-error-icon --}}
                                        <span class="material-symbols-outlined text-red-500">error</span>
                                    </div>
                                @enderror
                            </div>

                            {{-- Error Message Display --}}
                            @error("first_name")
                                <p class="js-error-message mt-1 flex items-center gap-1 text-xs text-red-600">
                                    {{-- Added js-error-message --}}

                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        {{-- ** end first name --}}

                        {{-- ** middle name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="middle_name" class="mb-1 block text-sm font-semibold text-gray-700">
                                Middle Name
                            </label>
                            <input
                                type="text"
                                id="middle_name"
                                name="middle_name"
                                value="{{ old("middle_name") }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-base shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="Optional"
                            />
                        </div>
                        {{-- ** end middle name --}}

                        {{-- ** last name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="last_name" class="mb-1 block text-sm font-semibold text-gray-700">
                                Last Name
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="js-error-container relative">
                                {{-- Added js-error-container --}}
                                <input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    value="{{ old("last_name") }}"
                                    class="js-error-field @error("last_name")
                                        outline-none
                                        border-2
                                        border-red-500
                                        has-server-error
                                        focus:ring-red-500
                                        focus:border-red-500
                                    @else
                                        {{-- ** para bumalik sa green kemerut ** --}}
                                        outline-none
                                        border
                                        border-gray-300
                                        focus:ring-green-200
                                        focus:border-green-500
                                    @enderror w-full rounded-lg px-4 py-2 pr-10 text-base shadow-sm transition duration-150 ease-in-out"
                                    placeholder="e.g. Dela Cruz"
                                />

                                @error("last_name")
                                    <div
                                        class="js-error-icon pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                                    >
                                        {{-- Added js-error-icon --}}
                                        <span class="material-symbols-outlined text-lg text-red-500">error</span>
                                    </div>
                                @enderror
                            </div>

                            {{-- Error Message Display --}}
                            @error("last_name")
                                <p class="js-error-message mt-1 flex items-center gap-1 text-xs text-red-600">
                                    {{-- Added js-error-message --}}

                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        {{-- ** end last name --}}

                        {{-- ** birthdate --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthdate" class="mb-1 block text-sm font-semibold text-gray-700">
                                Birthdate
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="js-error-container relative">
                                <input
                                    type="date"
                                    id="birthdate"
                                    name="birthdate"
                                    value="{{ old("birthdate") }}"
                                    class="js-error-field @error("birthdate")
                                        outline-none
                                        border-2
                                        border-red-500
                                        has-server-error
                                        focus:ring-red-500
                                        focus:border-red-500
                                    @else
                                        {{-- ** para bumalik sa green kemerut ** --}}
                                        outline-none
                                        border
                                        border-gray-300
                                        focus:ring-green-200
                                        focus:border-green-500
                                    @enderror w-full rounded-lg px-4 py-2 pr-5 text-base shadow-sm transition duration-150 ease-in-out"
                                />
                            </div>

                            @error("birthdate")
                                <p class="js-error-message mt-1 flex items-center gap-1 text-xs text-red-600">
                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        {{-- ** end birthdate ** --}}

                        {{-- ** age --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="age" class="mb-1 block text-sm font-semibold text-gray-700">
                                Age
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="js-error-container relative">
                                <input
                                    type="number"
                                    id="age"
                                    name="age"
                                    value="{{ old("age") }}"
                                    class="js-error-field w-full cursor-not-allowed rounded-lg border border-gray-300 px-4 py-2 pr-5 text-base shadow-sm transition duration-150 ease-in-out outline-none"
                                    placeholder="Age"
                                    readonly
                                />
                            </div>

                            <p class="js-error-message mt-1 flex items-center gap-1 text-xs text-gray-400">
                                {{-- Added js-error-message --}}
                                Please select a birthdate to compute age.
                            </p>
                        </div>
                        {{-- ** end age ** --}}

                        {{-- ** dropdown for sex ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label
                                id="sex_label"
                                for="sex_input"
                                class="mb-1 block text-sm font-semibold text-gray-700"
                            >
                                Sex
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="hidden" id="sex_input" name="sex" value="{{ old("sex") }}" />

                            <div class="custom-dropdown-container js-error-container relative">
                                <button
                                    type="button"
                                    class="custom-dropdown-button js-error-field @error("sex")
                                        outline-none
                                        border-2
                                        border-red-500
                                        has-server-error
                                        focus:ring-red-500
                                        focus:border-red-500
                                    @else
                                        {{-- ** para bumalik sa green kemerut ** --}}
                                        outline-none
                                        border
                                        border-gray-300
                                        focus:ring-green-200
                                        focus:border-green-500
                                        {{ old("sex") ? "text-gray-700" : "text-gray-400" }}
                                    @enderror flex w-full items-center justify-between rounded-lg px-4 py-2 pr-10 text-left text-base shadow-sm transition duration-150 ease-in-out"
                                    data-value="{{ old("sex") }}"
                                >
                                    <span class="dropdown-selected-text text-gray-400">
                                        {{ old("sex") ?: "Select Sex" }}
                                    </span>

                                    <span class="material-symbols-outlined dropdown-arrow text-lg text-gray-400">
                                        arrow_drop_down
                                    </span>
                                </button>

                                @error("sex")
                                    <div
                                        class="js-error-icon pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                                    >
                                        <span class="material-symbols-outlined text-lg text-red-500">error</span>
                                    </div>
                                @enderror

                                <div
                                    class="custom-dropdown-menu absolute z-10 mt-1 hidden w-full rounded-xl border border-gray-200 bg-white shadow-lg"
                                >
                                    <ul
                                        class="py-1"
                                        role="menu"
                                        aria-orientation="vertical"
                                        aria-labelledby="options-menu"
                                    >
                                        <li
                                            class="cursor-pointer px-4 py-2 text-gray-700 hover:bg-gray-100"
                                            data-value="Female"
                                        >
                                            Female
                                        </li>
                                        <li
                                            class="cursor-pointer px-4 py-2 text-gray-700 hover:bg-gray-100"
                                            data-value="Male"
                                        >
                                            Male
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            @error("sex")
                                <p class="js-error-message mt-1 flex items-center gap-1 text-xs text-red-600">
                                    <span data-original-error="{{ $message }}">{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        {{-- **end of sex dropdown** --}}

                        {{-- ** address ** --}}
                        <div class="col-span-6">
                            <label for="address" class="mb-1 block text-sm font-semibold text-gray-700">Address</label>
                            <input
                                type="text"
                                id="address"
                                name="address"
                                value="{{ old("address") }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-base shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="Street, City, Province/State, Country"
                            />
                        </div>
                        {{-- ** end address ** --}}

                        {{-- ** birthplace ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthplace" class="mb-1 block text-sm font-semibold text-gray-700">
                                Birth Place
                            </label>
                            <input
                                type="text"
                                id="birthplace"
                                name="birthplace"
                                value="{{ old("birthplace") }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-base shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="City/Municipality"
                            />
                        </div>
                        {{-- ** end birthplace ** --}}

                        {{-- ** dropdown for religion ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label
                                id="religion_label"
                                for="religion_input"
                                class="mb-1 block text-sm font-semibold text-gray-700"
                            >
                                Religion
                            </label>

                            <input type="hidden" id="religion_input" name="religion" value="{{ old("religion") }}" />

                            <div class="custom-dropdown-container js-error-container relative">
                                <button
                                    type="button"
                                    class="custom-dropdown-button flex w-full items-center justify-between rounded-lg border border-gray-300 px-4 py-2 text-left text-base text-gray-300 shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                    data-value="{{ old("religion") }}"
                                >
                                    <span class="dropdown-selected-text">
                                        {{ old("religion") ?: "Select Religion" }}
                                    </span>
                                    <span class="material-symbols-outlined dropdown-arrow text-lg text-gray-400">
                                        arrow_drop_down
                                    </span>
                                </button>

                                <div
                                    class="custom-dropdown-menu absolute z-10 mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg"
                                >
                                    <ul class="py-1" role="menu">
                                        @php
                                            $religions = ["Roman Catholic", "Islam", "Iglesia ni Cristo", "Protestant", "Born Again", "Buddhist", "Hindu", "Other"];
                                        @endphp

                                        @foreach ($religions as $r)
                                            <li
                                                class="cursor-pointer px-4 py-2 text-gray-700 hover:bg-gray-100"
                                                data-value="{{ $r }}"
                                            >
                                                {{ $r }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- ** end of religion dropdown ** --}}

                        {{-- ** dropdown for ethnicity ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label
                                id="ethnicity_label"
                                for="ethnicity_input"
                                class="mb-1 block text-sm font-semibold text-gray-700"
                            >
                                Ethnicity
                            </label>

                            <input
                                type="hidden"
                                id="ethnicity_input"
                                name="ethnicity"
                                value="{{ old("ethnicity") }}"
                            />

                            <div class="custom-dropdown-container js-error-container relative">
                                <button
                                    type="button"
                                    class="custom-dropdown-button flex w-full items-center justify-between rounded-lg border border-gray-300 px-4 py-2 text-left text-base text-gray-300 shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                    data-value="{{ old("ethnicity") }}"
                                >
                                    <span class="dropdown-selected-text">
                                        {{ old("ethnicity") ?: "Select Ethnicity" }}
                                    </span>
                                    <span class="material-symbols-outlined dropdown-arrow text-lg text-gray-400">
                                        arrow_drop_down
                                    </span>
                                </button>

                                <div
                                    class="custom-dropdown-menu absolute z-10 mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg"
                                >
                                    <ul class="py-1" role="menu">
                                        @php
                                            $ethnicities = ["Tagalog", "Cebuano", "Ilocano", "Bisaya", "Bicolano", "Kapampangan", "Ibanag", "Other"];
                                        @endphp

                                        @foreach ($ethnicities as $e)
                                            <li
                                                class="cursor-pointer px-4 py-2 text-gray-700 hover:bg-gray-100"
                                                data-value="{{ $e }}"
                                            >
                                                {{ $e }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- ** end of ethnicity dropdown ** --}}

                        {{-- ** chief complaints ** --}}
                        <div class="col-span-6">
                            <label for="chief_complaints" class="mb-1 block text-sm font-semibold text-gray-700">
                                Chief of Complaints
                            </label>
                            <textarea
                                id="chief_complaints"
                                name="chief_complaints"
                                rows="4"
                                class="w-full resize-none rounded-lg border border-gray-300 px-4 py-2 text-base shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="Describe the patient's primary symptoms or issues."
                            >
{{ old("chief_complaints") }}</textarea
                            >
                        </div>
                        {{-- ** end chief complaints ** --}}

                        {{-- ** admission date ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="admission_date" class="mb-1 block text-sm font-semibold text-gray-700">
                                Admission Date
                            </label>
                            <input
                                type="date"
                                id="admission_date"
                                name="admission_date"
                                value="{{ $currentDate }}"
                                class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-4 py-2 text-base shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                readonly
                            />
                        </div>
                        {{-- ** end admission date ** --}}

                        {{-- ** room number ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="room_no" class="mb-1 block text-sm font-semibold text-gray-700">Room No.</label>
                            <input
                                type="text"
                                id="room_no"
                                name="room_no"
                                value="{{ old("room_no") }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-base shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="Enter room number"
                            />
                        </div>
                        {{-- ** end room number ** --}}

                        {{-- ** bed number ** --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="bed_no" class="mb-1 block text-sm font-semibold text-gray-700">Bed No.</label>
                            <input
                                type="text"
                                id="bed_no"
                                name="bed_no"
                                value="{{ old("bed_no") }}"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 text-base shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="Enter bed number"
                            />
                        </div>
                        {{-- ** end bed number ** --}}
                    </div>
                </div>
            </div>

            <div class="mb-10 overflow-hidden rounded-xl border border-gray-100 shadow-2xl">
                <div class="main-header flex items-center justify-between pr-4 pl-10 tracking-wider text-white">
                    <h1>EMERGENCY CONTACT</h1>
                    <button
                        type="button"
                        id="add-contact"
                        class="flex cursor-pointer items-center gap-1 rounded-md px-3 py-1 font-bold text-white transition duration-150 hover:text-yellow-400"
                    >
                        <span class="material-symbols-outlined text-base">note_stack_add</span>
                    </button>
                </div>

                <div class="space-y-4 bg-white p-6 sm:p-8" id="contact-container">
                    <div class="contact-entry animate-fadeIn grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-gray-700">Name</label>
                            <input
                                type="text"
                                name="contact_name[]"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 shadow-sm outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="Full Name"
                            />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-semibold text-gray-700">Relationship</label>
                            <input
                                type="text"
                                name="contact_relationship[]"
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 shadow-sm transition duration-150 ease-in-out outline-none focus:border-green-500 focus:ring-green-200"
                                placeholder="e.g. Parent"
                            />
                        </div>

                        <div class="flex items-end gap-2">
                            <div class="col-span-1 flex-1">
                                <div class="js-error-parent">
                                    <label class="mb-1 block text-sm font-semibold text-gray-700">Contact Number</label>

                                    <div class="js-error-container relative">
                                        <input
                                            type="tel"
                                            name="contact_number[]"
                                            {{-- 💡 FIX: Use the safe index '0' for the first template element --}}
                                            value="{{ old("contact_number.0") }}"
                                            data-server-message="{{ $errors->first("contact_number.0") }}"
                                            class="js-error-field @error("contact_number.0")
                                                outline-none
                                                border-2
                                                border-red-500
                                                has-server-error
                                                focus:ring-red-500
                                                focus:border-red-500
                                            @else
                                                {{-- ** para bumalik sa green kemerut ** --}}
                                                outline-none
                                                border
                                                border-gray-300
                                                focus:ring-green-200
                                                focus:border-green-500
                                            @enderror w-full rounded-lg px-4 py-2 pr-10 text-base placeholder-gray-400 shadow-sm transition duration-150 ease-in-out"
                                            placeholder="e.g. 0912-345-6789"
                                            autocomplete="tel"
                                        />

                                        {{-- Error Icon Display --}}
                                        @error("contact_number.0")
                                            <div
                                                class="js-error-icon pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
                                            >
                                                <span class="material-symbols-outlined text-lg text-red-500">
                                                    error
                                                </span>
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Error Message Display --}}
                                    @error("contact_number.0")
                                        <p class="js-error-message mt-1 flex items-center gap-1 text-xs text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <button
                                type="button"
                                class="remove-contact hidden p-2 leading-none font-bold text-red-600 hover:text-red-800"
                            >
                                <span class="remove-contact material-symbols-outlined text-lg">cancel</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex items-center justify-end space-x-4">
                <button type="button" onclick="window.history.back()" class="button-default">BACK</button>
                <button type="submit" class="button-default">SAVE</button>
            </div>
        </form>
    </div>
@endsection

@push("scripts")
    @vite(["resources/js/compute-age.js"])

    <script>
        const ERROR_BORDER_CLASSES = [
            'border-2',
            'border-red-500',
            'focus:ring-red-500',
            'focus:border-red-500',
            'has-server-error',
        ];
        const DEFAULT_BORDER_CLASSES = ['border', 'border-gray-300', 'focus:ring-green-200', 'focus:border-green-500'];
        const ALLOWED_CHARS_REGEX = /^[0-9\-\s\(\)]*$/;

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

        function updateErrorState(input, isError, message = 'This field is required.') {
            const container = input.closest('.col-span-6') || input.closest('.js-error-parent');
            const relativeWrapper = input.closest('.js-error-container');

            let errorMessage = container ? container.querySelector('.js-error-message') : null;
            let errorIcon = relativeWrapper ? relativeWrapper.querySelector('.js-error-icon') : null;

            if (errorMessage) errorMessage.remove();
            if (errorIcon) errorIcon.remove();
            input.classList.remove(...ERROR_BORDER_CLASSES, ...DEFAULT_BORDER_CLASSES);

            if (isError) {
                input.classList.add(...ERROR_BORDER_CLASSES);
                if (relativeWrapper) {
                    relativeWrapper.insertAdjacentHTML('beforeend', iconTemplate);
                }
                if (container) {
                    container.insertAdjacentHTML('beforeend', messageTemplate(message));
                }
            } else {
                input.classList.add(...DEFAULT_BORDER_CLASSES);
            }
        }

        function initializeContactNumberFields(inputElement) {
            inputElement.addEventListener('input', function () {
                const value = this.value;
                let isError = false;
                let errorMessage = 'Enter a valid contact number.';

                if (value.trim() === '') {
                } else if (!ALLOWED_CHARS_REGEX.test(value)) {
                    isError = true;
                }

                updateErrorState(this, isError, errorMessage);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[name="contact_number[]"]').forEach(initializeContactNumberFields);

            document.getElementById('add-contact').addEventListener('click', function () {
                const container = document.getElementById('contact-container');
                const entry = container.querySelector('.contact-entry');
                const clone = entry.cloneNode(true);

                clone.querySelectorAll('input').forEach((input) => {
                    input.value = '';
                    updateErrorState(input, false, '');
                });

                clone.querySelector('.remove-contact').classList.remove('hidden');

                const newContactNumberInput = clone.querySelector('input[name="contact_number[]"]');
                if (newContactNumberInput) {
                    initializeContactNumberFields(newContactNumberInput);
                }

                container.appendChild(clone);
            });

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-contact')) {
                    e.target.closest('.contact-entry').remove();
                }
            });

            const errorFields = document.querySelectorAll('.js-error-field.has-server-error');

            errorFields.forEach((input) => {
                const serverMessage = input.getAttribute('data-server-message');
                if (serverMessage) {
                    updateErrorState(input, true, serverMessage);
                }

                input.addEventListener('input', function () {
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

                document.querySelectorAll('.custom-dropdown-container').forEach((otherContainer) => {
                    const otherMenu = otherContainer.querySelector('.custom-dropdown-menu');
                    const otherArrow = otherContainer.querySelector('.dropdown-arrow');

                    if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                        otherMenu.classList.add('hidden');
                        if (otherArrow) otherArrow.textContent = 'arrow_drop_down';
                    }
                });

                menu.classList.toggle('hidden');

                if (menu.classList.contains('hidden')) {
                    arrow.textContent = 'arrow_drop_down';
                } else {
                    arrow.textContent = 'arrow_drop_up';
                }
            }

            document.querySelectorAll('.custom-dropdown-container').forEach((container) => {
                const button = container.querySelector('.custom-dropdown-button');
                const menu = container.querySelector('.custom-dropdown-menu');
                const hiddenInput = container.closest('.col-span-6').querySelector('input[type="hidden"]');
                const selectedTextSpan = container.querySelector('.dropdown-selected-text');

                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    toggleDropdown(this);
                });

                menu.querySelectorAll('li').forEach((option) => {
                    option.addEventListener('click', function () {
                        const selectedValue = this.getAttribute('data-value');
                        const selectedText = this.textContent.trim();
                        const arrow = container.querySelector('.dropdown-arrow');

                        hiddenInput.value = selectedValue;
                        selectedTextSpan.textContent = selectedText;

                        selectedTextSpan.classList.remove('text-gray-500');
                        selectedTextSpan.classList.add('text-gray-900');

                        menu.classList.add('hidden');
                        arrow.textContent = 'arrow_drop_down';

                        updateErrorState(button, false);
                    });
                });

                document.addEventListener('click', function (e) {
                    const menu = container.querySelector('.custom-dropdown-menu');
                    const arrow = container.querySelector('.dropdown-arrow');

                    if (!container.contains(e.target) && !menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        arrow.textContent = 'arrow_drop_down';
                    }
                });

                if (hiddenInput.value) {
                    let initialText = hiddenInput.value;
                    menu.querySelectorAll('li').forEach((option) => {
                        if (option.getAttribute('data-value') === hiddenInput.value) {
                            initialText = option.textContent.trim();
                        }
                    });

                    selectedTextSpan.textContent = initialText;
                    selectedTextSpan.classList.remove('text-gray-400');
                    selectedTextSpan.classList.add('text-gray-700');

                    updateErrorState(button, false);
                }
            });
        });
    </script>
@endpush
