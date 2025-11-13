import axios from "axios";

window.initializeVitalSignsDateSync = function () {
    const dateSelector = document.getElementById("date_selector");
    const dayNoSelector = document.getElementById("day_no_selector");
    const dropdownContainer = document.querySelector(".searchable-dropdown");
    const hiddenPatientIdInput = document.getElementById("patient_id_hidden");
    const vitalsForm = document.getElementById("vitals-form");
    const hiddenDayNoForVitalsFormInput = document.getElementById(
        "hidden_day_no_for_vitals_form"
    );
    const hiddenDateForVitalsFormInput = document.getElementById(
        "hidden_date_for_vitals_form"
    );

    // 1. Check Elements
    if (!dateSelector || !dayNoSelector || !dropdownContainer || !vitalsForm) {
        console.warn(
            "[VitalSignsDateSync] Aborting: Required DOM elements not found."
        );
        return;
    }

    // 2. Check Data Attributes
    const admissionDateStr = dropdownContainer.dataset.admissionDate;
    // Handle cases where times might be missing or malformed
    let times = [];
    try {
        times = vitalsForm.dataset.times
            ? JSON.parse(vitalsForm.dataset.times)
            : null;
    } catch (e) {
        console.error("[VitalSignsDateSync] Error parsing data-times:", e);
    }
    const fetchUrl = vitalsForm.dataset.fetchUrl;

    if (!admissionDateStr) {
        console.error(
            "[VitalSignsDateSync] Missing 'data-admission-date' on .searchable-dropdown"
        );
        return;
    }
    if (!times) {
        console.error(
            "[VitalSignsDateSync] Missing or invalid 'data-times' on #vitals-form"
        );
        return;
    }
    if (!fetchUrl) {
        console.error(
            "[VitalSignsDateSync] Missing 'data-fetch-url' on #vitals-form"
        );
        return;
    }

    // --- Logic Starts Here ---
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayStr = today.toISOString().split("T")[0];

    // Manual Parse for Admission Date (Y-m-d)
    const dateParts = admissionDateStr.split("-");
    const admissionDate = new Date(
        parseInt(dateParts[0], 10),
        parseInt(dateParts[1], 10) - 1,
        parseInt(dateParts[2], 10)
    );
    admissionDate.setHours(0, 0, 0, 0);

    const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    const admissionDateOnlyStr = formatLocalDate(admissionDate);
    dateSelector.max = todayStr;
    dateSelector.min = admissionDateOnlyStr;

    // Calculate Max Day No based on existing options
    const dayOptions = dayNoSelector.querySelectorAll("option");
    const maxDayNo =
        dayOptions.length > 0
            ? parseInt(dayOptions[dayOptions.length - 1].value, 10)
            : 1;

    // --- Data Fetching Logic ---
    async function loadVitalSignsData(patientId, date, dayNo) {
        if (!patientId || !date || !dayNo) return;

        try {
            console.log(
                `Fetching vitals for PatID: ${patientId}, Date: ${date}, Day: ${dayNo}`
            );
            const response = await axios.post(
                fetchUrl,
                {
                    patient_id: patientId,
                    date: date,
                    day_no: dayNo,
                },
                {
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        Accept: "application/json",
                    },
                }
            );

            const fetchedData = response.data;

            // Update UI
            times.forEach((time) => {
                const record = fetchedData[time];
                const vitalInputs = document.querySelectorAll(
                    `.vital-input[data-time="${time}"]`
                );
                const alertBox = document.querySelector(
                    `.alert-box[data-alert-for-time="${time}"]`
                );

                if (record) {
                    vitalInputs.forEach((input) => {
                        const fieldName = input.dataset.fieldName;
                        input.value =
                            record[fieldName] !== null &&
                            record[fieldName] !== undefined
                                ? record[fieldName]
                                : "";
                    });

                    if (alertBox) {
                        let alertContent = "NO ALERTS";
                        let bgColor = "";
                        if (
                            record.alerts &&
                            record.alerts !== "NONE" &&
                            record.alerts.trim() !== ""
                        ) {
                            alertContent = record.alerts;
                            bgColor =
                                record.news_severity === "CRITICAL"
                                    ? "#B71C1C"
                                    : record.news_severity === "WARNING"
                                    ? "#FF9800"
                                    : record.news_severity === "INFO"
                                    ? "#2196F3"
                                    : "";
                        }
                        alertBox.innerHTML = `<span class="opacity-70 text-white font-semibold">${alertContent}</span>`;
                        alertBox.style.backgroundColor = bgColor;
                    }
                } else {
                    vitalInputs.forEach((input) => (input.value = ""));
                    if (alertBox) {
                        alertBox.innerHTML =
                            '<span class="opacity-70 text-white font-semibold">NO ALERTS</span>';
                        alertBox.style.backgroundColor = "";
                    }
                }
            });
        } catch (error) {
            console.error("Error fetching vital signs:", error);
        }
    }

    // --- Sync Logic ---
    function updateDate() {
        let dayNo = parseInt(dayNoSelector.value, 10);
        if (isNaN(dayNo)) return;

        if (dayNo > maxDayNo) {
            dayNo = maxDayNo;
            dayNoSelector.value = maxDayNo;
        }
        if (dayNo < 1) {
            dayNo = 1;
            dayNoSelector.value = 1;
        }

        const newDate = new Date(admissionDate.getTime());
        newDate.setDate(newDate.getDate() + (dayNo - 1));
        const formattedDate = formatLocalDate(newDate);

        dateSelector.value = formattedDate;
        if (hiddenDateForVitalsFormInput)
            hiddenDateForVitalsFormInput.value = formattedDate;
        if (hiddenDayNoForVitalsFormInput)
            hiddenDayNoForVitalsFormInput.value = dayNo;

        const patientId = hiddenPatientIdInput
            ? hiddenPatientIdInput.value
            : null;
        if (patientId) loadVitalSignsData(patientId, formattedDate, dayNo);
    }

    function updateDayNo() {
        const selectedDateStr = dateSelector.value;
        if (!selectedDateStr) return;

        const selectedDateParts = selectedDateStr.split("-");
        const selectedDate = new Date(
            parseInt(selectedDateParts[0], 10),
            parseInt(selectedDateParts[1], 10) - 1,
            parseInt(selectedDateParts[2], 10)
        );
        selectedDate.setHours(0, 0, 0, 0);

        if (selectedDate.getTime() > today.getTime()) {
            selectedDate.setTime(today.getTime());
            dateSelector.value = todayStr;
        } else if (selectedDate.getTime() < admissionDate.getTime()) {
            selectedDate.setTime(admissionDate.getTime());
            dateSelector.value = admissionDateOnlyStr;
        }

        const diffTime = selectedDate.getTime() - admissionDate.getTime();
        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;

        if (diffDays > maxDayNo) dayNoSelector.value = maxDayNo;
        else dayNoSelector.value = diffDays;

        const finalDayNo = dayNoSelector.value;
        const finalDate = dateSelector.value;

        if (hiddenDayNoForVitalsFormInput)
            hiddenDayNoForVitalsFormInput.value = finalDayNo;
        if (hiddenDateForVitalsFormInput)
            hiddenDateForVitalsFormInput.value = finalDate;

        const patientId = hiddenPatientIdInput
            ? hiddenPatientIdInput.value
            : null;
        if (patientId) loadVitalSignsData(patientId, finalDate, finalDayNo);
    }

    // Remove existing listeners to prevent duplicates if script runs twice
    dayNoSelector.removeEventListener("change", updateDate);
    dateSelector.removeEventListener("change", updateDayNo);

    // Attach listeners
    dayNoSelector.addEventListener("change", updateDate);
    dateSelector.addEventListener("change", updateDayNo);

    console.log("[VitalSignsDateSync] Initialized successfully.");
};

// Auto-run if DOM is ready
if (document.readyState === "loading") {
    document.addEventListener(
        "DOMContentLoaded",
        window.initializeVitalSignsDateSync
    );
} else {
    window.initializeVitalSignsDateSync();
}
