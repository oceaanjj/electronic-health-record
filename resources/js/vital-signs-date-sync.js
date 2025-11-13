import axios from "axios";

window.initializeVitalSignsDateSync = function () {
    // Prevent multiple global listeners if this function is called multiple times
    if (window.vitalSignsSyncInitialized) return;
    window.vitalSignsSyncInitialized = true;

    console.log("[VitalSignsDateSync] Initializing Global Event Delegation.");

    // --- Helper: Format Date Y-m-d ---
    const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    // --- Core Function: Fetch & Update Vitals ---
    async function loadVitalSignsData(patientId, date, dayNo) {
        const vitalsForm = document.getElementById("vitals-form");
        if (!vitalsForm || !patientId || !date || !dayNo) return;

        const fetchUrl = vitalsForm.dataset.fetchUrl;
        let times = [];
        try {
            times = vitalsForm.dataset.times
                ? JSON.parse(vitalsForm.dataset.times)
                : [];
        } catch (e) {
            console.error("Error parsing times", e);
        }

        try {
            const response = await axios.post(
                fetchUrl,
                { patient_id: patientId, date: date, day_no: dayNo },
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
            console.error("[VitalSignsDateSync] Fetch error:", error);
        }
    }

    // --- Event Delegation: Listen for changes on document ---
    document.addEventListener("change", (e) => {
        const targetId = e.target.id;

        // Only react to our specific selectors
        if (targetId !== "day_no_selector" && targetId !== "date_selector")
            return;

        // Dynamic DOM Lookups (Get fresh elements every time)
        const dropdownContainer = document.querySelector(
            ".searchable-dropdown"
        );
        const dateSelector = document.getElementById("date_selector");
        const dayNoSelector = document.getElementById("day_no_selector");
        const hiddenPatientIdInput =
            document.getElementById("patient_id_hidden");
        const hiddenDateInput = document.getElementById(
            "hidden_date_for_vitals_form"
        );
        const hiddenDayInput = document.getElementById(
            "hidden_day_no_for_vitals_form"
        );

        if (!dropdownContainer || !dateSelector || !dayNoSelector) return;

        // Parse Admission Date
        const admissionDateStr = dropdownContainer.dataset.admissionDate;
        if (!admissionDateStr) return;

        const dateParts = admissionDateStr.split("-");
        const admissionDate = new Date(
            parseInt(dateParts[0], 10),
            parseInt(dateParts[1], 10) - 1,
            parseInt(dateParts[2], 10)
        );
        admissionDate.setHours(0, 0, 0, 0);

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const todayStr = today.toISOString().split("T")[0];
        const admissionDateOnlyStr = formatLocalDate(admissionDate);

        // Logic: Handle Day No Change
        if (targetId === "day_no_selector") {
            let dayNo = parseInt(dayNoSelector.value, 10);

            // Calculate Max Day
            const dayOptions = dayNoSelector.querySelectorAll("option");
            const maxDayNo =
                dayOptions.length > 0
                    ? parseInt(dayOptions[dayOptions.length - 1].value, 10)
                    : 30;

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

            if (hiddenDateInput) hiddenDateInput.value = formattedDate;
            if (hiddenDayInput) hiddenDayInput.value = dayNo;

            if (hiddenPatientIdInput && hiddenPatientIdInput.value) {
                loadVitalSignsData(
                    hiddenPatientIdInput.value,
                    formattedDate,
                    dayNo
                );
            }
        }

        // Logic: Handle Date Change
        if (targetId === "date_selector") {
            const selectedDateStr = dateSelector.value;
            if (!selectedDateStr) return;

            const sParts = selectedDateStr.split("-");
            const selectedDate = new Date(
                parseInt(sParts[0], 10),
                parseInt(sParts[1], 10) - 1,
                parseInt(sParts[2], 10)
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

            // Calculate Diff
            const diffTime = selectedDate.getTime() - admissionDate.getTime();
            const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;

            // Get Max Day
            const dayOptions = dayNoSelector.querySelectorAll("option");
            const maxDayNo =
                dayOptions.length > 0
                    ? parseInt(dayOptions[dayOptions.length - 1].value, 10)
                    : 30;

            if (diffDays > maxDayNo) dayNoSelector.value = maxDayNo;
            else if (diffDays < 1) dayNoSelector.value = 1;
            else dayNoSelector.value = diffDays;

            const finalDayNo = dayNoSelector.value;
            const finalDate = dateSelector.value;

            if (hiddenDayInput) hiddenDayInput.value = finalDayNo;
            if (hiddenDateInput) hiddenDateInput.value = finalDate;

            if (hiddenPatientIdInput && hiddenPatientIdInput.value) {
                loadVitalSignsData(
                    hiddenPatientIdInput.value,
                    finalDate,
                    finalDayNo
                );
            }
        }
    });
};

// Auto-run
if (document.readyState === "loading") {
    document.addEventListener(
        "DOMContentLoaded",
        window.initializeVitalSignsDateSync
    );
} else {
    window.initializeVitalSignsDateSync();
}
