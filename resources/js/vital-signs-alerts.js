let debounceTimer;
let activeAnalysisCount = 0; // Counter for active analysis requests

// --- Function: Disable header inputs ---

// --- Function: Disable header inputs ---
function disableHeaderInputs() {
    const patientSearchInput = document.getElementById("patient_search_input");
    const dateSelector = document.getElementById("date_selector");
    const dayNoSelector = document.getElementById("day_no_selector");

    if (patientSearchInput) {
        patientSearchInput.setAttribute("disabled", "true");
    }
    if (dateSelector) {
        dateSelector.setAttribute("disabled", "true");
    }
    if (dayNoSelector) {
        dayNoSelector.setAttribute("disabled", "true");
    }
}

// --- Function: Enable header inputs ---
function enableHeaderInputs() {
    const patientSearchInput = document.getElementById("patient_search_input");
    const dateSelector = document.getElementById("date_selector");
    const dayNoSelector = document.getElementById("day_no_selector");

    if (patientSearchInput) {
        patientSearchInput.removeAttribute("disabled");
    }
    if (dateSelector) {
        dateSelector.removeAttribute("disabled");
    }
    if (dayNoSelector) {
        dayNoSelector.removeAttribute("disabled");
    }
}

// --- Function: Analyze input with backend ---
async function analyzeVitalSignField(
    fieldName,
    time,
    value,
    url,
    token,
    vitalsOverride = null
) {
    const alertCell = document.querySelector(`[data-alert-for-time="${time}"]`);
    if (!alertCell) return;

    let vitalsToSend = {};

    if (vitalsOverride) {
        vitalsToSend = vitalsOverride;
    } else {
        // Collect all vital signs for the current time slot from the DOM
        const vitalsForm = document.getElementById("vitals-form");
        if (!vitalsForm) return; // Should not happen if called from initialize or trigger

        const vitalInputsForTime = vitalsForm.querySelectorAll(
            `.vital-input[data-time="${time}"]`
        );
        vitalInputsForTime.forEach((input) => {
            const currentFieldName = input.dataset.fieldName;
            vitalsToSend[currentFieldName] = input.value.trim();
        });
    }

    const vitalsForm = document.getElementById("vitals-form");
    if (!vitalsForm) return;

    activeAnalysisCount++;
    if (activeAnalysisCount === 1) {
        vitalsForm.classList.add('is-loading-vitals');
        disableHeaderInputs();
    }

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify({ time, vitals: vitalsToSend }), // Send all vitals for the time slot
        });

        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        const alertData = await response.json();

        setTimeout(() => {
            displayAlert(alertCell, alertData);
            activeAnalysisCount--;
            if (activeAnalysisCount === 0) {
                vitalsForm.classList.remove('is-loading-vitals');
                enableHeaderInputs();
            }
        }, 150);
    } catch (error) {
        console.error("Vital Signs CDSS analysis failed:", error);
        displayAlert(alertCell, {
            alert: "Error analyzing...",
            severity: "CRITICAL",
        });
        activeAnalysisCount--;
        if (activeAnalysisCount === 0) {
            vitalsForm.classList.remove('is-loading-vitals');
            enableHeaderInputs();
        }
    }
}

// --- Loading spinner (continuous) ---
function showAlertLoading(alertCell) {
    const alertBoxDiv = alertCell.querySelector(".alert-box");
    if (!alertBoxDiv) {
        return;
    }

    // Update content of the inner div

    alertBoxDiv.innerHTML = `

            <div class=\"alert-message\">\n            <div class=\"alert-loading\">\n                <div class=\"loading-spinner\"></div>\n                <span>Analyzing...</span>\n            </div>\n        </div>

        `;

    alertCell.onclick = null;
}

// --- Display alert content ---
function displayAlert(alertCell, alertData) {
    console.log(
        "displayAlert called for time:",
        alertCell.dataset.alertForTime,
        "with data:",
        alertData
    );
    const alertBoxDiv = alertCell.querySelector(".alert-box");
    if (!alertBoxDiv) {
        return;
    }

    // Manage classes on the parent <td> (alertCell)
    alertCell.classList.remove(
        "alert-loading",
        "has-no-alert",
        "alert-red",
        "alert-orange",
        "alert-green"
    ); // Remove previous state classes

    // Set color by severity
    let colorClass = "alert-green";
    if (alertData.severity === "CRITICAL") colorClass = "alert-red";
    else if (alertData.severity === "WARNING") colorClass = "alert-orange";
    else if (alertData.severity === "INFO") colorClass = "alert-green";

    alertCell.classList.add(colorClass); // Add color class

    let alertMessageContent;
    if (alertData.alert?.toLowerCase().includes("no findings")) {
        alertCell.classList.add("has-no-alert");
        alertMessageContent = `
            <span class=\"opacity-80 text-white text-center font-semibold uppercase\">\n                NO FINDINGS\n            </span>
        `;
        alertCell.onclick = null; // No modal for "No Findings"
    } else {
        alertMessageContent = `<span >${alertData.alert}</span>`;
        alertCell.onclick = () => openAlertModal(alertData); // Add click listener for modal
    }

    // Update content of the inner div
    alertBoxDiv.innerHTML = `<div class=\"alert-message\"  style="padding:1rem;">${alertMessageContent}</div>`;
}

