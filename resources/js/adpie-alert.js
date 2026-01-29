// =======================================================
// ADPIE CDSS ALERT SYSTEM
// Handles LIVE TYPING analysis only.
// Page-load alerts are now pre-rendered by Blade.
// =======================================================

const TYPING_DELAY_MS = 800; // Delay after typing stops before analysis
let debounceTimer;

// Find alert cell for a given input (ADPIE version)
function findAlertCellForInput(input) {
    const fieldName = input.dataset.fieldName;
    if (fieldName) {
        return document.querySelector(`[data-alert-for="${fieldName}"]`);
    }
    return null;
}

// =======================================================
// 1. LIVE TYPING ANALYSIS
// =======================================================

// Initialize CDSS listeners for a form
window.initializeAdpieCdssForForm = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const component = form.dataset.component;
    const patientId = form.dataset.patientId;

    if (!analyzeUrl || !csrfToken || !component || !patientId) {
        console.error('[ADPIE] Missing form data: analyze-url, component, patient-id, or CSRF token.', form);
        return;
    }

    console.log(`[ADPIE] Initializing typing listeners for: ${component}`);

    const inputs = form.querySelectorAll('.cdss-input');
    inputs.forEach((input) => {
        if (input.dataset.alertListenerAttached) return; // Prevent duplicate listeners

        const handleInput = (e) => {
            clearTimeout(debounceTimer);
            const fieldName = e.target.dataset.fieldName;
            const fieldNameFormatted = fieldName.charAt(0).toUpperCase() + fieldName.slice(1);
            const finding = e.target.value.trim();
            const alertCell = findAlertCellForInput(e.target);

            if (alertCell && finding === '') {
                showDefaultNoAlerts(alertCell); // Show default if field is cleared
                return;
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && finding !== '' && alertCell) {
                    console.log(`[ADPIE] Input → Field: ${fieldNameFormatted} | Value: ${finding}`);

                    showAlertLoading(alertCell); // Show loading spinner *only when typing*
                    analyzeField(fieldName, finding, patientId, component, alertCell, analyzeUrl, csrfToken);
                }
            }, TYPING_DELAY_MS);
        };

        input.addEventListener('input', handleInput);
        input.dataset.alertListenerAttached = 'true';
    });
};

// Analyze ONE field via API (for live typing)
async function analyzeField(fieldName, finding, patientId, component, alertCell, url, token) {
    if (!alertCell) return;
    console.log(`[ADPIE] Sending single analysis for: ${fieldName}`);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                fieldName,
                finding,
                patient_id: patientId,
                component: component,
            }),
        });
        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        const alertData = await response.json();
        console.log(`[ADPIE] Single response received for: ${fieldName}`, alertData);
        displayAlert(alertCell, alertData); // Display immediately
    } catch (error) {
        console.error('[ADPIE] Single analysis failed:', error);
        displayAlert(alertCell, {
            message: 'Error analyzing...',
            level: 'CRITICAL',
        });
    }
}

// =======================================================
// 2. BATCH ANALYSIS (REMOVED)
// This is no longer needed. Alerts are pre-loaded by Blade.
// =======================================================

// =======================================================
// 3. HELPER FUNCTIONS (Display, Loading, Modal)
// =======================================================

// Display alert result (ADPIE version)
function displayAlert(alertCell, alertData) {
    if (!alertCell) return;

    console.log(`[ADPIE] Displaying alert → Level: ${alertData.level} | Message: ${alertData.message}`);

    let colorClass = 'alert-green';
    if (alertData.level === 'CRITICAL') colorClass = 'alert-red';
    else if (alertData.level === 'WARNING') colorClass = 'alert-orange';

    let alertContent = '';
    let hasNoAlerts = false;

    if (
        !alertData.message ||
        alertData.message.toLowerCase().includes('no findings') ||
        alertData.message.toLowerCase().includes('no recommendations') ||
        alertData.message.includes('No Recommendations')
    ) {
        hasNoAlerts = true;
        alertContent =
            '<span class="text-white text-center uppercase font-semibold opacity-80">NO RECOMMENDATIONS</span>';
    } else {
        alertContent = `<span>${alertData.message}</span>`;
    }

    alertCell.innerHTML = `
      <div class="alert-box fade-in ${colorClass} ${hasNoAlerts ? 'has-no-alert' : ''}" 
           style="height:90px; margin:2px;">
        <div class="alert-message p-1">${alertContent}</div>
      </div>
    `;

    if (!hasNoAlerts) {
        alertCell.querySelector('.alert-box')?.addEventListener('click', () => openAlertModal(alertData));
    }
}

