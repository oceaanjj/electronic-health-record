import axios from 'axios';

/**
 * ===================================================================================
 * Date/Day Sync & Data Loader
 *
 * This script provides universal date/day synchronization for any form using
 * #date_selector and #day_no_selector. It uses event delegation, so it
 * works even when the form is reloaded via AJAX.
 *
 * It operates in two modes, set by `data-sync-mode` on the .searchable-dropdown:
 *
 * 1.  data-sync-mode="html-reload" (e.g., ADL, Physical Exam)
 * - Fetches full HTML and replaces the #form-content-container.
 * - Uses `data-select-url` from .searchable-dropdown.
 *
 * 2.  data-sync-mode="json-vitals" (e.g., Vital Signs)
 * - Fetches JSON and updates the vitals table inputs.
 * - Uses `data-fetch-url` from #vitals-form.
 *
 * ===================================================================================
 */

//
//
//
//
//
//
if (!window.universalDateSyncAttached) {
    window.universalDateSyncAttached = true;
    console.log('[DateSync] Initializing Universal Date/Day Sync');

    // --- Helper: Format Date Y-m-d ---
    const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    // --- Mode 1: HTML Reload (for ADL, PE, etc.) ---
    async function loadFormContent(patientId, date, dayNo, selectUrl) {
        const formContainer = document.getElementById('form-content-container');
        if (!formContainer) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const overlay = formContainer.querySelector('.form-overlay');
        if (overlay) overlay.style.display = 'flex';

        try {
            const response = await fetch(selectUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Fetch-Form-Content': 'true',
                },
                body: `patient_id=${encodeURIComponent(
                    patientId,
                )}&date=${encodeURIComponent(date)}&day_no=${encodeURIComponent(dayNo)}`,
            });

            if (!response.ok) throw new Error(`Server error: ${response.status}`);

            const htmlText = await response.text();
            const parser = new DOMParser();
            const newHtml = parser.parseFromString(htmlText, 'text/html');
            const newContent = newHtml.getElementById('form-content-container');

            if (newContent) {
                formContainer.innerHTML = newContent.innerHTML;
                // Dispatch reload event for other scripts (like alerts)
                document.dispatchEvent(
                    new CustomEvent('cdss:form-reloaded', {
                        bubbles: true,
                        detail: { formContainer: formContainer },
                    }),
                );
            }
        } catch (error) {
            console.error('[DateSync] HTML content loading failed:', error);
            if (overlay) overlay.style.display = 'none';
        }
    }

    // --- Mode 2: JSON Fetch (for Vital Signs) ---
    //
    //
    //
    //
    //
    //

    async function loadVitalSignsData(patientId, date, dayNo) {
        const vitalsForm = document.getElementById('vitals-form');
        if (!vitalsForm || !patientId || !date || !dayNo) return;

        const fetchUrl = vitalsForm.dataset.fetchUrl;
        let times = [];
        try {
            times = vitalsForm.dataset.times ? JSON.parse(vitalsForm.dataset.times) : [];
        } catch (e) {
            console.error('Error parsing times', e);
        }

        try {
            const response = await axios.post(
                fetchUrl,
                { patient_id: patientId, date: date, day_no: dayNo },
                {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        Accept: 'application/json',
                    },
                },
            );
            const fetchedData = response.data;

            // Update UI
            times.forEach((time) => {
                const record = fetchedData[time];
                const vitalInputs = document.querySelectorAll(`.vital-input[data-time="${time}"]`);
                const alertBox = document.querySelector(`.alert-box[data-alert-for-time="${time}"]`);

                if (record) {
                    vitalInputs.forEach((input) => {
                        const fieldName = input.dataset.fieldName;
                        input.value =
                            record[fieldName] !== null && record[fieldName] !== undefined ? record[fieldName] : '';
                    });
                    if (alertBox) {
                        let alertContent = 'NO ALERTS';
                        let bgColor = '';
                        if (record.alerts && record.alerts !== 'NONE' && record.alerts.trim() !== '') {
                            alertContent = record.alerts;
                            bgColor =
                                record.news_severity === 'CRITICAL'
                                    ? '#B71C1C'
                                    : record.news_severity === 'WARNING'
                                      ? '#FF9800'
                                      : record.news_severity === 'INFO'
                                        ? '#2196F3'
                                        : '';
                        }
                        alertBox.innerHTML = `<span class="opacity-70 text-white font-semibold">${alertContent}</span>`;
                        alertBox.style.backgroundColor = bgColor;
                    }
                } else {
                    vitalInputs.forEach((input) => (input.value = ''));
                    if (alertBox) {
                        alertBox.innerHTML = '<span class="opacity-70 text-white font-semibold">NO ALERTS</span>';
                        alertBox.style.backgroundColor = '';
                    }
                }
            });
        } catch (error) {
            console.error('[DateSync] Vitals fetch error:', error);
        }
    }

    //
    //
    //
    //
    //
    //
    // --- Global Event Listener (Event Delegation) ---
    document.addEventListener('change', (e) => {
        const targetId = e.target.id;
        if (targetId !== 'day_no_selector' && targetId !== 'date_selector') return;

        // Find current elements
        const dropdownContainer = document.querySelector('.searchable-dropdown');
        const dateSelector = document.getElementById('date_selector');
        const dayNoSelector = document.getElementById('day_no_selector');
        const patientIdInput = document.getElementById('patient_id_hidden');

        if (!dropdownContainer || !dateSelector || !dayNoSelector || !patientIdInput) return;

        const patientId = patientIdInput.value;
        const syncMode = dropdownContainer.dataset.syncMode; // "html-reload" or "json-vitals"
        const admissionDateStr = dropdownContainer.dataset.admissionDate;
        const selectUrl = dropdownContainer.dataset.selectUrl; // For "html-reload"

        if (!patientId || !syncMode || !admissionDateStr) return;

        // Parse Admission Date
        const dateParts = admissionDateStr.split('-');
        const admissionDate = new Date(
            parseInt(dateParts[0], 10),
            parseInt(dateParts[1], 10) - 1,
            parseInt(dateParts[2], 10),
        );
        admissionDate.setHours(0, 0, 0, 0);

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const todayStr = today.toISOString().split('T')[0];
        const admissionDateOnlyStr = formatLocalDate(admissionDate);

        // Get Max Day from dropdown options
        const dayOptions = dayNoSelector.querySelectorAll('option');
        const maxDayNo = dayOptions.length > 0 ? parseInt(dayOptions[dayOptions.length - 1].value, 10) : 30; // Fallback

        // --- Main Sync Logic ---
        let finalDate = dateSelector.value;
        let finalDayNo = parseInt(dayNoSelector.value, 10);

        if (targetId === 'day_no_selector') {
            // --- Day Changed: Update Date ---
            if (isNaN(finalDayNo)) return;
            if (finalDayNo > maxDayNo) {
                finalDayNo = maxDayNo;
                dayNoSelector.value = maxDayNo;
            }
            if (finalDayNo < 1) {
                finalDayNo = 1;
                dayNoSelector.value = 1;
            }

            const newDate = new Date(admissionDate.getTime());
            newDate.setDate(newDate.getDate() + (finalDayNo - 1));
            finalDate = formatLocalDate(newDate);
            dateSelector.value = finalDate;
        } else if (targetId === 'date_selector') {
            // --- Date Changed: Update Day ---
            if (!finalDate) return;

            const sParts = finalDate.split('-');
            const selectedDate = new Date(
                parseInt(sParts[0], 10),
                parseInt(sParts[1], 10) - 1,
                parseInt(sParts[2], 10),
            );
            selectedDate.setHours(0, 0, 0, 0);

            // Bounds Check
            if (selectedDate.getTime() > today.getTime()) {
                selectedDate.setTime(today.getTime());
                dateSelector.value = todayStr;
            } else if (selectedDate.getTime() < admissionDate.getTime()) {
                selectedDate.setTime(admissionDate.getTime());
                dateSelector.value = admissionDateOnlyStr;
            }

            const diffTime = selectedDate.getTime() - admissionDate.getTime();
            let diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > maxDayNo) diffDays = maxDayNo;
            if (diffDays < 1) diffDays = 1;

            finalDayNo = diffDays;
            dayNoSelector.value = finalDayNo;
            finalDate = dateSelector.value; // Get final bounded value
        }

        // --- Trigger Data Load Based on Mode ---
        if (syncMode === 'html-reload' && selectUrl) {
            console.log(`[DateSync] Mode: HTML Reload. Fetching...`);
            loadFormContent(patientId, finalDate, finalDayNo, selectUrl);
        } else if (syncMode === 'json-vitals') {
            console.log(`[DateSync] Mode: JSON Vitals. Fetching...`);
            // Sync hidden inputs for form submission
            const hiddenDateInput = document.getElementById('hidden_date_for_vitals_form');
            const hiddenDayInput = document.getElementById('hidden_day_no_for_vitals_form');
            if (hiddenDateInput) hiddenDateInput.value = finalDate;
            if (hiddenDayInput) hiddenDayInput.value = finalDayNo;

            loadVitalSignsData(patientId, finalDate, finalDayNo);
        }
    });
}
