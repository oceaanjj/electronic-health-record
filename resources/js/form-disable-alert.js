document.addEventListener("DOMContentLoaded", function () {
    let alertTimeout;

    const alertMessage = document.createElement("div");
    alertMessage.id = "patient-selection-alert";

    // Updated CSS for Right Alignment + Animation properties
    alertMessage.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px; /* Moved to Right */
        background-color: #dc3545;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 10000;
        font-family: sans-serif;
        font-size: 16px;
        text-align: center;
        pointer-events: none;
        
        opacity: 0;
        transform: translateY(20px); 
        transition: opacity 0.2s ease, transform 0.2s ease;
        visibility: hidden; /* Prevents clicking when invisible */
    `;

    alertMessage.textContent = "Please select a patient first!";
    document.body.appendChild(alertMessage);

    function showAlertDialog() {
        // Clear existing timeout if the user clicks rapidly
        clearTimeout(alertTimeout);

        alertMessage.style.visibility = "visible";

        requestAnimationFrame(() => {
            alertMessage.style.opacity = "1";
            alertMessage.style.transform = "translateY(0)"; // Pop up to normal position
        });

        // timeout to Fade Out
        alertTimeout = setTimeout(() => {
            alertMessage.style.opacity = "0";
            alertMessage.style.transform = "translateY(20px)"; // Slide back down

            setTimeout(() => {
                if (alertMessage.style.opacity === "0") {
                    alertMessage.style.visibility = "hidden";
                }
            }, 200);
        }, 2000);
    }

    function isPatientSelected() {
        const patientIdHiddenInput =
            document.getElementById("patient_id_hidden");
        return patientIdHiddenInput && patientIdHiddenInput.value.trim() !== "";
    }

    // Event Delegation for the Overlay
    document.body.addEventListener("click", function (event) {
        if (
            event.target.classList.contains("trigger-patient-alert") ||
            event.target.closest(".trigger-patient-alert")
        ) {
            event.preventDefault();
            event.stopPropagation();

            if (!isPatientSelected()) {
                showAlertDialog();
            }
        }
    });
});
