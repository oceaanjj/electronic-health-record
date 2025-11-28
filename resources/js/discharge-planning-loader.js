function initializeDischargePlanningForm() {
    const formContainer = document.getElementById('form-content-container');
    if (!formContainer) return;

    const fieldset = formContainer.querySelector('fieldset');
    const textareas = formContainer.querySelectorAll('textarea.notepad-lines');
    const hiddenPatientIdInput = document.querySelector('input[name="patient_id"][type="hidden"]');

    function clearInputs() {
        textareas.forEach(textarea => {
            textarea.value = '';
        });
    }

    function toggleForm(enable) {
        if (fieldset) {
            fieldset.disabled = !enable;
        }
    }

    // Define the handler for the 'patient:cleared' event
    const handlePatientCleared = () => {
        // Check if we are still on the correct page before acting
        if (document.querySelector('form[action*="discharge-planning"]')) {
            clearInputs();
            toggleForm(false);
        }
    };

    // Attach the event listener, ensuring it's only attached once
    document.removeEventListener('patient:cleared', window.handleDischargePatientCleared);
    window.handleDischargePatientCleared = handlePatientCleared; // Store it globally to remove it later
    document.addEventListener('patient:cleared', window.handleDischargePatientCleared);


    // Initial state check
    if (!hiddenPatientIdInput || !hiddenPatientIdInput.value) {
        toggleForm(false);
    } else {
        toggleForm(true);
    }
}

// This listener is attached once and handles the AJAX loading
document.addEventListener("patient:selected", async (event) => {
    // Only act if we are on the discharge planning page
    if (!document.querySelector('form[action*="discharge-planning"]')) {
        return;
    }

    const { patientId, selectUrl } = event.detail;
    const formContainer = document.getElementById("form-content-container");
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    if (!formContainer || !selectUrl || !patientId) {
        return;
    }

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

        if (!response.ok) throw new Error(`Server responded with status: ${response.status}`);

        const htmlText = await response.text();
        const parser = new DOMParser();
        const newHtml = parser.parseFromString(htmlText, "text/html");
        const newContent = newHtml.getElementById("form-content-container");

        if (newContent) {
            formContainer.innerHTML = newContent.innerHTML;
            // Re-initialize the form logic for the new content
            initializeDischargePlanningForm();
            // Re-initialize the dropdown
            if (typeof window.initSearchableDropdown === "function") {
                window.initSearchableDropdown();
            }
        } else {
            throw new Error("Could not find '#form-content-container' in response.");
        }
    } catch (error) {
        console.error("Patient loading failed:", error);
        window.location.reload();
    }
});


// Initial setup on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeDischargePlanningForm();
});


