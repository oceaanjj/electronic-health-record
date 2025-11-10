import axios from "axios";

window.initializeVitalSignsDateSync = function () {
    const dateSelector = document.getElementById("date_selector");
    const dayNoSelector = document.getElementById("day_no_selector");
    const dropdownContainer = document.querySelector(".searchable-dropdown");
    const hiddenPatientIdInput = document.getElementById("patient_id_hidden"); // Get patient ID
    const vitalsForm = document.getElementById("vitals-form");
    const hiddenDayNoForVitalsFormInput = document.getElementById(
        "hidden_day_no_for_vitals_form"
    );
    const hiddenDateForVitalsFormInput = document.getElementById(
        "hidden_date_for_vitals_form"
    );

    if (
        !dateSelector ||
        !dayNoSelector ||
        !dropdownContainer ||
        !hiddenPatientIdInput ||
        !vitalsForm ||
        !hiddenDayNoForVitalsFormInput ||
        !hiddenDateForVitalsFormInput
    ) {
        return;
    }

    const admissionDateStr = dropdownContainer.dataset.admissionDate;
    const times = JSON.parse(vitalsForm.dataset.times);
    const fetchUrl = vitalsForm.dataset.fetchUrl;

    if (!admissionDateStr || !times || !fetchUrl) {
        return;
    }
    const admissionDate = new Date(admissionDateStr);
    // to account for timezone differences
    admissionDate.setMinutes(
        admissionDate.getMinutes() + admissionDate.getTimezoneOffset()
    );

    async function loadVitalSignsData(patientId, date, dayNo) {
        console.log("loadVitalSignsData called with:", {
            patientId,
            date,
            dayNo,
        });
        if (!patientId || !date || !dayNo) {
            console.log(
                "Clearing vital signs fields due to missing patientId, date, or dayNo."
            );
            // Clear all vital input fields and alerts if no patient/date/day is selected
            times.forEach((time) => {
                const vitalInputs = document.querySelectorAll(
                    `.vital-input[data-time="${time}"]`
                );
                vitalInputs.forEach((input) => (input.value = ""));
                const alertBox = document.querySelector(
                    `.alert-box[data-alert-for-time="${time}"]`
                );
                if (alertBox) {
                    alertBox.innerHTML =
                        '<span class="opacity-70 text-white font-semibold">NO ALERTS</span>';
                    alertBox.style.backgroundColor = ""; // Reset background
                }
            });
            return;
        }

        try {
            console.log("Making AJAX request to:", fetchUrl, "with params:", {
                patient_id: patientId,
                date: date,
                day_no: dayNo,
            });
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
                    },
                }
            );

            const fetchedData = response.data;
            console.log("Fetched data:", fetchedData);

            times.forEach((time) => {
                const record = fetchedData[time];
                const vitalInputs = document.querySelectorAll(
                    `.vital-input[data-time="${time}"]`
                );
                const alertBox = document.querySelector(
                    `.alert-box[data-alert-for-time="${time}"]`
                );

                if (record) {
                    console.log(
                        "Updating fields for time:",
                        time,
                        "with record:",
                        record
                    );
                    vitalInputs.forEach((input) => {
                        const fieldName = input.dataset.fieldName;
                        input.value =
                            record[fieldName] !== null ? record[fieldName] : "";
                        console.log(
                            `Set input ${input.name} to: ${input.value}`
                        );
                    });

                    // Update alerts
                    if (alertBox) {
                        let alertContent = "";
                        let bgColor = "";
                        if (record.alerts && record.alerts !== "NONE") {
                            alertContent = record.alerts;
                            if (record.news_severity === "CRITICAL") {
                                bgColor = "#B71C1C"; // Red
                            } else if (record.news_severity === "WARNING") {
                                bgColor = "#FF9800"; // Orange
                            } else if (record.news_severity === "INFO") {
                                bgColor = "#2196F3"; // Blue
                            }
                        } else {
                            alertContent = "NO ALERTS";
                            bgColor = ""; // Reset to default
                        }
                        alertBox.innerHTML = `<span class="opacity-70 text-white font-semibold">${alertContent}</span>`;
                        alertBox.style.backgroundColor = bgColor;
                        console.log(
                            `Updated alert for time ${time}: ${alertContent}, bgColor: ${bgColor}`
                        );
                    }
                } else {
                    console.log(
                        "No record for time:",
                        time,
                        ". Clearing fields."
                    );
                    // Clear inputs and alerts if no record for this time
                    vitalInputs.forEach((input) => (input.value = ""));
                    if (alertBox) {
                        alertBox.innerHTML =
                            '<span class="opacity-70 text-white font-semibold">NO ALERTS</span>';
                        alertBox.style.backgroundColor = "";
                    }
                }
            });
        } catch (error) {
            console.error("Error fetching vital signs data:", error);
            // Optionally, display an error message to the user
        }
    }

    function updateDate() {
        const dayNo = parseInt(dayNoSelector.value, 10);
        if (isNaN(dayNo)) {
            return;
        }

        const newDate = new Date(admissionDate.getTime());
        newDate.setDate(newDate.getDate() + dayNo); // Subtract 1 because day 1 is admission date

        const year = newDate.getFullYear();
        const month = String(newDate.getMonth() + 1).padStart(2, "0");
        const day = String(newDate.getDate()).padStart(2, "0");

        const formattedDate = `${year}-${month}-${day}`;

        dateSelector.value = formattedDate;

        hiddenDateForVitalsFormInput.value = formattedDate; // Update the hidden input for the main form

        hiddenDayNoForVitalsFormInput.value = dayNo; // Update the hidden input for the main form

        console.log(
            "updateDate: Setting date to:",
            formattedDate,
            "and dayNo to:",
            dayNo
        );

        // Load data for the new date/day
        const patientId = hiddenPatientIdInput.value;
        if (patientId) {
            loadVitalSignsData(patientId, formattedDate, dayNo);
        }
    }

    function updateDayNo() {
        const selectedDate = new Date(dateSelector.value);
        if (isNaN(selectedDate.getTime())) {
            return;
        }
        // to account for timezone differences
        selectedDate.setMinutes(
            selectedDate.getMinutes() + selectedDate.getTimezoneOffset()
        );

        const diffTime = Math.abs(selectedDate - admissionDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Add 1 for day number

        dayNoSelector.value = diffDays;

        hiddenDayNoForVitalsFormInput.value = diffDays; // Update the hidden input for the main form

        hiddenDateForVitalsFormInput.value = dateSelector.value; // Update the hidden input for the main form

        console.log(
            "updateDayNo: Setting dayNo to:",
            diffDays,
            "and date to:",
            dateSelector.value
        );

        // Load data for the new date/day
        const patientId = hiddenPatientIdInput.value;
        const date = dateSelector.value;
        if (patientId && date) {
            loadVitalSignsData(patientId, date, diffDays);
        }
    }

    dayNoSelector.addEventListener("change", updateDate);
    dateSelector.addEventListener("change", updateDayNo);

    // Initial load of data if a patient is already selected
    document.addEventListener("DOMContentLoaded", () => {
        const patientId = hiddenPatientIdInput.value;
        const date = dateSelector.value;
        const dayNo = dayNoSelector.value;
        if (patientId && date && dayNo) {
            loadVitalSignsData(patientId, date, dayNo);
        }
    });
};
