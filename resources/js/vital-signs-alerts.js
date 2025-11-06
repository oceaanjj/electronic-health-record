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
                } else {
                    if (!alertCell.classList.contains("alert-loading")) {
                        showAlertLoading(alertCell);
                    }
                }
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && time && value !== "") {
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

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                body: JSON.stringify({ fieldName, time, value }), // Send fieldName, time, and value
            });

            if (!response.ok) throw new Error(`Server error: ${response.status}`);

            const alertData = await response.json();

            setTimeout(() => {
                displayAlert(alertCell, alertData);
            }, 150);
        } catch (error) {
            console.error("Vital Signs CDSS analysis failed:", error);
            alertCell.innerHTML = `
                <div class="alert-box alert-red fade-in" style="height:90px;margin:2px;">
                    <span class="alert-message">Error analyzing...</span>
                </div>
            `;
        }
    }

    // --- Display alert content ---
    function displayAlert(alertCell, alertData) {
        alertCell.innerHTML = "";

        const alertBox = document.createElement("div");
        alertBox.className = "alert-box fade-in";
        alertBox.style.height = "90px";
        alertBox.style.margin = "2px";

        // Set color by severity
        let colorClass = "alert-green";
        if (alertData.severity === "CRITICAL") colorClass = "alert-red";
        else if (alertData.severity === "WARNING") colorClass = "alert-orange";
        else if (alertData.severity === "INFO") colorClass = "alert-green";

        alertBox.classList.add(colorClass);

        const alertMessage = document.createElement("div");
        alertMessage.className = "alert-message";
        alertMessage.style.padding = "8px";

        if (alertData.alert?.toLowerCase().includes("no findings")) {
            alertBox.classList.add("has-no-alert");
            alertMessage.innerHTML = `
                <span class="text-white text-center uppercase font-semibold opacity-80">
                    NO FINDINGS
                </span>
            `;
        } else {
            alertMessage.innerHTML = `<span>${alertData.alert}</span>`;
        }

        alertBox.appendChild(alertMessage);
        alertCell.appendChild(alertBox);

        if (!alertData.alert?.toLowerCase().includes("no findings")) {
            // No modal for vital signs alerts for now, as it's a combined alert
        }
    }

    // --- Default NO ALERTS state ---
    function showDefaultNoAlerts(alertCell) {
        alertCell.className = "alert-box has-no-alert alert-green fade-in";
        alertCell.style.height = "90px";
        alertCell.style.margin = "2.8px";
        alertCell.innerHTML = `
            <span class="alert-message opacity-80 text-white text-center font-semibold uppercase">
                NO ALERTS
            </span>
        `;
        alertCell.onclick = null;
    }

    // --- Loading spinner (continuous) ---
    function showAlertLoading(alertCell) {
        alertCell.className = "alert-box alert-green alert-loading fade-in";
        alertCell.style.height = "90px";
        alertCell.style.margin = "2px";
        alertCell.innerHTML = `
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
