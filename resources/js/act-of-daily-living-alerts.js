/**
 * act-of-daily-living-alerts.js
 *
 * Handles CDSS alerts for the ADL form.
 * This script is structured to co-exist with the global `alert.js`
 * by hooking into the same functions but *only* acting on the form
 * with the ID `#adl-form`.
 *
 * It preserves the custom styling/display functions for ADL.
 */

let adlDebounceTimer;

/**
 * This function name MUST match the one called by patient-loader.js
 * We add a check for 'form.id' to ensure this logic only
 * runs for the ADL form.
 */
window.initializeCdssForForm = function (form) {
    // Only execute this logic for the ADL form
    if (form.id !== "adl-form") {
        return;
    }

    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error(
            'ADL CDSS form missing "data-analyze-url" or CSRF token not found.',
            form
        );
        return;
    }

    const inputs = form.querySelectorAll(".cdss-input");

    // --- Analyze while typing ---
    inputs.forEach((input) => {
        // Remove any old listeners just in case
        input.removeEventListener("input", handleAdlInput);
        // Add the new listener
        input.addEventListener("input", (e) =>
            handleAdlInput(e, analyzeUrl, csrfToken)
        );
    });
};

/**
 * Handles the 'input' event for debouncing.
 */
function handleAdlInput(e, analyzeUrl, csrfToken) {
    clearTimeout(adlDebounceTimer);

    const fieldName = e.target.dataset.fieldName;
    const finding = e.target.value.trim();
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);

    if (alertCell) {
        if (finding === "") {
            showDefaultNoAlerts(alertCell);
        } else {
            // Show loading spinner immediately on type
            showAlertLoading(alertCell);
        }
    }

    adlDebounceTimer = setTimeout(() => {
        if (fieldName && finding !== "") {
            analyzeAdlField(fieldName, finding, analyzeUrl, csrfToken);
        }
    }, 800); // 800ms debounce
}

/**
 * This function runs the analysis for pre-filled fields.
 * It's called on DOMContentLoaded and by the 'cdss:form-reloaded' event.
 */
function runInitialAdlAnalysis(form) {
    // Only execute this logic for the ADL form
    if (!form || form.id !== "adl-form") {
        return;
    }

    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
        console.error("ADL initial analysis missing URL or CSRF token.", form);
        return;
    }

    const inputs = form.querySelectorAll(".cdss-input");

    inputs.forEach((input) => {
        const fieldName = input.dataset.fieldName;
        const finding = input.value.trim();
        const alertCell = document.querySelector(
            `[data-alert-for="${fieldName}"]`
        );

        if (alertCell) {
            if (finding === "") {
                showDefaultNoAlerts(alertCell);
            } else {
                analyzeAdlField(fieldName, finding, analyzeUrl, csrfToken);
            }
        }
    });
}

// --- Listen for form reload event (from patient-loader.js) ---
document.addEventListener("cdss:form-reloaded", (event) => {
    const adlForm = event.detail.formContainer.querySelector("#adl-form");
    if (adlForm) {
        // The initializeCdssForForm() is already called by patient-loader.js
        // We just need to trigger the initial analysis for pre-filled fields.
        runInitialAdlAnalysis(adlForm);
    }
});

// --- Initialize CDSS on first page load ---
document.addEventListener("DOMContentLoaded", () => {
    const adlForm = document.getElementById("adl-form");
    if (adlForm) {
        // This check is in case alert.js is also loaded.
        // We want the ADL-specific intializer to run.
        if (typeof window.initializeCdssForForm === "function") {
            window.initializeCdssForForm(adlForm);
        }
        runInitialAdlAnalysis(adlForm);
    }
});

// -------------------------------------------------------------------
// --- HELPER FUNCTIONS (ADL-SPECIFIC) ---
// --- (These functions are mostly from your original file) ---
// -------------------------------------------------------------------

// --- Function: Analyze input with backend ---
async function analyzeAdlField(fieldName, finding, url, token) {
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);
    if (!alertCell) return;

    showAlertLoading(alertCell); // Show loading state

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

// --- Display alert content (ADL View) ---
function displayAdlAlert(alertCell, alertData) {
    alertCell.innerHTML = ""; // Clear previous content

    const alertBox = document.createElement("div");
    alertBox.className =
        "alert-box my-[3px] h-[53px] flex justify-center items-center fade-in";

    let colorClass = "alert-green";
    if (alertData.severity === "CRITICAL") colorClass = "alert-red";
    else if (alertData.severity === "WARNING") colorClass = "alert-orange";

    alertBox.classList.add(colorClass);

    let messageContainerHTML;
    if (
        alertData.alert &&
        !alertData.alert.toLowerCase().includes("no findings")
    ) {
        messageContainerHTML = `<div class="alert-message" style="padding:1rem;"><span>${alertData.alert}</span></div>`;
        alertBox.onclick = () => {
            openAlertModal(alertData);
        };
    } else {
        alertBox.classList.add("has-no-alert");
        messageContainerHTML = `<div class="alert-message text-center"><span class="opacity-70 text-white font-semibold">NO FINDINGS</span></div>`;
        alertCell.onclick = null;
    }

    alertBox.innerHTML = messageContainerHTML;
    alertCell.appendChild(alertBox);
}

// --- Loading spinner (ADL View) ---
//removed fade-in dahil sa parang stutter effect
function showAlertLoading(alertCell) {
    alertCell.innerHTML = `
    <div class="alert-box my-[3px] h-[53px] flex justify-center items-center alert-green"> 
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
            <div class="loading-spinner"></div> 
            <span>Analyzing...</span>
        </div>
    </div>`;
    alertCell.onclick = null;
}

// --- Default NO ALERTS state (ADL View) ---
function showDefaultNoAlerts(alertCell) {
    alertCell.innerHTML = `
    <div class="alert-box my-[3px] h-[53px] flex justify-center items-center alert-green has-no-alert">
        <span class="opacity-70 text-white font-semibold text-center">NO ALERTS</span>
    </div>`;
    alertCell.onclick = null;
}

// --- Modal popup for details ---
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
