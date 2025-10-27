/**
 *
 * How it works:
 * 1. Listens for a custom "patient:selected" event on the document.
 * 2. This event is dispatched by UI components like the hybrid-dropdown.
 * 3. The event's 'detail' contains the patient ID and the URL to fetch.
 * 4. It shows a loading state and fetches the new form content.
 * 5. It replaces the old content and re-initializes all necessary scripts.
 */

document.addEventListener("patient:selected", async (event) => {
    const { patientId, selectUrl } = event.detail;
    const formContainer = document.getElementById("form-content-container");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    // Get header elements on the current page (may or may not exist)
    const patientSearchInput = document.getElementById("patient_search_input");
    const dateSelector = document.getElementById("date_selector");
    const dayNoSelector = document.getElementById("day_no_selector");

    // Check if this form *has* date/day selectors (e.g., ADL form)
    const isDateDayForm = dateSelector && dayNoSelector;

    if (!formContainer || !selectUrl || !patientId) {
        console.error(
            "Patient loader: Missing required data for fetch or patientId.",
            event.detail
        );
        // Safely disable date/day inputs if they exist
        if (dateSelector) dateSelector.disabled = true;
        if (dayNoSelector) dayNoSelector.disabled = true;
        return;
    }

    // --- Action: Reset Date/Day selectors immediately on patient change ---
    if (isDateDayForm) {
        // Clear value to indicate loading/reset
        dateSelector.value = "";
        dayNoSelector.value = "";
        // Disable temporarily until the new response enables it and sets the new admission date
        dateSelector.disabled = true;
        dayNoSelector.disabled = true;
    }
    // --- End Reset ---

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
            },
            body: `patient_id=${encodeURIComponent(patientId)}`,
        });

        if (!response.ok)
            throw new Error(`Server responded with status: ${response.status}`);

        const htmlText = await response.text();
        const parser = new DOMParser();
        const newHtml = parser.parseFromString(htmlText, "text/html");

        // --- Step 1: Update Header Elements (Date, Day) from the Response ---

        // 1. Update patient search input
        const newPatientSearchInput = newHtml.getElementById(
            "patient_search_input"
        );
        if (patientSearchInput && newPatientSearchInput) {
            patientSearchInput.value = newPatientSearchInput.value;
        }

        // 2. Only process Date/Day if the selectors exist on the current page
        if (isDateDayForm) {
            const newDateSelector = newHtml.getElementById("date_selector");
            const newDayNoSelector = newHtml.getElementById("day_no_selector");

            if (newDateSelector) {
                // Set the value based on the admission date provided by the server response
                dateSelector.value = newDateSelector.value;
                dateSelector.disabled = false;
            }
            if (newDayNoSelector) {
                // Set the default day value (Day 1) provided by the server response
                dayNoSelector.value = newDayNoSelector.value;
                dayNoSelector.disabled = false;
            }
        }

        // --- Step 2: Replace the Main Form Content ---
        const newContent = newHtml.getElementById("form-content-container");

        if (newContent) {
            formContainer.innerHTML = newContent.innerHTML;

            // --- Step 3: Re-initialize Scripts ---

            // Re-initialize the CDSS alerts for the new form
            const newCdssForm = formContainer.querySelector(".cdss-form");
            if (typeof window.initializeCdssForForm === "function") {
                window.initializeCdssForForm(newCdssForm);
            }

            // Re-initialize the generic Date/Day loader, only if this is a Date/Day form
            if (
                isDateDayForm &&
                typeof window.initializeDateDayLoader === "function"
            ) {
                window.initializeDateDayLoader(selectUrl);
            }
        } else {
            throw new Error(
                "Could not find '#form-content-container' in response."
            );
        }
    } catch (error) {
        console.error("Patient loading failed:", error);
        window.location.reload(); // Fallback to a full refresh on error
    }
});
