/**
 * ===================================================================
 * DYNAMIC CDSS ALERT SYSTEM (Universal)
 * ===================================================================
 * This single script handles CDSS alerts for all forms.
 *
 * It is now compatible with two types of pages:
 * 1. Single-Field: (e.g., physical-exam, adl)
 * - Sends: { fieldName: "...", finding: "..." }
 * 2. Time-Based Row: (e.g., vital-signs)
 * - Sends: { time: "...", vitals: { field1: "...", field2: "..." } }
 */

let debounceTimer;

/**
 * Finds the correct alert cell for a given input.
 * @param {HTMLElement} input - The input element.
 * @returns {HTMLElement|null} The-corresponding alert cell.
 */
function findAlertCellForInput(input) {
    const fieldName = input.dataset.fieldName;
    const time = input.dataset.time;

    if (time) {
        // Vital-Signs logic: Find cell by time
        return document.querySelector(`[data-alert-for-time="${time}"]`);
    }
    if (fieldName) {
        // ADL / Physical-Exam logic: Find cell by field name
        return document.querySelector(`[data-alert-for="${fieldName}"]`);
    }
    return null;
}

/**
 * Attaches real-time listeners to a form's inputs.
 */
window.initializeCdssForForm = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error(
            'CDSS form missing "data-analyze-url" or CSRF token not found.',
            form
        );
        return;
    }

    const inputs = form.querySelectorAll(".cdss-input");

    inputs.forEach((input) => {
        // Debounced input handler
        const handleInput = (e) => {
            clearTimeout(debounceTimer);

            const fieldName = e.target.dataset.fieldName;
            const finding = e.target.value.trim();
            const time = e.target.dataset.time;
            const alertCell = findAlertCellForInput(e.target);

            if (alertCell && finding === "") {
                // User deleted text. Show "No Alerts" immediately.
                console.log("[ALERT] Input empty. Showing 'No Alerts'.");
                showDefaultNoAlerts(alertCell);
                delete alertCell.dataset.startTime; // Clear any pending timer
                return; // Stop here
            }

            // User is typing. Set a timer.
            debounceTimer = setTimeout(() => {
                // User has stopped typing for 300ms.
                if (fieldName && finding !== "") {
                    // Now, show the loading spinner...
                    if (alertCell) {
                        console.log(
                            "[ALERT] Debounce over. Showing loading spinner."
                        );
                        showAlertLoading(alertCell);
                        alertCell.dataset.startTime = performance.now(); // <-- START TIMER
                    }
                    // ...and make the API call.
                    console.log("[ALERT] Calling analyzeField...");
                    analyzeField(
                        fieldName,
                        finding,
                        time,
                        alertCell,
                        analyzeUrl,
                        csrfToken
                    );
                }

                console.log(
                    `[ALERT] Typing... Field: ${fieldName || "N/A"}, Time: ${
                        time || "N/A"
                    }, Value: "${finding}"`
                );
            }, 300); // 300ms delay.
        };

        input.removeEventListener("input", handleInput);
        input.addEventListener("input", handleInput);
    });
};

/**
 * Triggers analysis for all fields that have pre-filled values.
 */
window.triggerInitialCdssAnalysis = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error(
            'Initial CDSS analysis missing "data-analyze-url" or CSRF token.',
            form
        );
        return;
    }

    const inputs = form.querySelectorAll(".cdss-input");

    // --- THIS LOGIC IS NOW TIME-AWARE ---

    // 1. Group inputs by time (for vitals) or handle as individuals (for adl/pe)
    const analysisGroups = new Map();

    inputs.forEach((input) => {
        const fieldName = input.dataset.fieldName;
        const finding = input.value.trim();
        const time = input.dataset.time;
        const alertCell = findAlertCellForInput(input);

        if (!alertCell || finding === "") {
            if (alertCell) showDefaultNoAlerts(alertCell);
            return;
        }

        let key = null;
        let data = null;

        if (time) {
            // It's a vitals input, group by time
            key = `time-${time}`;
            if (!analysisGroups.has(key)) {
                analysisGroups.set(key, { time, alertCell, fields: {} });
            }
            analysisGroups.get(key).fields[fieldName] = finding;
        } else {
            // It's a standard (ADL/PE) input, handle individually
            key = `field-${fieldName}`;
            analysisGroups.set(key, {
                time: null,
                alertCell,
                fieldName,
                finding,
            });
        }
    });

    // 2. Run analysis for each group
    analysisGroups.forEach((group) => {
        console.log("[ALERT] Initial load. Showing loading spinner.");
        showAlertLoading(group.alertCell);
        group.alertCell.dataset.startTime = performance.now(); // <-- START TIMER

        console.log("[ALERT] Calling analyzeField for initial load.");
        if (group.time) {
            // Vitals logic
            analyzeField(
                null,
                null,
                group.time,
                group.alertCell,
                analyzeUrl,
                csrfToken,
                group.fields
            );
        } else {
            // Standard logic
            analyzeField(
                group.fieldName,
                group.finding,
                null,
                group.alertCell,
                analyzeUrl,
                csrfToken
            );
        }
    });
};

