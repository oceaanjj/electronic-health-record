document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('patient-search');
    const patientTableBody = document.querySelector('.w-full tbody');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Function to render patient rows
    function renderPatientRows(patients) {
        patientTableBody.innerHTML = ''; // Clear existing rows

        if (patients.length > 0) {
            patients.forEach(patient => {
                const row = `
                    <tr class="${patient.deleted_at ? 'bg-red-100 text-red-700' : 'bg-beige'} hover:bg-gray-100" data-id="${patient.patient_id}">
                        <td class="p-3 border-b-2 border-line-brown/70 font-creato-black font-bold text-brown text-[13px] text-center border-r-2">${patient.patient_id}</td>
                        <td class="p-3 border-b-2 border-line-brown/70 border-r-2">
                            <a href="/patients/${patient.patient_id}" class="p-3 font-creato-black font-bold text-brown text-[13px]">
                                ${patient.name}
                            </a>
                        </td>
                        <td class="p-3 border-b-2 border-line-brown/70 font-creato-black font-bold text-brown text-[13px] border-r-2 text-center">${patient.age}</td>
                        <td class="p-3 border-b-2 border-line-brown/70 font-creato-black font-bold text-brown text-[13px] border-r-2 text-center">${patient.sex}</td>
                        <td class="p-3 border-b-2 border-line-brown/70 whitespace-nowrap text-center">
                            ${patient.deleted_at ?
                                `<button type="button" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status" data-patient-id="${patient.patient_id}" data-action="activate">SET ACTIVE</button>`
                                :
                                `<a href="/patients/${patient.patient_id}/edit" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>
                                <button type="button" class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status" data-patient-id="${patient.patient_id}" data-action="deactivate">SET INACTIVE</button>`
                            }
                        </td>
                    </tr>
                `;
                patientTableBody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            patientTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        No patients found.
                    </td>
                </tr>
            `;
        }
    }

    // Live search functionality
    if (searchInput && patientTableBody) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();
            fetch(`/patients/live-search?input=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(patients => {
                    renderPatientRows(patients);
                })
                .catch(error => console.error('Error fetching patients:', error));
        });
    }

    // Activate/Deactivate functionality
    patientTableBody.addEventListener('click', function(event) {
        const target = event.target;

        if (target.classList.contains('js-toggle-patient-status')) {
            event.preventDefault();

            const patientId = target.dataset.patientId;
            const action = target.dataset.action; // 'activate' or 'deactivate'
            const url = `/patients/${patientId}/${action}`;
            const method = action === 'deactivate' ? 'DELETE' : 'POST';

            fetch(url, {
                method: 'POST', // Always POST for Laravel routes, actual method is spoofed
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': method // Spoof DELETE for deactivation
                },
                body: JSON.stringify({}) // Empty body for POST/DELETE requests
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.patient) {
                    const row = target.closest('tr');
                    if (row) {
                        // Update row styling
                        if (data.patient.deleted_at) { // Patient is now inactive
                            row.classList.remove('bg-beige');
                            row.classList.add('bg-red-100', 'text-red-700');
                        } else { // Patient is now active
                            row.classList.remove('bg-red-100', 'text-red-700');
                            row.classList.add('bg-beige');
                        }

                        // Update action buttons
                        const actionsCell = row.querySelector('td:last-child'); // Assuming actions are in the last td
                        if (actionsCell) {
                            actionsCell.innerHTML = `
                                ${data.patient.deleted_at ?
                                    `<button type="button" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status" data-patient-id="${data.patient.patient_id}" data-action="activate">SET ACTIVE</button>`
                                    :
                                    `<a href="/patients/${data.patient.patient_id}/edit" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>
                                    <button type="button" class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status" data-patient-id="${data.patient.patient_id}" data-action="deactivate">SET INACTIVE</button>`
                                }
                            `;
                        }
                    }
                    console.log(data.message);
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => console.error('Error toggling patient status:', error));
        }
    });
});