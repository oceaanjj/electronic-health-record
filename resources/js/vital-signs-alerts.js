/**
 * vital-signs-alerts.js
 *
 * This script handles real-time CDSS alerts for the vital signs form.
 * It listens for input changes on vital sign fields and dynamically updates
 * the corresponding alert box for that time slot.
 */

window.initializeVitalSignsAlerts = function () {
    const vitalsForm = document.getElementById('vitals-form');
    if (!vitalsForm) {
        console.warn('Vital Signs Alerts: #vitals-form not found.');
        return;
    }

    const analyzeUrl = vitalsForm.dataset.analyzeUrl; // This will be added to the form
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error('Vital Signs Alerts: Form missing "data-analyze-url" or CSRF token not found.');
        return;
    }

    const inputs = vitalsForm.querySelectorAll('.cdss-input');
    let debounceTimer;

    inputs.forEach(input => {
        input.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);

            const fieldName = e.target.dataset.fieldName; // e.g., 'temperature'
            const time = e.target.dataset.time;         // e.g., '06:00'
            const value = e.target.value.trim();

            const alertCell = document.querySelector(`[data-alert-for-time="${time}"]`);

            if (alertCell) {
                if (value === "") {
                    showDefaultNoAlerts(alertCell);
                }
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && time && value !== "") {
                    const currentAlertCell = document.querySelector(`[data-alert-for-time="${time}"]`);
                    if (currentAlertCell && !currentAlertCell.classList.contains("alert-loading")) {
                        showAlertLoading(currentAlertCell);
                    }
                    analyzeVitalSignField(fieldName, time, value, analyzeUrl, csrfToken);
                }
            }, 300);
        });
    });

    // --- Function: Analyze input with backend ---
    async function analyzeVitalSignField(fieldName, time, value, url, token) {
        const alertCell = document.querySelector(`[data-alert-for-time="${time}"]`);
        if (!alertCell) return;

        showAlertLoading(alertCell);

        // Collect all vital signs for the current time slot
        const vitalsForTime = {};
        const vitalInputsForTime = vitalsForm.querySelectorAll(`.vital-input[data-time="${time}"]`);
        vitalInputsForTime.forEach(input => {
            const currentFieldName = input.dataset.fieldName;
            vitalsForTime[currentFieldName] = input.value.trim();
        });

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                body: JSON.stringify({ time, vitals: vitalsForTime }), // Send all vitals for the time slot
            });

            if (!response.ok) throw new Error(`Server error: ${response.status}`);

            const alertData = await response.json();

            setTimeout(() => {
                displayAlert(alertCell, alertData);
            }, 150);
        } catch (error) {
            console.error("Vital Signs CDSS analysis failed:", error);
            alertCell.querySelector('.alert-box').innerHTML = `
                <div class=\"alert-loading alert-red\" style=\"height:90px;margin:2px;\">\n                    <span class=\"alert-message\">Error analyzing...</span>\n                </div>
            `;
        }
    }

    // --- Display alert content ---
    function displayAlert(alertCell, alertData) {
        const alertBoxDiv = alertCell.querySelector('.alert-box');
        if (!alertBoxDiv) return;

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
        } else {
            innerHtmlContent = `<span>${alertData.alert}</span>`;
        }

        // Update content of the inner div
        alertBoxDiv.innerHTML = innerHtmlContent;
        alertCell.onclick = null; // Reset onclick
    }

    // --- Default NO ALERTS state ---
    function showDefaultNoAlerts(alertCell) {
        const alertBoxDiv = alertCell.querySelector('.alert-box');
        if (!alertBoxDiv) return;

        // Manage classes on the parent <td> (alertCell)
        alertCell.classList.remove("alert-loading", "alert-red", "alert-orange"); // Remove loading and severity classes
        alertCell.classList.add("has-no-alert", "alert-green", "fade-in"); // Add no alerts state, green color, and fade-in

        // Update content of the inner div
        alertBoxDiv.innerHTML = `
            <span class=\"alert-message opacity-80 text-white text-center font-semibold uppercase\">\n                NO ALERTS\n            </span>
        `;
        alertCell.onclick = null;
    }

    // --- Loading spinner (continuous) ---
    function showAlertLoading(alertCell) {
        const alertBoxDiv = alertCell.querySelector('.alert-box');
        if (!alertBoxDiv) return;

        // Manage classes on the parent <td> (alertCell)
        alertCell.classList.remove("has-no-alert", "alert-red", "alert-orange", "alert-green", "fade-in"); // Remove all previous state and animation classes
        alertCell.classList.add("alert-loading"); // Add loading state class

        // Update content of the inner div
        alertBoxDiv.innerHTML = `
            <div class="alert-loading">
                <div class="loading-spinner"></div>
                <span>Analyzing...</span>
            </div>
        `;
        alertCell.onclick = null;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.initializeVitalSignsAlerts();
});