// --- GLOBAL EVENT LISTENERS ---

// 1. Listen for form reload (patient-loader.js)
document.addEventListener("cdss:form-reloaded", (event) => {
    const formContainer = event.detail.formContainer;
    const cdssForm = formContainer.querySelector(".cdss-form");

    if (cdssForm) {
        console.log("[ALERT] Form reloaded. Initializing alerts.");
        window.initializeCdssForForm(cdssForm);
        window.triggerInitialCdssAnalysis(cdssForm);
    }
});

// 2. Initialize on first page load
document.addEventListener("DOMContentLoaded", () => {
    const cdssForms = document.querySelectorAll(".cdss-form");
    cdssForms.forEach((form) => {
        console.log(
            "[ALERT] DOM loaded. Initializing alerts for form:",
            form.id
        );
        window.initializeCdssForForm(form);
        window.triggerInitialCdssAnalysis(form);
    });
});

// --- API & DISPLAY FUNCTIONS ---

/**
 * Gets the dynamic height class from the <form> tag.
 */
function getAlertHeightClass(alertCell) {
    const form = alertCell.closest(".cdss-form");
    if (form && form.dataset.alertHeightClass) {
        return form.dataset.alertHeightClass;
    }
    console.warn(
        "CDSS: No data-alert-height-class found on form. Defaulting to h-[90px]."
    );
    return "h-[90px]"; // Fallback default
}

/**
 * Calls the backend to analyze a single field.
 * @param {string|null} fieldName - The single field name (for ADL/PE)
 * @param {string|null} finding - The single finding (for ADL/PE)
 * @param {string|null} time - The time slot (for Vitals)
 * @param {HTMLElement} alertCell - The <td> cell to update
 * @param {string} url - The analysis URL
 * @param {string} token - The CSRF token
 * @param {Object|null} vitalsOverride - Pre-grouped vitals for initial load
 */
async function analyzeField(
    fieldName,
    finding,
    time,
    alertCell,
    url,
    token,
    vitalsOverride = null
) {
    // We already found the cell, so just check if it exists
    if (!alertCell) return;

    let bodyData = {};

    // --- Vitals Signs ---
    if (time) {
        console.log(`[ALERT] Vitals logic triggered for time: ${time}`);
        let vitalsToSend = {};

        if (vitalsOverride) {
            // Use pre-grouped vitals from initial load
            vitalsToSend = vitalsOverride;
        } else {
            // Gather all vitals for this time slot from the DOM
            const form = alertCell.closest(".cdss-form");
            if (form) {
                const vitalInputsForTime = form.querySelectorAll(
                    `.cdss-input[data-time="${time}"]`
                );
                vitalInputsForTime.forEach((input) => {
                    const currentFieldName = input.dataset.fieldName;
                    vitalsToSend[currentFieldName] = input.value.trim();
                });
            }
            1;
        }
        bodyData = { time: time, vitals: vitalsToSend };
        // --- End Vitals Signs ---
    } else {
        // --- Physical Exam / ADL---
        console.log(`[ALERT] Standard logic triggered for field: ${fieldName}`);
        bodyData = { fieldName: fieldName, finding: finding };
        // --- End Standard Physical Exam ---
    }

    console.log("[ALERT] Sending body:", bodyData);

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify(bodyData), // This is now dynamic
        });

        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        const alertData = await response.json();
        console.log("[ALERT] Received alert data:", alertData);

        setTimeout(() => {
            displayAlert(alertCell, alertData);
        }, 300); // Short delay to prevent loading flash
    } catch (error) {
        console.error("CDSS analysis failed:", error);
        displayAlert(alertCell, {
            alert: "Error analyzing...",
            severity: "CRITICAL",
        });
    }
}

/**
 * Displays the API alert result.
 */
