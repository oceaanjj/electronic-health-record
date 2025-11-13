/**
 * patient-loader.js
 * Handles the AJAX loading of form content when a patient is selected via the dropdown.
 */

if (!window.patientSelectedListenerAttached) {
    window.patientSelectedListenerAttached = true;
    console.log("[PatientLoader] Attaching patient:selected listener.");

    document.addEventListener("patient:selected", async (event) => {
        const { patientId, selectUrl } = event.detail;
        const formContainer = document.getElementById("form-content-container");
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        // Get existing header elements
        const dropdownContainer = document.querySelector(
            ".searchable-dropdown"
        );
        const dateSelector = document.getElementById("date_selector");
        const dayNoSelector = document.getElementById("day_no_selector");

        // Check if we are on a page that has date/day selectors (like Vital Signs or ADL)
        const isDateDayForm =
            dateSelector && dayNoSelector && dropdownContainer;

        if (!formContainer || !selectUrl || !patientId) {
            console.error(
                "Patient loader: Missing required data.",
                event.detail
            );
            return;
        }

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

            if (!response.ok) throw new Error(`Status: ${response.status}`);

            const htmlText = await response.text();
            const parser = new DOMParser();
            const newHtml = parser.parseFromString(htmlText, "text/html");

            // --- Step 1: Update Header Elements (Search, Date, Day) ---

            // 1a. Update Search Input (Visually keep the name)
            const newPatientSearchInput = newHtml.getElementById(
                "patient_search_input"
            );
            const patientSearchInput = document.getElementById(
                "patient_search_input"
            );
            if (patientSearchInput && newPatientSearchInput) {
                patientSearchInput.value = newPatientSearchInput.value;
            }

            if (isDateDayForm) {
                const newDropdownContainer = newHtml.querySelector(
                    ".searchable-dropdown"
                );
                const newDateSelector = newHtml.getElementById("date_selector");
                const newDayNoSelector =
                    newHtml.getElementById("day_no_selector");

                // 1b. Update Admission Date Dataset (Crucial for sync script)
                if (
                    newDropdownContainer &&
                    newDropdownContainer.dataset.admissionDate
                ) {
                    dropdownContainer.dataset.admissionDate =
                        newDropdownContainer.dataset.admissionDate;
                }

                // 1c. Update Date Selector
                if (newDateSelector) {
                    dateSelector.disabled = false;
                    // Directly take the value from the server response
                    dateSelector.value = newDateSelector.value;
                }

                // 1d. Update Day Selector (The Fix)
                if (newDayNoSelector) {
                    dayNoSelector.disabled = false;
                    // Replace options entirely
                    dayNoSelector.innerHTML = newDayNoSelector.innerHTML;

                    // Directly take the value that the server marked as selected
                    // The controller calculates the latest day, so newDayNoSelector.value IS the latest day.
                    dayNoSelector.value = newDayNoSelector.value;

                    console.log(
                        `[PatientLoader] Set Day No to: ${dayNoSelector.value}`
                    );
                }
            }

            // --- Step 2: Replace Main Form Content ---
            // The form content returned by the server corresponds to the Date/Day set above
            const newContent = newHtml.getElementById("form-content-container");
            if (newContent) {
                formContainer.innerHTML = newContent.innerHTML;

                // --- Step 3: Re-initialize Scripts ---
                window.cdssFormReloaded = true;

                if (window.initializeSearchableDropdown) {
                    window.initializeSearchableDropdown();
                }

                // Re-initialize Sync Script
                if (window.initializeVitalSignsDateSync) {
                    console.log(
                        "[PatientLoader] Re-initializing vital signs date sync."
                    );
                    // Wait for DOM to paint before re-initializing sync to prevent calculation overrides
                    setTimeout(() => {
                        window.initializeVitalSignsDateSync();
                    }, 50);
                }

                // Re-initialize Date/Day Loader for other forms (ADL etc)
                if (window.initializeDateDayLoader) {
                    const headerDropdown = document.querySelector(
                        ".searchable-dropdown"
                    );
                    const newSelectUrl = headerDropdown
                        ? headerDropdown.dataset.selectUrl
                        : selectUrl;
                    window.initializeDateDayLoader(newSelectUrl);
                }

                // Dispatch event for other listeners
                document.dispatchEvent(
                    new CustomEvent("cdss:form-reloaded", {
                        bubbles: true,
                        detail: { formContainer: formContainer },
                    })
                );
            }
        } catch (error) {
            console.error("Patient loading failed:", error);
            // Fallback to full reload if AJAX fails
            window.location.reload();
        }
    });
}
