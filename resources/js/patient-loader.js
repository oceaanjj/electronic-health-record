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

    if (!formContainer || !selectUrl || !patientId) {
        console.error(
            "Patient loader: Missing required data for fetch.",
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
            throw new Error(`Server responded with status: ${response.status}`);

        const htmlText = await response.text();
        const parser = new DOMParser();
        const newHtml = parser.parseFromString(htmlText, "text/html");
        const newContent = newHtml.getElementById("form-content-container");

        if (newContent) {
            formContainer.innerHTML = newContent.innerHTML;

            // Re-initialize all necessary scripts on the new content
            // 1. Re-initialize the hybrid dropdown itself
            const newDropdown = document.querySelector(
                ".hybrid-dropdown-container"
            );
            if (newDropdown && typeof initializeHybridDropdown === "function") {
                initializeHybridDropdown(newDropdown);
            }

            // 2. Re-initialize the CDSS alerts for the new form
            const newCdssForm = formContainer.querySelector(".cdss-form");
            if (
                newCdssForm &&
                typeof window.initializeCdssForForm === "function"
            ) {
                window.initializeCdssForForm(newCdssForm);
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
