/**
 * How it works:
 * 1. Finds any <select> with the class "patient-select-dropdown".
 * 2. On change, it gets the selected patient ID and the URL from 'data-select-url'.
 * 3. It finds the main content container, identified by "#form-content-container".
 * 4. It shows the form's overlay to create a "loading" state.
 * 5. It sends a fetch request to the select URL. The backend sets the session and redirects,
 * and fetch automatically follows this to get the new page HTML.
 * 6. It parses the new HTML, grabs the new content from "#form-content-container",
 * and replaces the old content with it.
 * 7. Crucially, it re-initializes itself and the 'alert.js' script on the new content.
 */
document.addEventListener("DOMContentLoaded", () => {
    // Find all patient dropdowns on the page (usually just one)
    const loaders = document.querySelectorAll(".patient-select-dropdown");
    loaders.forEach((loader) => {
        initializePatientLoader(loader);
    });
});

/**
 * Attaches the change event listener to a patient select dropdown.
 * @param {HTMLSelectElement} loader - The <select> element.
 */
function initializePatientLoader(loader) {
    // Find the main form container this dropdown is supposed to control
    const formContainer = document.getElementById("form-content-container");
    if (!formContainer) {
        console.error(
            "Patient loader: Could not find '#form-content-container'."
        );
        return;
    }

    loader.addEventListener("change", async (e) => {
        const patientId = e.target.value;
        const selectUrl = e.target.dataset.selectUrl;
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        if (!selectUrl) {
            console.error(
                "Patient loader: Dropdown is missing 'data-select-url'."
            );
            return;
        }

        // Show the loading overlay
        const overlay = formContainer.querySelector(".form-overlay");
        if (overlay) {
            overlay.style.display = "flex";
        }

        try {
            const response = await fetch(selectUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest", // Lets Laravel know it's an AJAX request
                },
                body: `patient_id=${encodeURIComponent(patientId)}`,
            });

            if (!response.ok) {
                throw new Error(
                    `Server responded with status: ${response.status}`
                );
            }

            // Get the full HTML of the redirected page
            const htmlText = await response.text();

            // Parse the new HTML to find the new content
            const parser = new DOMParser();
            const newHtml = parser.parseFromString(htmlText, "text/html");
            const newContent = newHtml.getElementById("form-content-container");

            if (newContent) {
                // Replace the old form container's content with the new one
                formContainer.innerHTML = newContent.innerHTML;

                // RE-INITIALIZE all scripts on the new content
                // 1. Re-initialize the patient loader for the new dropdown
                const newLoader = formContainer.querySelector(
                    ".patient-select-dropdown"
                );
                if (newLoader) {
                    initializePatientLoader(newLoader);
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
            // In case of error, just reload the page as a fallback
            window.location.reload();
        }
    });
}
