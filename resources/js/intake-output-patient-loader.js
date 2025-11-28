/**
 *
 * How it works:
 * 1. Listens for a custom "patient:selected" event on the document.
 * 2. This event is dispatched by UI components like the searchable-dropdown.
 * 3. The event's 'detail' contains the patient ID and the URL to fetch.
 * 4. It fetches the new form content.
 * 5. It replaces the old content and re-initializes all necessary scripts.
 */

document.addEventListener("patient:selected", async (event) => {
    const { patientId, selectUrl } = event.detail;
    const formContainer = document.getElementById("form-content-container");
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!formContainer || !selectUrl || !patientId) {
        console.error(
            "Intake/Output Patient Loader: Missing required data for fetch or patientId.",
            event.detail
        );
        return;
    }

    try {
        const response = await fetch(selectUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
                "X-Fetch-Form-Content": "true", // Custom header to request full form content
            },
            body: `patient_id=${encodeURIComponent(patientId)}`,
        });

        if (!response.ok)
            throw new Error(`Server responded with status: ${response.status}`);

        const htmlText = await response.text();
        const parser = new DOMParser();
        const newHtml = parser.parseFromString(htmlText, "text/html");

        // --- Step 1: Replace the Main Form Content ---
        const newContent = newHtml.getElementById("form-content-container");

        if (newContent) {
            formContainer.innerHTML = newContent.innerHTML;

            // --- START: "CLONE" FIX ---
            // This "clone" logic is CORRECT and NECESSARY for this page.
            // It strips old event listeners from the dropdown
            // which is outside the reloaded content.

            // 1. Find the dropdown (which still has old, buggy listeners).
            const oldDropdown = document.querySelector(".searchable-dropdown");
            if (oldDropdown) {
                // 2. Clone it. A clone does NOT copy event listeners.
                const newDropdown = oldDropdown.cloneNode(true);

                // 3. Remove the 'initialized' flag from the clone.
                delete newDropdown.dataset.initialized;

                // 4. Replace the old dropdown with the clean clone.
                oldDropdown.parentNode.replaceChild(newDropdown, oldDropdown);
            }
            // --- END: "CLONE" FIX ---

            // --- Step 2: Re-initialize Scripts ---
            // Now, when the loop runs, initializeSearchableDropdown
            // will find the clean clone and attach the NEW, fixed listeners.
            if (
                window.pageInitializers &&
                Array.isArray(window.pageInitializers)
            ) {
                console.log("Re-initializing scripts...");
                window.pageInitializers.forEach((init) => {
                    if (typeof init === "function") {
                        init();
                    }
                });
            } else {
                // Fallback if global array isn't set (though it should be)
                console.warn(
                    "window.pageInitializers not found, running fallback initializers."
                );

                // --- START: TYPO FIX ---
                if (typeof window.initSearchableDropdown === "function") {
                    window.initSearchableDropdown(); // Fixed: init
                }
                // --- END: TYPO FIX ---

                if (
                    typeof window.initializeIntakeOutputDataLoader ===
                    "function"
                ) {
                    window.initializeIntakeOutputDataLoader();
                }
                if (typeof window.intakeOutputCdss === "function") {
                    window.intakeOutputCdss();
                } else if (
                    typeof window.intakeOutputCdss?.init === "function"
                ) {
                    window.intakeOutputCdss.init();
                }
            }

            // Dispatch a custom event to signal that the form content has been reloaded
            document.dispatchEvent(
                new CustomEvent("cdss:form-reloaded", {
                    bubbles: true,
                    detail: { formContainer: formContainer },
                })
            );
        } else {
            throw new Error(
                "Intake/Output Patient Loader: Could not find '#form-content-container' in response."
            );
        }
    } catch (error) {
        console.error("Intake/Output Patient loading failed:", error);
        window.location.reload(); // Fallback to a full refresh on error
    }
});
