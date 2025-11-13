// =======================================================
// UNIVERSAL CDSS ALERT SYSTEM
// Handles CDSS alerts for all forms (ADL, PE, Vitals, etc.)
// Supports both single-field and time-based row forms
// =======================================================

let debounceTimer;

// Find alert cell for a given input
function findAlertCellForInput(input) {
    const fieldName = input.dataset.fieldName;
    const time = input.dataset.time;
    if (time) return document.querySelector(`[data-alert-for-time="${time}"]`);
    if (fieldName)
        return document.querySelector(`[data-alert-for="${fieldName}"]`);
    return null;
}

// Initialize CDSS listeners for a form
// form → HTMLFormElement
window.initializeCdssForForm = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (!analyzeUrl || !csrfToken) {
        console.error('Missing "data-analyze-url" or CSRF token.', form);
        return;
    }

    console.log(
        `[CDSS] Initializing listeners for form: ${form.id || "(unnamed)"}`
    );

    const inputs = form.querySelectorAll(".cdss-input");
    inputs.forEach((input) => {
        if (input.dataset.alertListenerAttached) return; // Prevent duplicate listeners

        const handleInput = (e) => {
            clearTimeout(debounceTimer);
            const fieldName = e.target.dataset.fieldName;
            const finding = e.target.value.trim();
            const time = e.target.dataset.time;
            const alertCell = findAlertCellForInput(e.target);

            if (alertCell && finding === "") {
                showDefaultNoAlerts(alertCell);
                delete alertCell.dataset.startTime;
                return;
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && finding !== "") {
                    if (alertCell) {
                        showAlertLoading(alertCell);
                        alertCell.dataset.startTime = performance.now();
                        console.log(
                            `[CDSS] Started analysis for ${fieldName} at ${alertCell.dataset.startTime} ms`
                        );
                    }
                    analyzeField(
                        fieldName,
                        finding,
                        time,
                        alertCell,
                        analyzeUrl,
                        csrfToken
                    );
                    console.log(
                        `[CDSS] Input detected → Field: ${
                            fieldName || "(time-based)"
                        } | Value: ${finding}`
                    );
                }
            }, 300);
        };

        input.addEventListener("input", handleInput);
        input.dataset.alertListenerAttached = "true";
    });
};

// Trigger CDSS analysis for all pre-filled inputs
// form → HTMLFormElement
window.triggerInitialCdssAnalysis = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (!analyzeUrl || !csrfToken) {
        console.error('Missing "data-analyze-url" or CSRF token.', form);
        return;
    }

    console.log(
        `[CDSS] Triggering initial analysis for form: ${form.id || "(unnamed)"}`
    );

    const inputs = form.querySelectorAll(".cdss-input");
    const analysisGroups = new Map();

    inputs.forEach((input) => {
        const fieldName = input.dataset.fieldName;
        const finding = input.value.trim();
        const time = input.dataset.time;
        const alertCell = findAlertCellForInput(input);

        if (!alertCell) return;
        if (finding === "") {
            showDefaultNoAlerts(alertCell);
            return;
        }

        let key = time ? `time-${time}` : `field-${fieldName}`;
        if (!analysisGroups.has(key)) {
            analysisGroups.set(
                key,
                time
                    ? { time, alertCell, fields: {} }
                    : { time: null, alertCell, fieldName, finding }
            );
        }
        if (time) analysisGroups.get(key).fields[fieldName] = finding;
    });

    analysisGroups.forEach((group) => {
        showAlertLoading(group.alertCell);
        group.alertCell.dataset.startTime = performance.now();
        console.log(
            `[CDSS] Initial analysis started at ${group.alertCell.dataset.startTime} ms`
        );
    });

    analysisGroups.forEach((group) => {
        if (group.time) {
            console.log(`[CDSS] Analyzing time group ${group.time}...`);
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
            console.log(`[CDSS] Analyzing single field: ${group.fieldName}`);
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

// Global listeners for reload & DOM load
if (!window.cdssFormReloadListenerAttached) {
    window.cdssFormReloadListenerAttached = true;
    document.addEventListener("cdss:form-reloaded", (event) => {
        const formContainer = event.detail.formContainer;
        const cdssForm = formContainer.querySelector(".cdss-form");
        if (cdssForm) {
            console.log("[CDSS] Form reloaded — reinitializing CDSS");
            window.initializeCdssForForm(cdssForm);
            window.triggerInitialCdssAnalysis(cdssForm);
        }
    });
}

if (!window.cdssDomLoadListenerAttached) {
    window.cdssDomLoadListenerAttached = true;
    document.addEventListener("DOMContentLoaded", () => {
        if (window.cdssFormReloaded === true) return;
        console.log("[CDSS] DOM fully loaded — initializing all CDSS forms");
        const cdssForms = document.querySelectorAll(".cdss-form");
        cdssForms.forEach((form) => {
            window.initializeCdssForForm(form);
            window.triggerInitialCdssAnalysis(form);
        });
    });
}

// Get alert box height class from form
// alertCell → HTMLElement
function getAlertHeightClass(alertCell) {
    const form = alertCell.closest(".cdss-form");
    return form?.dataset.alertHeightClass || "h-[90px]";
}

// Analyze one field or time group via API
// fieldName → string | null
// finding → string | null
// time → string | null
// alertCell → HTMLElement
// url → string
// token → string
// vitalsOverride → object | null
async function analyzeField(
    fieldName,
    finding,
    time,
    alertCell,
    url,
    token,
    vitalsOverride = null
) {
    if (!alertCell) return;

    let bodyData = {};
    if (time) {
        let vitalsToSend = vitalsOverride || {};
        if (!vitalsOverride) {
            const form = alertCell.closest(".cdss-form");
            const vitalInputs = form?.querySelectorAll(
                `.cdss-input[data-time="${time}"]`
            );
            vitalInputs?.forEach((input) => {
                const name = input.dataset.fieldName;
                vitalsToSend[name] = input.value.trim();
            });
        }
        bodyData = { time, vitals: vitalsToSend };
    } else {
        bodyData = { fieldName, finding };
    }

    console.log("[CDSS] Sending data for analysis:", bodyData);

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify(bodyData),
        });
        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        const alertData = await response.json();
        const endTime = performance.now();
        const startTime = parseFloat(alertCell.dataset.startTime || endTime);
        const duration = (endTime - startTime).toFixed(2);

        console.log(`[CDSS] Response received in ${duration} ms`);
        console.log("[CDSS] Response data:", alertData);

        setTimeout(() => displayAlert(alertCell, alertData, duration), 300);
    } catch (error) {
        console.error("[CDSS] Analysis failed:", error);
        displayAlert(alertCell, {
            alert: "Error analyzing...",
            severity: "CRITICAL",
        });
    }
}

