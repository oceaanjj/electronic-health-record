document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('patient-search');
    const patientTableBody = document.querySelector('.w-full tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Exit if required elements don't exist on this page
    if (!searchInput || !patientTableBody) return;

    // Helper: smooth fade animation
    function fadeIn(element) {
        element.classList.remove('opacity-0', 'translate-y-2');
        element.classList.add('opacity-100', 'translate-y-0');
    }

    function fadeOut(element) {
        element.classList.remove('opacity-100', 'translate-y-0');
        element.classList.add('opacity-0', 'translate-y-2');
    }

    let lastRenderedHTML = '';

    // Render patient rows
    function renderPatientRows(patients) {
        // Generate new HTML first (without changing DOM yet)
        let newHTML = '';

        if (patients.length > 0) {
            newHTML = patients
                .map(
                    (patient) => `
                <tr class="${
                    !patient.is_active ? 'bg-red-100 text-red-700' : 'bg-beige'
                } hover:bg-white hover:bg-opacity-50 transition-all duration-300 opacity-0 translate-y-2" data-id="${patient.patient_id}">
                    <td class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                        ${patient.patient_id}
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30">
                        <a href="/patients/${patient.patient_id}/edit"
                            class="p-3 font-creato-black font-bold text-brown text-[13px] hover:underline hover:text-brown transition-colors duration-150">
                            ${patient.name}
                        </a>
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                        ${patient.age}
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                        ${patient.sex}
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30 whitespace-nowrap text-center">
                        ${
                            !patient.is_active
                                ? `<button type="button"
                                    class="js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-red-50 border border-red-600 px-3 py-1 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-100 cursor-pointer"
                                    data-patient-id="${patient.patient_id}" data-action="activate">RESTORE</button>`
                                : `<div class="flex justify-center gap-2">
                                    <a href="/patients/${patient.patient_id}/edit"
                                        class="inline-flex items-center justify-center rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition hover:bg-green-600 cursor-pointer">
                                        EDIT
                                    </a>
                                    <button type="button"
                                        class="js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition hover:bg-dark-red cursor-pointer"
                                        data-patient-id="${patient.patient_id}" data-action="deactivate">SET INACTIVE</button>
                                   </div>`
                        }
                    </td>
                </tr>
            `,
                )
                .join('');
        } else {
            newHTML = `
                <tr class="opacity-0 translate-y-2 transition-all duration-300">
                    <td colspan="5" class="p-4 text-center text-gray-500">No patients found.</td>
                </tr>
            `;
        }

        // ✅ Only re-render if content changed
        if (newHTML === lastRenderedHTML) return;
        lastRenderedHTML = newHTML;

        patientTableBody.querySelectorAll('tr').forEach((tr) => fadeOut(tr));

        setTimeout(() => {
            patientTableBody.innerHTML = newHTML;
            patientTableBody.querySelectorAll('tr').forEach((tr) => {
                setTimeout(() => fadeIn(tr), 50);
            });
        }, 200);
    }

    // Live search functionality
    if (searchInput && patientTableBody) {
        let debounceTimer;
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.trim();

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetch(`/patients/live-search?input=${encodeURIComponent(searchTerm)}`)
                    .then((response) => response.json())
                    .then((patients) => {
                        renderPatientRows(patients);
                    })
                    .catch((error) => console.error('Error fetching patients:', error));
            }, 250);
        });
    }
});