// --- Default NO ALERTS state ---
function showDefaultNoAlerts(alertCell) {
    const alertBoxDiv = alertCell.querySelector(".alert-box");
    if (!alertBoxDiv) {
        return;
    }

    // Manage classes on the parent <td> (alertCell)
    alertCell.classList.remove("alert-loading", "alert-red", "alert-orange"); // Remove loading and severity classes
    alertCell.classList.add("has-no-alert", "alert-green"); // Add no alerts state, green color

    // Update content of the inner div
    alertBoxDiv.innerHTML = `
        <div class=\"alert-message\">\n            <span class=\"opacity-80 text-white text-center font-semibold uppercase\">\n                NO ALERTS\n            </span>\n        </div>
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

// --- Function: Trigger initial CDSS analysis for pre-filled fields ---
function triggerInitialVitalSignsAnalysis() {
    console.log("triggerInitialVitalSignsAnalysis called");
    const vitalsForm = document.getElementById("vitals-form");
    if (!vitalsForm) {
        console.warn("Initial Vital Signs Analysis: #vitals-form not found.");
        return;
    }

    const analyzeUrl = vitalsForm.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error(
            'Initial Vital Signs Analysis: Missing "data-analyze-url" or CSRF token.'
        );
        return;
    }

    const inputs = vitalsForm.querySelectorAll(".cdss-input");
    console.log("Inputs found for initial analysis:", inputs.length);
    const vitalsByTime = new Map(); // Map to store vitals grouped by time

    inputs.forEach((input) => {
        const fieldName = input.dataset.fieldName;
        const time = input.dataset.time;
        const value = input.value.trim();

        if (time && value !== "") {
            if (!vitalsByTime.has(time)) {
                vitalsByTime.set(time, {});
            }
            vitalsByTime.get(time)[fieldName] = value;
        }
    });
    console.log("Vitals grouped by time for initial analysis:", vitalsByTime);

    vitalsByTime.forEach((vitals, time) => {
        const alertCell = document.querySelector(
            `[data-alert-for-time="${time}"]`
        );
        if (alertCell) {
            // Only analyze if the alert cell is not already displaying an alert or loading
            const isAlreadyAlerted =
                alertCell.classList.contains("alert-red") ||
                alertCell.classList.contains("alert-orange") ||
                alertCell.classList.contains("alert-green") ||
                alertCell.classList.contains("has-no-alert");
            const isLoading = alertCell.classList.contains("alert-loading");

            if (!isAlreadyAlerted && !isLoading) {
                console.log(
                    `Calling analyzeVitalSignField for time: ${time} with vitals:`,
                    vitals
                );
                showAlertLoading(alertCell);
                analyzeVitalSignField(
                    null,
                    time,
                    null,
                    analyzeUrl,
                    csrfToken,
                    vitals
                ); // Pass vitals directly
            } else {
                console.log(
                    `Skipping analysis for time: ${time}. Already alerted or loading.`
                );
            }
        }
    });
}

window.initializeVitalSignsAlerts = function () {
    console.log("initializeVitalSignsAlerts called");
    const vitalsForm = document.getElementById("vitals-form");
    if (!vitalsForm) {
        console.warn("Vital Signs Alerts: #vitals-form not found.");
        return;
    }

    const analyzeUrl = vitalsForm.dataset.analyzeUrl; // This will be added to the form
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error(
            'Vital Signs Alerts: Form missing "data-analyze-url" or CSRF token not found.'
        );
        return;
    }

    const inputs = vitalsForm.querySelectorAll(".cdss-input");
    let debounceTimer;

    inputs.forEach((input) => {
        input.addEventListener("input", (e) => {
            clearTimeout(debounceTimer);

            const fieldName = e.target.dataset.fieldName; // e.g., 'temperature'
            const time = e.target.dataset.time; // e.g., '06:00'
            const value = e.target.value.trim();

            const alertCell = document.querySelector(
                `[data-alert-for-time="${time}"]`
            );

            if (alertCell) {
                if (value === "") {
                    showDefaultNoAlerts(alertCell);
                }
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && time && value !== "") {
                    const currentAlertCell = document.querySelector(
                        `[data-alert-for-time="${time}"]`
                    );
                    if (
                        currentAlertCell &&
                        !currentAlertCell.classList.contains("alert-loading")
                    ) {
                        showAlertLoading(currentAlertCell); // Re-add this call
                    }
                    analyzeVitalSignField(
                        fieldName,
                        time,
                        value,
                        analyzeUrl,
                        csrfToken
                    );
                }
            }, 300);
        });
    });

    // Trigger initial analysis for pre-filled fields
    triggerInitialVitalSignsAnalysis();
};
