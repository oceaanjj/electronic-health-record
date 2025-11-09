/**
 * How it works:
 * ... (same as before) ...
 * * MODIFICATION: The initializeCdssForForm function is attached to the 'window'
 * object so that other scripts (like patient-loader.js) can call it
 * after replacing page content.
 */

let debounceTimer;

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
        input.addEventListener("input", (e) => {
            clearTimeout(debounceTimer);

            const fieldName = e.target.dataset.fieldName;
            const finding = e.target.value.trim();
            const alertCell = document.querySelector(
                `[data-alert-for="${fieldName}"]`
            );

            // --- ADD THESE 2 LINES ---
            const form = e.target.closest(".cdss-form");
            const patientId = form.dataset.patientId;
            // ------------------------

            if (alertCell) {
                if (finding === "") {
                    showDefaultNoAlerts(alertCell);
                } else {
                    if (!alertCell.classList.contains("alert-loading")) {
                        showAlertLoading(alertCell);
                    }
                }
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && finding !== "") {
                    // --- UPDATE THIS LINE ---
                    analyzeField(
                        fieldName,
                        finding,
                        patientId,
                        analyzeUrl,
                        csrfToken
                    );
                }
            }, 300);
        });
    });
};

// --- Listen for form reload event to trigger initial analysis for pre-filled fields ---
document.addEventListener("cdss:form-reloaded", (event) => {
    const formContainer = event.detail.formContainer;
    const cdssForm = formContainer.querySelector(".cdss-form");

    if (cdssForm) {
        const analyzeUrl = cdssForm.dataset.analyzeUrl;
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        // --- ADD THIS LINE (THE BUG WAS HERE) ---
        const patientId = cdssForm.dataset.patientId;
        // ----------------------------------------

        if (!analyzeUrl || !csrfToken) {
            console.error(
                'CDSS form reloaded: Missing "data-analyze-url" or CSRF token.',
                cdssForm
            );
            return;
        }

        const inputs = cdssForm.querySelectorAll(".cdss-input");
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
                    // --- FIX THIS LINE (Pass patientId) ---
                    analyzeField(
                        fieldName,
                        finding,
                        patientId,
                        analyzeUrl,
                        csrfToken
                    );
                }
            }
        });
    }
});

// --- Initialize CDSS forms on page load ---
document.addEventListener("DOMContentLoaded", () => {
    const cdssForms = document.querySelectorAll(".cdss-form");
    cdssForms.forEach((form) => window.initializeCdssForForm(form));
});

// --- Function: Analyze input with backend ---
async function analyzeField(fieldName, finding, patientId, url, token) {
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);
    if (!alertCell) return;

    showAlertLoading(alertCell);

    try {
        const response = await fetch(url, {
            method: "POST",
            credentials: "same-origin", // <-- Correct
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
                Accept: "application/json", // <-- Correct
                "X-Requested-With": "XMLHttpRequest", // <-- Correct
            },
            body: JSON.stringify({
                fieldName,
                finding,
                patient_id: patientId, // <-- Correct
            }),
        });

        const responseText = await response.text(); // Get raw response text
        console.log("Raw server response:", responseText); // Log it here

        if (!response.ok) {
            console.error(
                "Server returned non-OK response:",
                response.status,
                responseText
            );
            // Try to parse JSON even on error, as Laravel sends JSON for 4xx/5xx errors
            let errorMsg = `Server error: ${response.status}`;
            try {
                const errorData = JSON.parse(responseText);
                // Check for Laravel validation error
                if (errorData.message && errorData.errors) {
                    errorMsg = `${errorData.message} (Details: ${JSON.stringify(
                        errorData.errors
                    )})`;
                } else {
                    errorMsg = errorData.message || errorMsg;
                }
            } catch (e) {
                // It wasn't JSON, just use the raw text if it's not too long
                errorMsg =
                    responseText.length < 200
                        ? responseText
                        : "Server returned non-JSON response. Check for redirects.";
            }
            throw new Error(errorMsg);
        }

        // Attempt to parse JSON only if response.ok is true
        let alertData;
        try {
            alertData = JSON.parse(responseText);
        } catch (jsonError) {
            console.error(
                "Failed to parse JSON from response:",
                jsonError,
                responseText
            );
            throw new Error("Invalid JSON response from server.");
        }

        setTimeout(() => {
            displayAlert(alertCell, alertData);
        }, 150);
    } catch (error) {
        console.error("CDSS analysis failed:", error);
        alertCell.innerHTML = `
      <div class="alert-box alert-red fade-in" style="height:90px;margin:2px;">
        <span class="alert-message">Error: ${error.message}</span>
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
    if (alertData.level === "CRITICAL")
        colorClass = "alert-red"; // Assuming "level" maps to severity
    else if (alertData.level === "WARNING") colorClass = "alert-orange";
    else if (alertData.level === "INFO") colorClass = "alert-green";

    alertBox.classList.add(colorClass);

    const alertMessage = document.createElement("div");
    alertMessage.className = "alert-message";
    alertMessage.style.padding = "1px"; //

    // --- Handle NO FINDINGS ---
    if (
        alertData.message?.toLowerCase().includes("no findings") ||
        !alertData.message
    ) {
        // Check message, not alert
        alertBox.classList.add("has-no-alert");
        alertMessage.innerHTML = `
      <span class="text-white text-center uppercase font-semibold opacity-80">
        NO FINDINGS
      </span>
    `;
    } else {
        alertMessage.innerHTML = `<span>${alertData.message}</span>`; // Use message, not alert
    }

    alertBox.appendChild(alertMessage);
    alertCell.appendChild(alertBox);

    // --- Simple scroll behavior without gradient ---
    alertMessage.addEventListener("scroll", () => {
        // No visual fade — clean scroll only
    });

    if (
        alertData.message &&
        !alertData.message.toLowerCase().includes("no findings")
    ) {
        // Use message, not alert
        alertBox.addEventListener("click", () => openAlertModal(alertData));
    }
}

// --- Function: Trigger initial CDSS analysis for pre-filled fields ---
window.triggerInitialCdssAnalysis = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    // --- ADD THIS LINE ---
    const patientId = form.dataset.patientId;

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

        if (alertCell && finding !== "") {
            // --- UPDATE THIS LINE ---
            analyzeField(fieldName, finding, patientId, analyzeUrl, csrfToken);
        } else if (alertCell && finding === "") {
            showDefaultNoAlerts(alertCell);
        }
    });
};

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

// --- Modal popup for details ---
function openAlertModal(alertData) {
    const overlay = document.createElement("div");
    overlay.className = "alert-modal-overlay";

    const modal = document.createElement("div");
    modal.className = "alert-modal fade-in";

    // Check if alertData.alert exists, otherwise use message
    const alertContent = alertData.alert
        ? alertData.alert
        : alertData.message || "No details available.";

    modal.innerHTML = `
    <button class="close-btn">&times;</button>
    <h2>Alert Details</h2>
    <p>${alertContent}</p>
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