// Display alert result
// alertCell → HTMLElement
// alertData → object
// duration → string (ms)
function displayAlert(alertCell, alertData, duration = null) {
    const heightClass = getAlertHeightClass(alertCell);
    let colorClass = "alert-green";
    if (alertData.severity === "CRITICAL") colorClass = "alert-red";
    else if (alertData.severity === "WARNING") colorClass = "alert-orange";

    let alertContent = "";
    let hasNoAlerts = false;
    let isClickable = false;

    if (
        !alertData.alert ||
        alertData.alert.toLowerCase().includes("no findings")
    ) {
        hasNoAlerts = true;
        alertContent = `<span class="text-white text-center uppercase font-semibold opacity-80">NO FINDINGS</span>`;
    } else {
        if (alertData.alert.includes(";")) {
            const items = alertData.alert
                .split("; ")
                .filter((a) => a.trim() !== "");
            alertContent = `<ul class="list-disc list-inside text-left">${items
                .map((a) => `<li>${a}</li>`)
                .join("")}</ul>`;
        } else {
            alertContent = `<span>${alertData.alert}</span>`;
        }
        isClickable = true;
    }

    console.log(
        `[CDSS] Displaying alert → Severity: ${
            alertData.severity
        } | Duration: ${duration || "?"} ms | Message: ${alertData.alert}`
    );

    alertCell.innerHTML = `
      <div class="alert-box fade-in ${heightClass} ${colorClass} ${
        hasNoAlerts ? "has-no-alert" : ""
    }" style="margin:2px;">
        <div class="alert-message p-1">${alertContent}</div>
      </div>
    `;

    if (isClickable) {
        alertCell
            .querySelector(".alert-box")
            ?.addEventListener("click", () => openAlertModal(alertData));
    }
    delete alertCell.dataset.startTime;
}

// Show "No Alerts" state
// alertCell → HTMLElement
function showDefaultNoAlerts(alertCell) {
    const heightClass = getAlertHeightClass(alertCell);
    console.log("[CDSS] No findings — clearing alert box");
    alertCell.innerHTML = `
      <div class="alert-box has-no-alert alert-green ${heightClass}" style="margin:2.8px;">
        <span class="alert-message text-white text-center font-semibold uppercase opacity-80">NO ALERTS</span>
      </div>
    `;
    alertCell.onclick = null;
}

// Show loading spinner
// alertCell → HTMLElement
function showAlertLoading(alertCell) {
    const heightClass = getAlertHeightClass(alertCell);
    console.log("[CDSS] Analyzing... showing loader");
    alertCell.innerHTML = `
      <div class="alert-box alert-green ${heightClass} flex justify-center items-center" style="margin:2px;">
        <div class="flex items-center gap-2 text-white font-semibold">
          <div class="loading-spinner"></div>
          <span>Analyzing...</span>
        </div>
      </div>
    `;
    alertCell.onclick = null;
}

// Open modal with alert details
// alertData → object
function openAlertModal(alertData) {
    if (document.querySelector(".alert-modal-overlay")) return;
    console.log("[CDSS] Opening alert modal");

    const overlay = document.createElement("div");
    overlay.className = "alert-modal-overlay";

    const alerts = alertData.alert.includes(";")
        ? `<ul class="list-disc list-inside text-left">${alertData.alert
              .split("; ")
              .map((a) => `<li>${a.trim()}</li>`)
              .join("")}</ul>`
        : `<p>${alertData.alert}</p>`;

    const modal = document.createElement("div");
    modal.className = "alert-modal fade-in";
    modal.innerHTML = `
      <button class="close-btn">&times;</button>
      <h2>Alert Details</h2>
      ${alerts}
      ${
          alertData.recommendation
              ? `<h3>Recommendation:</h3><p>${alertData.recommendation}</p>`
              : ""
      }
    `;

    overlay.appendChild(modal);
    document.body.appendChild(overlay);

    const close = () => {
        console.log("[CDSS] Closing alert modal");
        overlay.remove();
    };

    overlay.addEventListener("click", (e) => {
        if (e.target === overlay) close();
    });
    modal.querySelector(".close-btn").addEventListener("click", close);
}

// Add fade-in animation
(function () {
    const style = document.createElement("style");
    style.textContent = `
      .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
      @keyframes fadeIn { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
    `;
    document.head.appendChild(style);
})();
