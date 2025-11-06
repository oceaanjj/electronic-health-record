/**
 * intake-output-cdss.js
 *
 * This script handles real-time CDSS alerts for the intake and output form,
 * leveraging the alert.js framework.
 */

(function() {
    let debounceTimer;

    function initializeIntakeOutputCdss() {
        const ioForm = document.getElementById('io-form');
        if (!ioForm) {
            console.warn('Intake/Output CDSS: #io-form not found.');
            return;
        }

        const analyzeUrl = ioForm.dataset.analyzeUrl;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        if (!analyzeUrl || !csrfToken) {
            console.error('Intake/Output CDSS: Form missing "data-analyze-url" or CSRF token not found.');
            return;
        }

        const inputs = ioForm.querySelectorAll('.cdss-input');
        const alertCell = document.querySelector('[data-alert-for="io_alert"]');
        const alertBoxDiv = alertCell ? alertCell : null;

        if (!alertBoxDiv) {
            console.warn('Intake/Output CDSS: Alert box div with data-alert-for="io_alert" not found.');
            return;
        }

        // --- Local function: Default NO ALERTS state ---
        function showDefaultNoAlertsLocal(alertBoxDiv) {
            alertBoxDiv.classList.remove("alert-loading", "alert-red", "alert-orange", "alert-green"); // Remove loading and severity classes
            alertBoxDiv.classList.add("has-no-alert", "alert-green"); // Add no alerts state, green color
            alertBoxDiv.innerHTML = `
                <span class="opacity-70 text-white font-semibold">NO ALERTS</span>
            `;
            alertBoxDiv.onclick = null;
        }

        // --- Local function: Loading spinner (continuous) ---
        function showAlertLoadingLocal(alertBoxDiv) {
            alertBoxDiv.classList.remove("has-no-alert", "alert-red", "alert-orange", "alert-green"); // Remove all previous state and animation classes
            alertBoxDiv.classList.add("alert-loading"); // Add loading state class
            alertBoxDiv.innerHTML = `
                <div class="alert-message" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <div class="loading-spinner"></div>
                    <span>Analyzing...</span>
                </div>
            `;
            alertBoxDiv.onclick = null;
        }

        // --- Local function: Display alert content ---
        function displayAlertLocal(alertBoxDiv, alertData) {
            alertBoxDiv.classList.remove("alert-loading", "has-no-alert", "alert-red", "alert-orange", "alert-green"); // Remove previous state classes

            // Set color by severity
            let colorClass = "alert-green";
            if (alertData.severity === "CRITICAL") colorClass = "alert-red";
            else if (alertData.severity === "WARNING") colorClass = "alert-orange";
            else if (alertData.severity === "INFO") colorClass = "alert-green";

            alertBoxDiv.classList.add(colorClass);

            let innerHtmlContent;
            if (alertData.alert?.toLowerCase().includes("no findings")) {
                alertBoxDiv.classList.add("has-no-alert");
                innerHtmlContent = `
                    <span class="opacity-70 text-white text-center uppercase font-semibold">
                        NO FINDINGS
                    </span>
                `;
                alertBoxDiv.onclick = null; // No modal for "No Findings"
            } else {
                innerHtmlContent = `<span>${alertData.alert}</span>`;
                // Now openAlertModal is local
                alertBoxDiv.onclick = () => openAlertModal(alertData);
            }

            alertBoxDiv.innerHTML = `
                <div class="alert-message">${innerHtmlContent}</div>
            `;
        }

        // Function to collect all input values
        const collectInputData = () => {
            const oralIntakeInput = ioForm.querySelector('[name="oral_intake"]');
            const ivFluidsInput = ioForm.querySelector('[name="iv_fluids_volume"]');
            const urineOutputInput = ioForm.querySelector('[name="urine_output"]');

            return {
                oral_intake: oralIntakeInput ? oralIntakeInput.value.trim() : '',
                iv_fluids_volume: ivFluidsInput ? ivFluidsInput.value.trim() : '',
                urine_output: urineOutputInput ? urineOutputInput.value.trim() : '',
            };
        };

        // Function to trigger analysis
        const triggerAnalysis = async () => {
            const intakeData = collectInputData();
            const allEmpty = Object.values(intakeData).every(val => val === '');

            if (allEmpty) {
                // Use alert.js's showDefaultNoAlerts
                if (typeof showDefaultNoAlertsLocal === 'function') {
                    showDefaultNoAlertsLocal(alertBoxDiv);
                } else {
                    console.error('showDefaultNoAlertsLocal function not available');
                }
            } else {
                // Use alert.js's showAlertLoading
                if (typeof showAlertLoadingLocal === 'function') {
                    showAlertLoadingLocal(alertBoxDiv);
                } else {
                    console.error('showAlertLoadingLocal function not available');
                }

                try {
                    const response = await fetch(analyzeUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                        },
                        body: JSON.stringify(intakeData),
                    });

                    if (!response.ok) throw new Error(`Server error: ${response.status}`);

                    const alertData = await response.json();

                    setTimeout(() => {
                        // Use alert.js's displayAlert
                        if (typeof displayAlertLocal === 'function') {
                            displayAlertLocal(alertBoxDiv, alertData);
                        } else {
                            console.error('displayAlertLocal function not available');
                        }
                    }, 150);
                } catch (error) {
                    console.error("Intake/Output CDSS analysis failed:", error);
                    // Display a generic error using alert.js's displayAlert
                    if (typeof displayAlertLocal === 'function') {
                        displayAlertLocal(alertBoxDiv, { alert: 'Error analyzing...', severity: 'CRITICAL' });
                    }
                }
            }
        };

        // Event listeners for input changes
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(triggerAnalysis, 300);
            });
        });

        // Initial analysis on page load if patient is selected and fields are not empty
        const initialIntakeData = collectInputData();
        const initialAllEmpty = Object.values(initialIntakeData).every(val => val === '');
        if (!initialAllEmpty && ioForm.querySelector('input[name="patient_id"]').value) {
            triggerAnalysis();
        } else if (initialAllEmpty && ioForm.querySelector('input[name="patient_id"]').value) {
            // If patient is selected but fields are empty, show "No Alerts"
            if (typeof showDefaultNoAlertsLocal === 'function') {
                showDefaultNoAlertsLocal(alertBoxDiv);
            }
        }

        // Listen for io:data-loaded event to re-trigger analysis
        document.addEventListener('io:data-loaded', (event) => {
            console.log('io:data-loaded event received, re-triggering CDSS analysis.');
            // If ioData is null, it means fields were cleared, so show no alerts
            if (!event.detail.ioData) {
                showDefaultNoAlertsLocal(alertBoxDiv);
            } else {
                triggerAnalysis();
            }
        });
    }

    // Initialize CDSS for Intake and Output when the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', initializeIntakeOutputCdss);

    // Re-initialize if form content is dynamically reloaded (e.g., via patient selection)
    document.addEventListener('cdss:form-reloaded', (event) => {
        const formContainer = event.detail.formContainer;
        if (formContainer.querySelector('#io-form')) {
            initializeIntakeOutputCdss();
        }
    });

    // --- Modal popup for details (copied from alert.js) ---
    function openAlertModal(alertData) {
        const overlay = document.createElement("div");
        overlay.className = "alert-modal-overlay";

        const modal = document.createElement("div");
        modal.className = "alert-modal fade-in";
        modal.innerHTML = `
            <button class="close-btn">&times;</button>
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

    // --- Fade-in animation (copied from alert.js) ---
    const style = document.createElement("style");
    style.textContent = `
        .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98); }
            to { opacity: 1; transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
})();