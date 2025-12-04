document.addEventListener("DOMContentLoaded", function () {
    const patientIdHiddenInput = document.getElementById("patient_id_hidden");
    let alertTimeout;

    // 1. Create the alert message element (Same as before)
    const alertMessage = document.createElement("div");
    alertMessage.id = "patient-selection-alert";
    alertMessage.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #dc3545;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        z-index: 9999;
        display: none;
        font-family: sans-serif;
        font-size: 16px;
        text-align: center;
        pointer-events: none;
    `;
    alertMessage.textContent = "Please select a patient first!.";
    document.body.appendChild(alertMessage);

    function showAlertDialog() {
        clearTimeout(alertTimeout);
        alertMessage.style.display = "block";
        alertTimeout = setTimeout(() => {
            alertMessage.style.display = "none";
        }, 3000);
    }

    function isPatientSelected() {
        return patientIdHiddenInput && patientIdHiddenInput.value.trim() !== "";
    }

    // 2. Event Delegation for the Overlay
    // This works even if the overlay is added/removed dynamically by the dropdown script
    document.body.addEventListener("click", function (event) {
        if (event.target.classList.contains("trigger-patient-alert")) {
            event.preventDefault();
            event.stopPropagation();

            // Trigger alert if no patient is actually selected
            if (!isPatientSelected()) {
                showAlertDialog();
            }
        }
    });
});
