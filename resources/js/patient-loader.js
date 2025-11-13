/**
 *
 * How it works:
 * 1. Listens for a custom "patient:selected" event on the document.
 * 2. Shows loading state and fetches new content.
 * 3. Updates header elements (Date, Day, Search) and Form Content.
 * 4. Re-initializes scripts.
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

        // Check if we are on a page that has date/day selectors
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

            // --- Step 1: Update Header Elements ---
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

                // FIX: Ensure admission date dataset is updated
                if (
                    newDropdownContainer &&
                    newDropdownContainer.dataset.admissionDate
                ) {
                    dropdownContainer.dataset.admissionDate =
                        newDropdownContainer.dataset.admissionDate;
                }

                // Update Date Selector
                if (newDateSelector) {
                    dateSelector.value = newDateSelector.value;
                    dateSelector.disabled = false;
                }

                // FIX: Robust Day Selector Update
                if (newDayNoSelector) {
                    // 1. Swap the options HTML
                    dayNoSelector.innerHTML = newDayNoSelector.innerHTML;
                    dayNoSelector.disabled = false;

                    // 2. Find the correct value
                    const selectedOpt =
                        newDayNoSelector.querySelector("option[selected]");
                    if (selectedOpt) {
                        dayNoSelector.value = selectedOpt.value;
                    } else {
                        // Fallback: Default to the LATEST day (last option), not Day 1
                        const options =
                            newDayNoSelector.querySelectorAll("option");
                        if (options.length > 0) {
                            dayNoSelector.value =
                                options[options.length - 1].value;
                        }
                    }
                    console.log(
                        `[PatientLoader] Updated Day No to: ${dayNoSelector.value}`
                    );
                }
            }

            // --- Step 2: Replace Main Form Content ---
            const newContent = newHtml.getElementById("form-content-container");
            if (newContent) {
                formContainer.innerHTML = newContent.innerHTML;

                // --- Step 3: Re-initialize Scripts ---
                window.cdssFormReloaded = true;

                if (window.initializeSearchableDropdown) {
                    window.initializeSearchableDropdown();
                }

                // FIX: Re-initialize Sync Script
                if (window.initializeVitalSignsDateSync) {
                    console.log(
                        "[PatientLoader] Re-initializing vital signs date sync."
                    );
                    // Small timeout to ensure DOM updates have settled before script runs
                    setTimeout(() => {
                        window.initializeVitalSignsDateSync();
                    }, 50);
                }

                if (window.initializeDateDayLoader) {
                    const headerDropdown = document.querySelector(
                        ".searchable-dropdown"
                    );
                    const newSelectUrl = headerDropdown
                        ? headerDropdown.dataset.selectUrl
                        : selectUrl;
                    window.initializeDateDayLoader(newSelectUrl);
                }

                document.dispatchEvent(
                    new CustomEvent("cdss:form-reloaded", {
                        bubbles: true,
                        detail: { formContainer: formContainer },
                    })
                );
            }
        } catch (error) {
            console.error("Patient loading failed:", error);
            window.location.reload();
        }
    });
}