function displayAlert(alertCell, alertData) {
    // --- ADDED TIMER LOG ---
    const startTime = parseFloat(alertCell.dataset.startTime || 0);
    const endTime = performance.now();
    const timeSpent = startTime ? (endTime - startTime).toFixed(2) : "N/A";

    console.log(
        `[ALERT] Displaying alert:`,
        alertData,
        `(Time taken: ${timeSpent}ms)`
    );
    // --- END TIMER LOG ---

    const heightClass = getAlertHeightClass(alertCell);

    // Set color by severity
    let colorClass = "alert-green";
    if (alertData.severity === "CRITICAL") colorClass = "alert-red";
    else if (alertData.severity === "WARNING") colorClass = "alert-orange";
    else if (alertData.severity === "INFO") colorClass = "alert-green";

    let alertContentHTML = "";
    let hasNoAlertsClass = "";
    let isClickable = false;

    // --- Handle NO FINDINGS ---
    if (
        !alertData.alert ||
        alertData.alert.toLowerCase().includes("no findings")
    ) {
        hasNoAlertsClass = "has-no-alert";
        alertContentHTML = `
      <span class="text-white text-center uppercase font-semibold opacity-80">
        NO FINDINGS
      </span>
    `;
    } else {
        // --- Handle Vitals Signs (bullet points) vs Standard (single line) ---
        if (alertData.alert.includes(";")) {
            // Vitals-style: split by semicolon
            const alertsArray = alertData.alert
                .split("; ")
                .filter((alert) => alert.trim() !== "");
            let bulletPoints = alertsArray
                .map((alert) => `<li>${alert.trim()}</li>`)
                .join("");
            alertContentHTML = `<ul class="text-left list-disc list-inside">${bulletPoints}</ul>`;
        } else {
            // Standard-style: single line
            alertContentHTML = `<span>${alertData.alert}</span>`;
        }
        isClickable = true;
    }

    // Set the entire innerHTML of the <td>
    // 'fade-in' is kept for the final result
    alertCell.innerHTML = `
    <div class="alert-box fade-in ${heightClass} ${colorClass} ${hasNoAlertsClass}" 
         style="margin: 2px;">
      <div class="alert-message" style="padding: 1px;">
        ${alertContentHTML}
      </div>
    </div>
  `;

    if (isClickable) {
        const alertBox = alertCell.querySelector(".alert-box");
        if (alertBox) {
            alertBox.addEventListener("click", () => openAlertModal(alertData));
        }
    }

    // Clean up the timer data
    delete alertCell.dataset.startTime;
}

/**
 * Displays the default "No Alerts" state.
 */
function showDefaultNoAlerts(alertCell) {
    const heightClass = getAlertHeightClass(alertCell);

    // 'fade-in' is REMOVED to prevent stutter on delete
    alertCell.innerHTML = `
    <div class="alert-box has-no-alert alert-green ${heightClass}" 
         style="margin: 2.8px;">
      <span class="alert-message opacity-80 text-white text-center font-semibold uppercase">
        NO ALERTS
      </span>
    </div>
  `;
    alertCell.onclick = null;
}

/**
 * Displays the loading spinner state.
 */
function showAlertLoading(alertCell) {
    const heightClass = getAlertHeightClass(alertCell);

    // This HTML structure does NOT use the '.alert-loading' class
    // This bypasses the CSS fade-in animation and stops the stutter
    alertCell.innerHTML = `
    <div class="alert-box alert-green ${heightClass} flex justify-center items-center" 
         style="margin: 2px;">
      
      <div style="display: flex; align-items: center; justify-content: center; gap: 8px; color: #ffffffcc; font-weight: 600;">
        <div class="loading-spinner"></div>
        <span>Analyzing...</span>
      </div>

    </div>
  `;
    alertCell.onclick = null;
}

// --- MODAL ---

function openAlertModal(alertData) {
    if (document.querySelector(".alert-modal-overlay")) return;

    const overlay = document.createElement("div");
    overlay.className = "alert-modal-overlay";

    let modalContent = "";

    // --- Handle Vitals Signs (bullet points) vs Standard (single line) ---
    if (alertData.alert.includes(";")) {
        // Vitals-style: split by semicolon
        const alertsArray = alertData.alert
            .split("; ")
            .filter((alert) => alert.trim() !== "");
        let bulletPoints = alertsArray
            .map((alert) => `<li>${alert.trim()}</li>`)
            .join("");
        modalContent = `<ul class="text-left list-disc list-inside">${bulletPoints}</ul>`;
    } else {
        // Standard-style: single paragraph
        modalContent = `<p>${alertData.alert}</p>`;
    }

    const modal = document.createElement("div");
    modal.className = "alert-modal fade-in"; // 'fade-in' kept for modal
    modal.innerHTML = `
    <button class="close-btn">&times;</button>
    <h2>Alert Details</h2>
    ${modalContent}
    ${
        alertData.recommendation
            ? `<h3>Recommendation:</h3><p>${alertData.recommendation}</p>`
            : ""
    }
  `;

    overlay.appendChild(modal);
    document.body.appendChild(overlay);

    const closeModal = () => overlay.remove();
    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) closeModal();
    });
    modal.querySelector(".close-btn").addEventListener("click", closeModal);
}

// --- DYNAMIC STYLES ---
(function () {
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
