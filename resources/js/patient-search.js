document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('patient-search');
    const patientTableBody = document.querySelector('.w-full tbody'); // Assuming this is the table body

    if (searchInput && patientTableBody) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim();

            fetch(`/patients/live-search?input=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(patients => {
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
                                            `<form action="/patients/${patient.patient_id}/activate" method="POST" class="inline-block">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                                <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">SET ACTIVE</button>
                                            </form>`
                                            :
                                            `<a href="/patients/${patient.patient_id}/edit" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>
                                            <form action="/patients/${patient.patient_id}/deactivate" method="POST" class="inline-block">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">SET INACTIVE</button>
                                            </form>`
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
                })
                .catch(error => console.error('Error fetching patients:', error));
        });
    }
});