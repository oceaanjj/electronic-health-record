/**
 * io-alerts.js
 *
 * This script handles real-time CDSS alerts for the intake and output form.
 * It listens for input changes on I/O fields and dynamically updates
 * the alert box.
 */

window.initializeIntakeOutputAlerts = function () {
    const ioForm = document.getElementById('io-form');
    if (!ioForm) {
        console.warn('I/O Alerts: #io-form not found.');
        return;
    }

    const analyzeUrl = ioForm.dataset.analyzeUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error('I/O Alerts: Form missing "data-analyze-url" or CSRF token not found.');
        return;
    }

    const inputs = ioForm.querySelectorAll('.cdss-input');
    let debounceTimer;

    inputs.forEach(input => {
        input.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const oralIntakeInput = ioForm.querySelector('[name="oral_intake"]');
                const ivFluidsInput = ioForm.querySelector('[name="iv_fluids_volume"]');
                const urineOutputInput = ioForm.querySelector('[name="urine_output"]');

                if (oralIntakeInput && ivFluidsInput && urineOutputInput) {
                    const intakeData = {
                        oral_intake: oralIntakeInput.value.trim(),
                        iv_fluids_volume: ivFluidsInput.value.trim(),
                        urine_output: urineOutputInput.value.trim(),
                    };

                    const alertCell = document.querySelector('[data-alert-for-field="io_alert"]');
                    if (!alertCell) return;

                    // Check if all fields are empty
                    const allEmpty = Object.values(intakeData).every(val => val === '');

                    if (allEmpty) {
                        showDefaultNoAlerts(alertCell);
                    } else {
                        if (!alertCell.classList.contains("alert-loading")) {
                            showAlertLoading(alertCell);
                        }
                        analyzeIntakeOutput(intakeData, analyzeUrl, csrfToken);
                    }
                }
            }, 300);
        });
    });

    async function analyzeIntakeOutput(intakeData, url, token) {
        const alertCell = document.querySelector('[data-alert-for-field="io_alert"]');
        if (!alertCell) return;

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                body: JSON.stringify(intakeData),
            });

            if (!response.ok) throw new Error(`Server error: ${response.status}`);

            const alertData = await response.json();

            setTimeout(() => {
                displayAlert(alertCell, alertData);
            }, 150);
        } catch (error) {
            console.error("I/O CDSS analysis failed:", error);
            displayAlert(alertCell, { alert: 'Error analyzing...', severity: 'CRITICAL' });
        }
    }

    function showAlertLoading(alertCell) {
        const alertBoxContentDiv = alertCell.querySelector('.alert-box-content');
        if (!alertBoxContentDiv) return;

        // Manage classes on the parent <td> (alertCell)
        alertCell.classList.remove("has-no-alert", "alert-red", "alert-orange", "alert-green", "fade-in"); // Remove all previous state and animation classes
        alertCell.classList.add("alert-loading"); // Add loading state class

        // Update content of the inner div
        alertBoxContentDiv.innerHTML = `
            <div class=\"alert-loading\">\n                <div class=\"loading-spinner\"></div>\n                <span>Analyzing...</span>\n            </div>
        `;
        alertCell.onclick = null;
    }

    function displayAlert(alertCell, alertData) {
        const alertBoxContentDiv = alertCell.querySelector('.alert-box-content');
        if (!alertBoxContentDiv) return;

        // Manage classes on the parent <td> (alertCell)
        alertCell.classList.remove("alert-loading", "has-no-alert", "alert-red", "alert-orange", "alert-green"); // Remove previous state classes

        // Set color by severity
        let colorClass = "alert-green";
        if (alertData.severity === "CRITICAL") colorClass = "alert-red";
        else if (alertData.severity === "WARNING") colorClass = "alert-orange";
        else if (alertData.severity === "INFO") colorClass = "alert-green";

        alertCell.classList.add(colorClass, "fade-in"); // Add color class and fade-in

        let innerHtmlContent;
        if (alertData.alert?.toLowerCase().includes("no findings")) {
            alertCell.classList.add("has-no-alert");
            innerHtmlContent = `
                <span class="alert-message opacity-80 text-white text-center font-semibold uppercase">\n                    NO FINDINGS\n                </span>
            `;
            alertCell.onclick = null; // No modal for "No Findings"
        } else {
            innerHtmlContent = `<span>${alertData.alert}</span>`;
            alertCell.onclick = () => openAlertModal(alertData); // Add click listener for modal
        }

        // Update content of the inner div
        alertBoxContentDiv.innerHTML = innerHtmlContent;
    }

    function showDefaultNoAlerts(alertCell) {
        const alertBoxContentDiv = alertCell.querySelector('.alert-box-content');
        if (!alertBoxContentDiv) return;

        // Manage classes on the parent <td> (alertCell)
        alertCell.classList.remove("alert-loading", "alert-red", "alert-orange"); // Remove loading and severity classes
        alertCell.classList.add("has-no-alert", "alert-green", "fade-in"); // Add no alerts state, green color, and fade-in

        // Update content of the inner div
        alertBoxContentDiv.innerHTML = `
            <span class=\"alert-message opacity-80 text-white text-center font-semibold uppercase\">\n                NO ALERTS\n            </span>
        `;
        alertCell.onclick = null;
    }


    // --- Modal popup for details ---
    function openAlertModal(alertData) {
        const overlay = document.createElement("div");
        overlay.className = "alert-modal-overlay";

        const modal = document.createElement("div");
        modal.className = "alert-modal fade-in";
        modal.innerHTML = `
            <button class=\"close-btn\">&times;</button>
            <h2>Alert Details</h2>
            <p>${alertData.alert}</p>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        const closeModal = () => overlay.remove();
        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) closeModal();
        });
        modal.querySelector(".close-btn").addEventListener("click", closeModal);
    }

    // --- Fade-in animation ---
    const style = document.createElement("style");
    style.textContent = `
        .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
};

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('io-form')) {
        window.initializeIntakeOutputAlerts();
    }
});
