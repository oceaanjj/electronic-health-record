document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('patient-search');
    const patientTableBody = document.querySelector('.w-full tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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
            newHTML = patients.map(patient => `
                <tr class="${
                    patient.deleted_at ? 'bg-red-100 text-red-700' : 'bg-beige'
                } hover:bg-white hover:bg-opacity-50 transition-all duration-300 opacity-0 translate-y-2" data-id="${patient.patient_id}">
                    <td class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                        ${patient.patient_id}
                    </td>
                    <td class="p-3 border-b-2 border-line-brown/30">
                        <a href="/patients/${patient.patient_id}"
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
                            patient.deleted_at
                                ? `<button type="button"
                                    class="inline-block bg-green-500 cursor-pointer hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                    data-patient-id="${patient.patient_id}" data-action="activate">SET ACTIVE</button>`
                                : `<a href="/patients/${patient.patient_id}/edit"
                                    class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>
                                    <button type="button"
                                        class="inline-block bg-red-600 cursor-pointer hover:bg-dark-red text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                        data-patient-id="${patient.patient_id}" data-action="deactivate">SET INACTIVE</button>`
                        }
                    </td>
                </tr>
            `).join('');
        } else {
            newHTML = `
                <tr class="opacity-0 translate-y-2 transition-all duration-300">
                    <td colspan="5" class="p-4 text-center text-gray-500">No patients found.</td>
                </tr>
            `;
        }

        // ✅ Only re-render if content changed (prevents double blinking)
        if (newHTML === lastRenderedHTML) return;
        lastRenderedHTML = newHTML;

        // Fade out existing rows first
        patientTableBody.querySelectorAll('tr').forEach(tr => fadeOut(tr));

        // Wait for fade-out transition (200ms)
        setTimeout(() => {
            patientTableBody.innerHTML = newHTML;

            // Fade-in each row
            patientTableBody.querySelectorAll('tr').forEach(tr => {
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
                    .then(response => response.json())
                    .then(patients => {
                        renderPatientRows(patients);
                    })
                    .catch(error => console.error('Error fetching patients:', error));
            }, 250);
        });
    }

    // Activate/Deactivate functionality
    patientTableBody.addEventListener('click', function (event) {
        const target = event.target;
        if (!target.classList.contains('js-toggle-patient-status')) return;

        event.preventDefault();

        const patientId = target.dataset.patientId;
        const action = target.dataset.action;
        const url = `/patients/${patientId}/${action}`;
        const method = action === 'deactivate' ? 'DELETE' : 'POST';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-HTTP-Method-Override': method
            },
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.patient) {
                    const row = target.closest('tr');
                    if (!row) return;

                    row.classList.add('transition-all', 'duration-300', 'ease-in-out');

                    if (data.patient.deleted_at) {
                        row.classList.remove('bg-beige');
                        row.classList.add('bg-red-100', 'text-red-700');
                    } else {
                        row.classList.remove('bg-red-100', 'text-red-700');
                        row.classList.add('bg-beige');
                    }

                    const actionsCell = row.querySelector('td:last-child');
                    if (actionsCell) {
                        actionsCell.innerHTML = data.patient.deleted_at
                            ? `<button type="button"
                                class="inline-block bg-green-500 cursor-pointer hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                data-patient-id="${data.patient.patient_id}" data-action="activate">SET ACTIVE</button>`
                            : `<a href="/patients/${data.patient.patient_id}/edit"
                                class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>
                                <button type="button"
                                class="inline-block bg-red-600 cursor-pointer hover:bg-dark-red text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                data-patient-id="${data.patient.patient_id}" data-action="deactivate">SET INACTIVE</button>`;
                    }
                }
            })
            .catch(error => console.error('Error toggling patient status:', error));
    });
});
