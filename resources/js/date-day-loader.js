/**
 * Dynamic Date & Day Loader for ADL Form
 *
 * How it works:
 * 1. Listens for a 'change' event on the date and day selectors.
 * 2. Fetches the patient ID, selected date, and day number from the form.
 * 3. Sends an AJAX POST request to the controller to get the updated form content.
 * 4. Replaces the old form content with the new content without a page reload.
 * 5. Re-initializes necessary scripts (like CDSS alerts) on the newly loaded content.
 */
const initializeDateDayLoader = () => {
    const formContainer = document.getElementById("form-content-container");
    if (!formContainer) return;

    const dateSelector = formContainer.querySelector("#date_selector");
    const daySelector = formContainer.querySelector("#day_no");
    const dateDayForm = formContainer.querySelector("#date-day-select-form");

    if (!dateSelector || !daySelector || !dateDayForm) {
        return;
    }

    const selectUrl = dateDayForm.dataset.selectUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    const handleSelectionChange = async () => {
        const patientIdInput = dateDayForm.querySelector(
            'input[name="patient_id"]'
        );
        if (!patientIdInput || !patientIdInput.value) {
            // Don't proceed if no patient is selected
            return;
        }

        const patientId = patientIdInput.value;
        const date = dateSelector.value;
        const dayNo = daySelector.value;

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
            const newContent = newHtml.getElementById("form-content-container");

            if (newContent) {
                formContainer.innerHTML = newContent.innerHTML;

                // Re-initialize all necessary scripts on the new content
                // 1. Re-initialize the CDSS alerts for the new form
                const newCdssForm = formContainer.querySelector(".cdss-form");
                if (
                    newCdssForm &&
                    typeof window.initializeCdssForForm === "function"
                ) {
                    window.initializeCdssForForm(newCdssForm);
                }

                // 2. Re-initialize this date/day loader itself for the new content
                initializeDateDayLoader();
            } else {
                throw new Error(
                    "Could not find '#form-content-container' in response."
                );
            }
        } catch (error) {
            console.error("ADL Date/Day loading failed:", error);
            window.location.reload(); // Fallback to a full refresh on error
        }
    };

    dateSelector.addEventListener("change", handleSelectionChange);
    daySelector.addEventListener("change", handleSelectionChange);
};

// Make the function globally accessible so patient-loader.js can call it
window.initializeDateDayLoader = initializeDateDayLoader;

// Run on the initial page load
document.addEventListener("DOMContentLoaded", () => {
    window.initializeDateDayLoader();
});
