/**
 * ===================================================================
 * DYNAMIC CDSS ALERT SYSTEM
 * ===================================================================
 * This single script handles CDSS alerts for all forms.
 *
 * How it works:
 * 1.  It relies on a 'data-alert-height-class' attribute on the
 * <form class="cdss-form"> tag (e.g., "h-[90px]" or "h-[53px]").
 * 2.  A helper function, getAlertHeightClass(), finds the form
 * and gets this class.
 * 3.  All display functions (loading, default, alert) are
 * standardized to replace the innerHTML of the alert cell (<td>)
 * and use this dynamic height class.
 * 4.  It attaches to window.initializeCdssForForm and the
 * "cdss:form-reloaded" event, just like before.
 */

let debounceTimer;

/**
 * Attaches real-time listeners to a form's inputs.
 * This is the main initialization function called by patient-loader.js
 * and on DOMContentLoaded.
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

    // --- Analyze while typing ---
    inputs.forEach((input) => {
        // Debounced input handler
        const handleInput = (e) => {
            clearTimeout(debounceTimer);

            const fieldName = e.target.dataset.fieldName;
            const finding = e.target.value.trim();
            const alertCell = document.querySelector(
                `[data-alert-for="${fieldName}"]`
            );

            if (alertCell) {
                if (finding === "") {
                    showDefaultNoAlerts(alertCell);
                } else {
                    // Show loading state immediately on type
                    showAlertLoading(alertCell);
                }
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && finding !== "") {
                    analyzeField(fieldName, finding, analyzeUrl, csrfToken);
                }
            }, 800);
        };

        // Remove old listener to prevent duplicates, then add new one
        input.removeEventListener("input", handleInput);
        input.addEventListener("input", handleInput);
    });
};

/**
 * Triggers analysis for all fields that have pre-filled values.
 * Called on page load and after a patient is loaded.
 */
function triggerInitialCdssAnalysis(form) {
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
                analyzeField(fieldName, finding, analyzeUrl, csrfToken);
            }
        }
    });
}

// --- GLOBAL EVENT LISTENERS ---

// 1. Listen for form reload (patient-loader.js)
document.addEventListener("cdss:form-reloaded", (event) => {
    const formContainer = event.detail.formContainer;
    const cdssForm = formContainer.querySelector(".cdss-form");

    if (cdssForm) {
        // Re-attach listeners for typing
        window.initializeCdssForForm(cdssForm);
        // Run analysis for pre-filled data
        triggerInitialCdssAnalysis(cdssForm);
    }
});

// 2. Initialize on first page load
document.addEventListener("DOMContentLoaded", () => {
    const cdssForms = document.querySelectorAll(".cdss-form");
    cdssForms.forEach((form) => {
        window.initializeCdssForForm(form);
        triggerInitialCdssAnalysis(form);
    });
});

// --- API & DISPLAY FUNCTIONS ---

/**
 * Gets the dynamic height class (e.g., "h-[90px]") from the
 * <form> tag's data attribute.
 * @param {HTMLElement} alertCell - The <td> element for the alert.
 * @returns {string} The height class or a default.
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
 */
async function analyzeField(fieldName, finding, url, token) {
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);
    if (!alertCell) return;

    // This call is necessary for the initial page load analysis
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
        console.log("[CDSS] Received alert data:", alertData);

        setTimeout(() => {
            displayAlert(alertCell, alertData);
        }, 800); // Short delay to prevent loading flash
    } catch (error) {
        console.error("CDSS analysis failed:", error);
        // Display an error *using the same displayAlert function*
        displayAlert(alertCell, {
            alert: "Error analyzing...",
            severity: "CRITICAL",
        });
    }
}

/**
 * [REFACTORED] Displays the API alert result.
 * Replaces the innerHTML of the alert cell.
 */
function displayAlert(alertCell, alertData) {
    console.log("[CDSS] Displaying alert:", alertData);
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
        alertContentHTML = `<span>${alertData.alert}</span>`;
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

    // Add click listener only if it's a real alert
    if (isClickable) {
        const alertBox = alertCell.querySelector(".alert-box");
        if (alertBox) {
            alertBox.addEventListener("click", () => openAlertModal(alertData));
        }
    }
}

/**
 * [REFACTORED] Displays the default "No Alerts" state.
 * Replaces the innerHTML of the alert cell.
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
    alertCell.onclick = null; // Clear any old click handlers on the <td>
}

/**
 * [REFACTORED] Displays the loading spinner state.
 * Replaces the innerHTML of the alert cell.
 */
function showAlertLoading(alertCell) {
    const heightClass = getAlertHeightClass(alertCell);

    // --- THIS IS THE FIX ---
    // This HTML structure now mimics your "smooth" adl.js file.
    // It does NOT add the '.alert-loading' class, which bypasses the
    // fade-in animation in your app.css and stops the stutter.
    alertCell.innerHTML = `
    <div class="alert-box alert-green ${heightClass} flex justify-center items-center" 
         style="margin: 2px;">
      
      <div style="display: flex; align-items: center; justify-content: center; gap: 8px; color: #ffffffcc; font-weight: 600;">
        <div class="loading-spinner"></div>
        <span>Analyzing...</span>
      </div>

    </div>
  `;
    alertCell.onclick = null; // Clear any old click handlers on the <td>
}

// --- MODAL ---

function openAlertModal(alertData) {
    // Check if a modal is already open
    if (document.querySelector(".alert-modal-overlay")) return;

    const overlay = document.createElement("div");
    overlay.className = "alert-modal-overlay";

    const modal = document.createElement("div");
    modal.className = "alert-modal fade-in"; // 'fade-in' kept for modal
    modal.innerHTML = `
    <button class="close-btn">&times;</button>
    <h2>Alert Details</h2>
    <p>${alertData.alert}</p>
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
// (Inject fade-in animation)
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
