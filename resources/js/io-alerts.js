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
                    analyzeIntakeOutput(intakeData, analyzeUrl, csrfToken);
                }
            }, 300);
        });
    });

    async function analyzeIntakeOutput(intakeData, url, token) {
        const alertCell = document.querySelector('[data-alert-for-field="io_alert"]');
        if (!alertCell) return;

        showAlertLoading(alertCell);

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
            alertCell.innerHTML = `
                <div class="alert-box alert-red fade-in" style="height:90px;margin:2px;">
                    <span class="alert-message">Error analyzing...</span>
                </div>
            `;
        }
    }

    function displayAlert(alertCell, alertData) {
        alertCell.innerHTML = "";

        const alertBox = document.createElement("div");
        alertBox.className = "alert-box fade-in";
        alertBox.style.height = "90px";
        alertBox.style.margin = "2px";

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
    }

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
    if (document.getElementById('io-form')) {
        window.initializeIntakeOutputAlerts();
    }
});
