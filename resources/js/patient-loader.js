/**
 *
 * How it works:
 * 1. Listens for a custom "patient:selected" event on the document.
 * 2. This event is dispatched by UI components like the hybrid-dropdown.
 * 3. The event's 'detail' contains the patient ID and the URL to fetch.
 * 4. It shows a loading state and fetches the new form content.
 * 5. It replaces the old content and re-initializes all necessary scripts.
 */

// ---!! FIX: Add this check !! ---
// Check if the listener has already been attached.
if (!window.patientSelectedListenerAttached) {
    window.patientSelectedListenerAttached = true;
    console.log("[PatientLoader] Attaching patient:selected listener.");

    document.addEventListener("patient:selected", async (event) => {
        const { patientId, selectUrl } = event.detail;
        const formContainer = document.getElementById("form-content-container");
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        // Get header elements on the current page (may or may not exist)
        const patientSearchInput = document.getElementById(
            "patient_search_input"
        );
        const dateSelector = document.getElementById("date_selector");
        const dayNoSelector = document.getElementById("day_no_selector");

        // Check if this form *has* date/day selectors (e.g., ADL form)
        const isDateDayForm = dateSelector && dayNoSelector;

        if (!formContainer || !selectUrl || !patientId) {
            console.error(
                "Patient loader: Missing required data for fetch or patientId.",
                event.detail
            );
            return;
        }

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
                throw new Error(
                    `Server responded with status: ${response.status}`
                );

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
                const newDayNoSelector =
                    newHtml.getElementById("day_no_selector");

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

                // --- THIS IS THE FIX (PART 1) ---
                // Set a global flag *before* dispatching the event.
                // alert.js will check for this flag.
                window.cdssFormReloaded = true;

                // ---!! FIX 2 (See below) !! ---
                // We must manually re-initialize the dropdown that we just added
                if (window.initSearchableDropdown) {
                    console.log(
                        "[PatientLoader] Re-initializing searchable dropdown."
                    );
                    window.initSearchableDropdown();
                }
                // ---!! END FIX 2 !! ---

                // Dispatch a custom event to signal that the form content has been reloaded
                document.dispatchEvent(
                    new CustomEvent("cdss:form-reloaded", {
                        bubbles: true,
                        detail: { formContainer: formContainer },
                    })
                );
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

    // ---!! FIX: Add this 'else' block !! ---
} else {
    console.log(
        "[PatientLoader] Skipping attachment of patient:selected listener (already attached)."
    );
}
// ---!! END FIX !! ---
