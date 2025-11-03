document.addEventListener("DOMContentLoaded", function () {
    const patientIdHidden = document.getElementById("patient_id_hidden");
    const generateReportButton = document.querySelector(
        '#reportForm button[type="submit"]'
    );

    function toggleGenerateReportButton() {
        if (patientIdHidden.value) {
            generateReportButton.removeAttribute("disabled");
            generateReportButton.classList.remove(
                "opacity-50",
                "cursor-not-allowed"
            );
        } else {
            generateReportButton.setAttribute("disabled", "disabled");
            generateReportButton.classList.add(
                "opacity-50",
                "cursor-not-allowed"
            );
        }
    }

    // Initial state
    toggleGenerateReportButton();

    // Listen for changes in the hidden patient ID input
    // This assumes searchable-dropdown.js updates this hidden input
    const observer = new MutationObserver(toggleGenerateReportButton);
    observer.observe(patientIdHidden, {
        attributes: true,
        attributeFilter: ["value"],
    });
});
