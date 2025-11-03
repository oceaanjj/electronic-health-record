document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('patientSearchInput');
    const patientTableBody = document.getElementById('patientTableBody');

    if (searchInput && patientTableBody) {
        // Function to fetch and display patients
        const fetchAndDisplayPatients = (filter) => {
            if (filter.length === 0) {
                patientTableBody.innerHTML = '<tr><td colspan="3">Please enter a patient ID or Name.</td></tr>'; // Display message when search is empty
                return;
            }

            fetch(`/patients/live-search?input=${filter}`)
                .then(response => response.json())
                .then(data => {
                    patientTableBody.innerHTML = ''; // Clear existing rows

                    if (data.length > 0) {
                        data.forEach(patient => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${patient.patient_id}</td>
                                <td>${patient.name}</td>
                                <td>${patient.age}</td>
                            `;
                            patientTableBody.appendChild(row);
                        });
                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td colspan="3">No patient found matching "${filter}" in your records.</td>`;
                        patientTableBody.appendChild(row);
                    }
                })
                .catch(error => {
                    console.error('Error fetching patient data:', error);
                    patientTableBody.innerHTML = `<tr><td colspan="3">Error loading patients.</td></tr>`;
                });
        };

        // Initial load if there's a pre-filled search input (e.g., from a previous search)
        if (searchInput.value.length > 0) {
            fetchAndDisplayPatients(searchInput.value);
        } else {
            // If the input is empty on load, show the initial message
            patientTableBody.innerHTML = '<tr><td colspan="3">Please enter a patient ID or Name.</td></tr>';
        }

        // Event listener for input changes
        searchInput.addEventListener('input', function() {
            fetchAndDisplayPatients(searchInput.value);
        });
    }
});
