function initializeMedicationAdministrationForm() {
    const form = document.querySelector('#medication-administration-form');
    if (!form) return;

    const hiddenPatientIdInput = document.getElementById('patient_id_hidden');
    const dateInput = document.getElementById('date_selector');
    const medicationInputs = document.querySelectorAll('.medication-input');
    const submitButton = document.getElementById('submit_button');

    const timeMap = {
        '10:00:00': 0,
        '14:00:00': 1,
        '18:00:00': 2,
    };

    function toggleFormElements(enable) {
        dateInput.disabled = !enable;
        medicationInputs.forEach((input) => (input.disabled = !enable));
        submitButton.disabled = !enable;
    }

    function clearMedicationInputs() {
        medicationInputs.forEach((input) => (input.value = ''));
    }

    function populateMedicationInputs(records) {
        clearMedicationInputs();

        records.forEach((record) => {
            const timeIndex = timeMap[record.time];
            if (timeIndex !== undefined) {
                const baseIndex = timeIndex * 5;
                if (medicationInputs[baseIndex]) medicationInputs[baseIndex].value = record.medication || '';
                if (medicationInputs[baseIndex + 1]) medicationInputs[baseIndex + 1].value = record.dose || '';
                if (medicationInputs[baseIndex + 2]) medicationInputs[baseIndex + 2].value = record.route || '';
                if (medicationInputs[baseIndex + 3]) medicationInputs[baseIndex + 3].value = record.frequency || '';
                if (medicationInputs[baseIndex + 4]) medicationInputs[baseIndex + 4].value = record.comments || '';
            }
        });
    }

    async function fetchAndDisplayRecords() {
        const patientId = hiddenPatientIdInput.value;
        const date = dateInput.value;

        if (!patientId || !date) {
            clearMedicationInputs();
            return;
        }

        try {
            const response = await fetch(`/medication-administration/records?patient_id=${patientId}&date=${date}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const records = await response.json();
            populateMedicationInputs(records);
        } catch (error) {
            console.error('Error fetching medication records:', error);
        }
    }

    if (!hiddenPatientIdInput.value) {
        toggleFormElements(false);
    } else {
        toggleFormElements(true);
        fetchAndDisplayRecords();
    }

    dateInput.addEventListener('change', () => {
        if (hiddenPatientIdInput.value) {
            fetchAndDisplayRecords();
        } else {
            clearMedicationInputs();
        }
    });

    // form.addEventListener('submit', async function (e) {
    //     e.preventDefault();

    //     const formData = new FormData(form);
    //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    //     try {
    //         const response = await fetch(form.action, {
    //             method: 'POST',
    //             headers: {
    //                 'X-CSRF-TOKEN': csrfToken,
    //                 'Accept': 'application/json'
    //             },
    //             body: formData
    //         });

    //         const result = await response.json();

    //         if (response.ok) {
    //             if (typeof showSuccessAlert === 'function') {
    //                 showSuccessAlert(result.message || 'Medication Administration data saved successfully!');
    //             } else {
    //                 alert(result.message || 'Medication Administration data saved successfully!');
    //             }
    //             fetchAndDisplayRecords();
    //         } else {
    //             if (typeof showErrorAlert === 'function') {
    //                 showErrorAlert(result.message || 'Error saving data.');
    //             } else {
    //                 alert(result.message || 'Error saving data.');
    //             }
    //             console.error('Form submission error:', result);
    //         }
    //     } catch (error) {
    //         console.error('Network or unexpected error during form submission:', error);
    //         if (typeof showErrorAlert === 'function') {
    //             showErrorAlert('An unexpected error occurred. Please try again.');
    //         } else {
    //             alert('An unexpected error occurred. Please try again.');
    //         }
    //     }
    // });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeMedicationAdministrationForm();
});

// Listener for patient cleared
document.addEventListener('patient:cleared', () => {
    const form = document.querySelector('#medication-administration-form');
    if (!form) return;

    const dateInput = document.getElementById('date_selector');
    const medicationInputs = document.querySelectorAll('.medication-input');
    const submitButton = document.getElementById('submit_button');

    function toggleFormElements(enable) {
        dateInput.disabled = !enable;
        medicationInputs.forEach((input) => (input.disabled = !enable));
        submitButton.disabled = !enable;
    }

    function clearMedicationInputs() {
        medicationInputs.forEach((input) => (input.value = ''));
    }

    toggleFormElements(false);
    clearMedicationInputs();
});

// Listener for patient selection
document.addEventListener('patient:selected', async (event) => {
    const { patientId, selectUrl } = event.detail;
    const formContainer = document.getElementById('form-content-container');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!formContainer || !selectUrl || !patientId) {
        return;
    }

    try {
        const response = await fetch(selectUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'X-Fetch-Form-Content': 'true',
            },
            body: `patient_id=${encodeURIComponent(patientId)}`,
        });

        if (!response.ok) throw new Error(`Server responded with status: ${response.status}`);

        const htmlText = await response.text();
        const parser = new DOMParser();
        const newHtml = parser.parseFromString(htmlText, 'text/html');
        const newContent = newHtml.getElementById('form-content-container');

        if (newContent) {
            formContainer.innerHTML = newContent.innerHTML;
            if (typeof window.initSearchableDropdown === 'function') {
                window.initSearchableDropdown();
            }
            initializeMedicationAdministrationForm();
        } else {
            throw new Error("Could not find '#form-content-container' in response.");
        }
    } catch (error) {
        console.error('Patient loading failed:', error);
        window.location.reload();
    }
});
