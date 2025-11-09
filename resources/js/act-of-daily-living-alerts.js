/**
 * act-of-daily-living-alerts.js
 *
 * Handles CDSS alerts for the ADL form.
 * Mimics the structure of vital-signs-alerts.js to work with page-initializer.js
 * and prevent infinite loops.
 */

// --- Functions copied/adapted from alert.js ---

let adlDebounceTimer;

// Analyze a single ADL field
async function analyzeAdlField(fieldName, finding, url, token) {
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);
    if (!alertCell) return;

    showAlertLoading(alertCell);

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify({ fieldName, finding }),
        });

        if (!response.ok) throw new Error(`Server error: ${response.status}`);
        const alertData = await response.json();
        setTimeout(() => displayAdlAlert(alertCell, alertData), 150);
    } catch (error) {
        console.error("ADL CDSS analysis failed:", error);
        displayAdlAlert(alertCell, {
            alert: "Error analyzing...",
            severity: "CRITICAL",
        });
    }
}

// Display alert content in the ADL view
function displayAdlAlert(alertCell, alertData) {
    alertCell.innerHTML = ""; // Clear previous content

    const alertBox = document.createElement("div");
    alertBox.className =
        "alert-box my-[3px] h-[53px] flex justify-center items-center fade-in";

    let colorClass = "alert-green";
    if (alertData.severity === "CRITICAL") colorClass = "alert-red";
    else if (alertData.severity === "WARNING") colorClass = "alert-orange";

    alertBox.classList.add(colorClass);

    // Mark that an alert state has been set
    alertCell.dataset.alerted = "true";

    let messageContainerHTML;
    if (
        alertData.alert &&
        !alertData.alert.toLowerCase().includes("no findings")
    ) {
        messageContainerHTML = `<div class="alert-message" style="padding:1rem;"><span>${alertData.alert}</span></div>`;
        alertBox.onclick = () => {
            if (!alertBox.classList.contains("has-no-alert")) {
                openAlertModal(alertData);
            }
        };
    } else {
        messageContainerHTML = `<div class="alert-message text-center"><span class="opacity-70 text-white font-semibold">NO FINDINGS</span></div>`;
        alertCell.onclick = null;
    }

    alertBox.innerHTML = messageContainerHTML;

    alertCell.appendChild(alertBox);
}

// Show loading state
function showAlertLoading(alertCell) {
    // alertCell is the <td>
    // --- Start Fix: Ensure alertBox is created if it doesn't exist ---
    let alertBox = alertCell.querySelector(".alert-box");
    if (!alertBox) {
        alertCell.innerHTML = `
            <div class="alert-box my-[3px] h-[53px] flex justify-center items-center"></div>
        `;
        alertBox = alertCell.querySelector(".alert-box");
    }
    // --- End Fix ---

    // Use a static, non-animated background color. Green is a neutral choice.
    alertBox.className =
        "alert-box my-[3px] h-[53px] flex justify-center items-center alert-green";

    // The spinner itself has its own spin animation, which is desired.
    // The inner div helps with centering and spacing.
    alertBox.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
            <div class="loading-spinner"></div>
            <span>Analyzing...</span>
        </div>
    `;
    delete alertCell.dataset.alerted;
}

// Show default "No Alerts" state
function showDefaultNoAlerts(alertCell) {
    // --- Start Fix: Ensure innerHTML is set on the cell, not the classList ---
    alertCell.innerHTML = `
        <div class="alert-box my-[3px] h-[53px] flex justify-center items-center has-no-alert alert-green">
            <span class="opacity-70 text-white font-semibold text-center">NO ALERTS</span>
        </div>
    `;
    // --- End Fix ---

    // Get the newly created alert-box div and set its onclick to null
    const alertBoxDiv = alertCell.querySelector(".alert-box");
    if (alertBoxDiv) {
        alertBoxDiv.onclick = null;
    }
}

// --- Modal popup for details (from vital-signs-alerts.js) ---
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

// --- Logic adapted from vital-signs-alerts.js ---

// Trigger analysis for all pre-filled fields
function triggerInitialAdlAnalysis() {
    const adlForm = document.getElementById("adl-form");
    if (!adlForm) return;

    const analyzeUrl = adlForm.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (!analyzeUrl || !csrfToken) return;

    const inputs = adlForm.querySelectorAll(".cdss-input");

    inputs.forEach((input) => {
        const fieldName = input.dataset.fieldName;
        const finding = input.value.trim();
        const alertCell = document.querySelector(
            `[data-alert-for="${fieldName}"]`
        );

        // This is the anti-loop condition
        if (alertCell && !alertCell.dataset.alerted && finding !== "") {
            analyzeAdlField(fieldName, finding, analyzeUrl, csrfToken);
        } else if (alertCell && finding === "") {
            showDefaultNoAlerts(alertCell);
        }
    });
}

// Main initializer function attached to the window
window.initializeAdlAlerts = function () {
    const adlForm = document.getElementById("adl-form");
    if (!adlForm) return;

    // Check if listeners have already been attached to this element
    if (adlForm.dataset.listenersAttached) return;

    const analyzeUrl = adlForm.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (!analyzeUrl || !csrfToken) return;

    const inputs = adlForm.querySelectorAll(".cdss-input");

    inputs.forEach((input) => {
        input.addEventListener("input", (e) => {
            clearTimeout(adlDebounceTimer);
            const fieldName = e.target.dataset.fieldName;
            const finding = e.target.value.trim();
            const alertCell = document.querySelector(
                `[data-alert-for="${fieldName}"]`
            );

            if (alertCell && finding === "") {
                showDefaultNoAlerts(alertCell);
            }

            adlDebounceTimer = setTimeout(() => {
                if (fieldName && finding !== "") {
                    analyzeAdlField(fieldName, finding, analyzeUrl, csrfToken);
                }
            }, 300);
        });
    });

    // Mark that listeners have been attached to this specific form instance
    adlForm.dataset.listenersAttached = "true";

    // Trigger the initial analysis for any pre-filled data
    triggerInitialAdlAnalysis();
};

// --- START: THE FIX ---
// Listen for the 'cdss:form-reloaded' event dispatched by patient-loader.js
// This will re-run the initializer after the new form content is loaded.
document.addEventListener("cdss:form-reloaded", (event) => {
    // Check if the new form is the ADL form
    if (event.detail.formContainer.querySelector("#adl-form")) {
        console.log("ADL form reloaded, re-initializing alerts...");
        window.initializeAdlAlerts();
    }
});
// --- END: THE FIX ---
