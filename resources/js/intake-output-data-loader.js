
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const patientSelectForm = document.getElementById('patient-select-form');
        if (!patientSelectForm) {
            console.warn('Intake/Output Data Loader: #patient-select-form not found.');
            return;
        }

        const dateSelector = document.getElementById('date_selector');
        const dayNoSelector = document.getElementById('day_no_selector');
        const patientIdHiddenInput = document.getElementById('patient_id_hidden');
        const ioForm = document.getElementById('io-form');

        if (!dateSelector || !dayNoSelector || !patientIdHiddenInput || !ioForm) {
            console.error('Intake/Output Data Loader: Missing one or more required elements (date, day, patient_id hidden input, or io-form).');
            return;
        }

        const dropdownContainer = document.querySelector(".searchable-dropdown");
        const analyzeUrl = dropdownContainer ? dropdownContainer.dataset.selectUrl : null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        if (!analyzeUrl || !csrfToken) {
            console.error('Intake/Output Data Loader: Form missing action URL or CSRF token not found.');
            return;
        }

        const fetchIntakeOutputData = async () => {
            const patientId = patientIdHiddenInput.value;
            const date = dateSelector.value;
            const dayNo = dayNoSelector.value;

            if (!patientId) {
                console.log('No patient selected, skipping data fetch.');
                return;
            }

            console.log(`Fetching data for Patient ID: ${patientId}, Date: ${date}, Day No: ${dayNo}`);

            try {
                const response = await fetch(analyzeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest' // Laravel specific header for AJAX
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        date: date,
                        day_no: dayNo
                    })
                });

                if (!response.ok) {
                    throw new Error(`Server error: ${response.status}`);
                }

                const data = await response.json();
                console.log('Received data:', data);

                // Update form fields
                const oralIntakeInput = ioForm.querySelector('[name="oral_intake"]');
                const ivFluidsInput = ioForm.querySelector('[name="iv_fluids_volume"]');
                const urineOutputInput = ioForm.querySelector('[name="urine_output"]');

                if (oralIntakeInput) oralIntakeInput.value = data.ioData?.oral_intake ?? '';
                if (ivFluidsInput) ivFluidsInput.value = data.ioData?.iv_fluids_volume ?? '';
                if (urineOutputInput) urineOutputInput.value = data.ioData?.urine_output ?? '';

                // Update hidden fields in the io-form
                const hiddenDateInput = ioForm.querySelector('input[name="date"]');
                const hiddenDayNoInput = ioForm.querySelector('input[name="day_no"]');
                if (hiddenDateInput) hiddenDateInput.value = data.currentDate;
                if (hiddenDayNoInput) hiddenDayNoInput.value = data.currentDayNo;

                // Dispatch a custom event to notify other scripts (e.g., CDSS) that data has been loaded
                const event = new CustomEvent('io:data-loaded', { detail: { ioData: data.ioData } });
                document.dispatchEvent(event);

            } catch (error) {
                console.error('Error fetching intake and output data:', error);
                // Optionally, clear fields or show an error message
                const oralIntakeInput = ioForm.querySelector('[name="oral_intake"]');
                const ivFluidsInput = ioForm.querySelector('[name="iv_fluids_volume"]');
                const urineOutputInput = ioForm.querySelector('[name="urine_output"]');

                if (oralIntakeInput) oralIntakeInput.value = '';
                if (ivFluidsInput) ivFluidsInput.value = '';
                if (urineOutputInput) urineOutputInput.value = '';

                // Also dispatch event to clear CDSS alerts
                const event = new CustomEvent('io:data-loaded', { detail: { ioData: null } });
                document.dispatchEvent(event);
            }
        };

        // Event listeners for date and day changes
        dateSelector.addEventListener('change', fetchIntakeOutputData);
        dayNoSelector.addEventListener('change', fetchIntakeOutputData);

        // Also trigger on patient selection change (if patient_id_hidden changes)
        // This assumes patient_id_hidden is updated by another script (e.g., searchable-dropdown.js)
        // We can listen for a custom event or mutation observer if direct change event is not reliable
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    // Only fetch if the patient ID actually changed and is not empty
                    if (patientIdHiddenInput.value && mutation.oldValue !== patientIdHiddenInput.value) {
                        fetchIntakeOutputData();
                    }
                }
            });
        });
        observer.observe(patientIdHiddenInput, { attributes: true, attributeFilter: ['value'] });

        // Initial fetch if a patient is already selected on page load
        if (patientIdHiddenInput.value) {
            fetchIntakeOutputData();
        }
    });
})();
