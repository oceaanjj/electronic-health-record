/**
 * date-day-loader.js
 *
 * Initializes listeners for Date and Day selectors (ID: date_selector, day_no_selector).
 * When either is changed, it sends an AJAX request using the provided form's URL
 * and loads the new content into the #form-content-container, preventing a full page refresh.
 *
 * NOTE: This assumes the controller on the server is capable of accepting patient_id, date,
 * and day_no parameters and rendering the appropriate view with data.
 *
 * @param {string} selectUrl - The route URL (e.g., 'adl.select' or 'physical-exam.select')
 * to fetch new content when date/day changes.
 */
window.initializeDateDayLoader = function (selectUrl) {
    // If we're initializing this on page load (not via patient-loader), try to find the URL
    if (!selectUrl) {
        const dropdownContainer = document.querySelector(
            ".searchable-dropdown"
        );
        selectUrl = dropdownContainer
            ? dropdownContainer.dataset.selectUrl
            : null;
    }

    const formContainer = document.getElementById("form-content-container");

    // Get elements for event listeners (these will be re-queried inside handleDateDayChange)
    const initialDateSelector = document.getElementById("date_selector");
    const initialDayNoSelector = document.getElementById("day_no_selector");
    const initialPatientIdHidden = document.getElementById("patient_id_hidden");

    const handleDateDayChange = async () => {
        // Introduce a micro-delay to allow other event handlers (like date-sync) to finish.
        await new Promise(resolve => setTimeout(resolve, 0));

        // Re-query elements to ensure they are fresh after potential DOM updates
        const patientIdHidden = document.getElementById("patient_id_hidden");
        const dateSelector = document.getElementById("date_selector");
        const dayNoSelector = document.getElementById("day_no_selector");

        const patientId = patientIdHidden.value;
        const date = dateSelector.value;
        const dayNo = dayNoSelector.value;

        // Ensure a patient is selected and both date/day have values before fetching
        if (!patientId || !date || !dayNo) {
            console.warn(
                "Date/Day Loader: Cannot load data. Missing Patient ID, Date, or Day No."
            );
            return;
        }

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        // Show loading state
        const overlay = formContainer.querySelector(".form-overlay");
        if (overlay) overlay.style.display = "flex";

        try {
            const response = await fetch(selectUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    "X-Fetch-Form-Content": "true",
                },
                // Pass all three values to the controller to fetch specific data from SQL
                body: `patient_id=${encodeURIComponent(
                    patientId
                )}&date=${encodeURIComponent(date)}&day_no=${encodeURIComponent(
                    dayNo
                )}`,
            });

            if (!response.ok)
                throw new Error(
                    `Server responded with status: ${response.status}`
                );

            const htmlText = await response.text();
            const parser = new DOMParser();
            const newHtml = parser.parseFromString(htmlText, "text/html");

            // The controller returns the whole view, but we only need the form content
            const newContent = newHtml.getElementById("form-content-container");

            if (newContent) {
                formContainer.innerHTML = newContent.innerHTML;

                // Re-initialize CDSS alerts for the new form content
                const newCdssForm = formContainer.querySelector(".cdss-form");
                if (newCdssForm) {
                    if (newCdssForm.id === 'vitals-form' && typeof window.initializeVitalSignsAlerts === 'function') {
                        window.initializeVitalSignsAlerts();
                    } else if (newCdssForm.id === 'io-form' && typeof window.initializeIntakeOutputAlerts === 'function') {
                        window.initializeIntakeOutputAlerts();
                    }
                }

                // Re-attach event listeners to the newly rendered date/day selectors
                const updatedDateSelector = document.getElementById("date_selector");
                const updatedDayNoSelector = document.getElementById("day_no_selector");

                if (updatedDateSelector && updatedDayNoSelector) {
                    updatedDateSelector.removeEventListener("change", handleDateDayChange);
                    updatedDayNoSelector.removeEventListener("change", handleDateDayChange);
                    updatedDateSelector.addEventListener("change", handleDateDayChange);
                    updatedDayNoSelector.addEventListener("change", handleDateDayChange);

                    // Keep the date/day inputs enabled/disabled status synchronized
                    updatedDateSelector.disabled = false;
                    updatedDayNoSelector.disabled = false;
                }
            } else {
                throw new Error(
                    "Could not find '#form-content-container' in response."
                );
            }
        } catch (error) {
            console.error("Date/Day loading failed:", error);
            if (overlay) overlay.style.display = "none";
        }
    };

    // Check if ALL required ADL elements are present initially
    const isADLForm =
        initialDateSelector && initialDayNoSelector && initialPatientIdHidden && formContainer;

    if (!selectUrl || !isADLForm) {
        console.warn(
            "Date/Day Loader: Missing required ADL elements or selectUrl. Not initializing date/day change listeners."
        );
        return;
    }

    // Attach event listeners to the initial elements
    initialDateSelector.addEventListener("change", handleDateDayChange);
    initialDayNoSelector.addEventListener("change", handleDateDayChange);
};