// Show "No Alerts" state (ADPIE version)
function showDefaultNoAlerts(alertCell) {
    if (!alertCell) return;
    console.log('[ADPIE] Clearing alert box (No Alerts)');
    alertCell.innerHTML = `
      <div class="alert-box has-no-alert alert-green" style="height:90px; margin:2.8px;">
        <span class="alert-message text-white text-center font-semibold uppercase opacity-80">NO RECOMMENDATIONS</span>
      </div>
    `;
    alertCell.onclick = null;
}

// Show loading spinner
function showAlertLoading(alertCell) {
    if (!alertCell) return;
    console.log('[ADPIE] Analyzing... showing loader');
    alertCell.innerHTML = `
      <div class="alert-box alert-green flex justify-center items-center" style="height:90px; margin:2px;">
        <div class="flex items-center gap-2 text-white font-semibold">
          <div class="loading-spinner"></div>
          <span>Analyzing...</span>
        </div>
      </div>
    `;
    alertCell.onclick = null;
}

// Open modal with alert details (ADPIE version)
function openAlertModal(alertData) {
    if (document.querySelector('.alert-modal-overlay')) return;

    const overlay = document.createElement('div');
    overlay.className = 'alert-modal-overlay fade-in';

    const alertContent = alertData.message || alertData.alert || 'No details available.';

    const recommendation = alertData.recommendation || null;

    const modal = document.createElement('div');
    modal.className = 'alert-modal fade-in';
    modal.innerHTML = `
      <button class="close-btn">&times;</button>
      <h2>Alert Details</h2>
      <p>${alertContent}</p>
      ${recommendation ? `<h3>Recommendation:</h3><p>${recommendation}</p>` : ''}
    `;

    overlay.appendChild(modal);
    document.body.appendChild(overlay);

    const close = () => {
        overlay.remove();
    };

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) close();
    });
    modal.querySelector('.close-btn').addEventListener('click', close);
}

// Add fade-in animation & modal styles
(function () {
    if (document.getElementById('adpie-alert-styles')) return;
    const style = document.createElement('style');
    style.id = 'adpie-alert-styles';
    style.textContent = `
      .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
      @keyframes fadeIn { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
      .alert-modal-overlay {
        position: fixed; top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex; justify-content: center; align-items: center;
        z-index: 1000; backdrop-filter: blur(5px);
      }
      .alert-modal {
        background: white; padding: 2rem; border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        max-width: 500px; width: 90%; position: relative; color: #333;
      }
      .alert-modal h2 {
        margin-top: 0; font-size: 1.5rem; font-weight: 600; color: #222;
      }
      .alert-modal h3 {
        font-size: 1.1rem; font-weight: 600;
        margin-top: 1rem; margin-bottom: 0.5rem; color: #444;
      }
      .alert-modal p { font-size: 1rem; line-height: 1.6; }
      .alert-modal .close-btn {
        position: absolute; top: 10px; right: 15px;
        font-size: 2rem; font-weight: bold; color: #888;
        background: none; border: none; cursor: pointer; line-height: 1;
      }
      .alert-modal .close-btn:hover { color: #333; }
    `;
    document.head.appendChild(style);
})();

// =======================================================
// 4. GLOBAL EVENT LISTENERS (Simplified)
// =======================================================

// Initializes all ADPIE CDSS forms on the page.
function initializeAllAdpieCdssForms() {
    console.log('[ADPIE] Initializing all forms D-P-I-E');
    const cdssForms = document.querySelectorAll('.cdss-form');
    cdssForms.forEach((form) => {
        // Check if it's an ADPIE form by looking for 'data-component'
        if (form.dataset.component) {
            window.initializeAdpieCdssForForm(form); // Init typing ONLY
        }
    });
}

// Listener for when a patient/form is changed
if (!window.adpieCdssFormReloadListenerAttached) {
    window.adpieCdssFormReloadListenerAttached = true;
    document.addEventListener('cdss:form-reloaded', (event) => {
        const formContainer = event.detail.formContainer;
        const cdssForm = formContainer.querySelector('.cdss-form');
        if (cdssForm && cdssForm.dataset.component) {
            console.log('[ADPIE] Form reloaded — reinitializing typing listeners');
            window.initializeAdpieCdssForForm(cdssForm);
        }
    });
}

// Listener for the very first page load
if (!window.adpieCdssDomLoadListenerAttached) {
    window.adpieCdssDomLoadListenerAttached = true;
    document.addEventListener('DOMContentLoaded', () => {
        if (window.cdssFormReloaded === true) return; // Prevent double-loading
        console.log('[ADPIE] DOM fully loaded — initializing forms');
        initializeAllAdpieCdssForms();
    });
}
