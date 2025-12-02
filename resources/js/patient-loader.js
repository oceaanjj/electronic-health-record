/**
 * patient-loader.js
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

            // 1. Replace Main Form Content (This includes Date/Day/Search headers)
            const newContent = newHtml.getElementById("form-content-container");

            if (newContent) {
                formContainer.innerHTML = newContent.innerHTML;

                // 2. Re-initialize specific scripts if needed
                // Note: initializeVitalSignsDateSync is now delegated, so it doesn't strictly
                // need re-running, but we keep it to ensure global listeners are active.

                window.cdssFormReloaded = true;

                if (window.initializeSearchableDropdown) {
                    window.initializeSearchableDropdown();
                }

                if (window.initializeVitalSignsDateSync) {
                    window.initializeVitalSignsDateSync();
                }

                if (window.initializeDateDayLoader) {
                    // For other forms that use the generic loader
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
