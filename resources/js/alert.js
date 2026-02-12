// =======================================================
// Handles CDSS alerts for all forms (ADL, PE, Vitals, etc.)
// =======================================================

const TYPING_DELAY_MS = 500; // Delay after typing stops before analysis in ms
//added delay para hindi kada keyword mag a-analyze

let debounceTimer;

let analysisInterval; // For the moving dots

function startDotAnimation(element) {
    let dots = 0;
    analysisInterval = setInterval(() => {
        dots = (dots + 1) % 4;
        element.textContent = "Analyzing" + ".".repeat(dots);
    }, 400);
}

function stopDotAnimation() {
    clearInterval(analysisInterval);
}

// Find alert cell for a given input
function findAlertCellForInput(input) {
    const fieldName = input.dataset.fieldName;
    const time = input.dataset.time;
    if (time) return document.querySelector(`[data-alert-for-time="${time}"]`);
    if (fieldName) return document.querySelector(`[data-alert-for="${fieldName}"]`);
    return null;
}

// =======================================================
// BUTTON STATE MANAGEMENT
// =======================================================

/**
 * Checks if any input in the form has a value and toggles the CDSS button state.
 * @param {HTMLElement} form
 */
function updateCdssButtonState(form) {
    const cdssBtn = form.querySelector('.cdss-btn');
    if (!cdssBtn) return;

    const inputs = form.querySelectorAll('.cdss-input');
    let hasInput = false;

    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value.trim() !== '') {
            hasInput = true;
            break;
        }
    }

    if (hasInput) {
        // Enable button
        cdssBtn.classList.remove('opacity-50', 'pointer-events-none', 'cursor-not-allowed');
        if (cdssBtn.tagName === 'BUTTON') {
            cdssBtn.disabled = false;
        }
    } else {
        // Disable button
        cdssBtn.classList.add('opacity-50', 'pointer-events-none', 'cursor-not-allowed');
        if (cdssBtn.tagName === 'BUTTON') {
            cdssBtn.disabled = true;
        }
    }
}

// =======================================================
// LIVE TYPING ANALYSIS (Requires analyzeField)
// =======================================================

// Initialize CDSS listeners for a form
window.initializeCdssForForm = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const inputs = form.querySelectorAll('.cdss-input');
    inputs.forEach((input) => {
        if (input.dataset.alertListenerAttached) return;

        input.addEventListener('input', (e) => {
            updateCdssButtonState(form);
            const finding = e.target.value.trim();
            const alertCell = findAlertCellForInput(e.target);

            if (!alertCell) return;

            // 1. Instant Reset if field is cleared
            if (finding === '') {
                showDefaultNoAlerts(alertCell);
                return;
            }

            // 2. INSTANT LOADING: Trigger the bubble immediately on typing
            if (!alertCell.querySelector('.glass-spinner')) {
                showAlertLoading(alertCell);
            }

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                analyzeField(e.target.dataset.fieldName, finding, e.target.dataset.time, alertCell, analyzeUrl, csrfToken);
            }, TYPING_DELAY_MS);
        });
        input.dataset.alertListenerAttached = 'true';
    });
};

// Analyze ONE field or time group via API (for live typing)
async function analyzeField(fieldName, finding, time, alertCell, url, token, vitalsOverride = null) {
    if (!alertCell) return;

    let bodyData = {};
    if (time) {
        let vitalsToSend = vitalsOverride || {};
        if (!vitalsOverride) {
            const form = alertCell.closest('.cdss-form');
            const vitalInputs = form?.querySelectorAll(`.cdss-input[data-time="${time}"]`);
            vitalInputs?.forEach((input) => {
                const name = input.dataset.fieldName;
                vitalsToSend[name] = input.value.trim();
            });
        }
        bodyData = { time, vitals: vitalsToSend };
    } else {
        bodyData = { fieldName, finding };
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify(bodyData),
        });
        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        const alertData = await response.json();
        const endTime = performance.now();
        const startTime = parseFloat(alertCell.dataset.startTime || endTime);
        const duration = (endTime - startTime).toFixed(2);

        console.log(`[CDSS] Single response received in ${duration} ms`);

        // Display alert immediately (no 300ms delay)
        displayAlert(alertCell, alertData, duration);
    } catch (error) {
        console.error('[CDSS] Single analysis failed:', error);
        displayAlert(alertCell, {
            alert: 'Error analyzing...',
            severity: 'CRITICAL',
        });
    }
}

// =======================================================
//  BATCH ANALYSIS (For initial page load)
// =======================================================

// Trigger CDSS analysis for all pre-filled inputs
window.triggerInitialCdssAnalysis = async function (form) {
    const analyzeUrl = form.dataset.analyzeUrl; // Single URL (for fallback)
    const batchAnalyzeUrl = form.dataset.batchAnalyzeUrl; // Batch URL
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!batchAnalyzeUrl || !csrfToken) {
        console.error('Missing "data-batch-analyze-url" or CSRF token.', form);
        // Fallback to old, slow method if batch URL is missing
        if (analyzeUrl) {
            console.warn(
                '[CDSS] data-batch-analyze-url not found!, \n Falling back to old (single-analyze) analysis method.',
            );
            const inputs = form.querySelectorAll('.cdss-input');
            inputs.forEach((input) => {
                const finding = input.value.trim();
                const alertCell = findAlertCellForInput(input);
                if (finding !== '' && alertCell) {
                    analyzeField(
                        input.dataset.fieldName,
                        finding,
                        input.dataset.time,
                        alertCell,
                        analyzeUrl,
                        csrfToken,
                    );
                } else if (alertCell) {
                    showDefaultNoAlerts(alertCell);
                }
            });
        }
        return;
    }

    console.log(`[CDSS] Triggering BATCH analysis for form: ${form.id || '(unnamed)'}`);

    const inputs = form.querySelectorAll('.cdss-input');
    const analysisGroups = new Map();

    // 1. GATHER ALL GROUPS
    inputs.forEach((input) => {
        const fieldName = input.dataset.fieldName;
        const finding = input.value.trim();
        const time = input.dataset.time;
        const alertCell = findAlertCellForInput(input);

        if (!alertCell) return;
        if (finding === '') {
            showDefaultNoAlerts(alertCell);
            return;
        }

        let key = time ? `time-${time}` : `field-${fieldName}`;
        if (!analysisGroups.has(key)) {
            analysisGroups.set(
                key,
                time ? { time, alertCell, fields: {} } : { time: null, alertCell, fieldName, finding },
            );
        }
        if (time) analysisGroups.get(key).fields[fieldName] = finding;
    });

    // 2. PREPARE BATCH
    const groups = Array.from(analysisGroups.values());
    if (groups.length === 0) {
        console.log('[CDSS] No pre-filled inputs to analyze.');
        return; // Nothing to analyze
    }

    groups.forEach((group) => {
        showAlertLoading(group.alertCell);
        group.alertCell.dataset.startTime = performance.now();
    });

    const batchPayload = groups.map((group) => {
        if (group.time) {
            return { time: group.time, vitals: group.fields };
        } else {
            return { fieldName: group.fieldName, finding: group.finding };
        }
    });

    console.log(`[CDSS] Sending ${batchPayload.length} items for batch analysis...`);

    // 3. SEND ONE SINGLE FETCH REQUEST
    try {
        const response = await fetch(batchAnalyzeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ batch: batchPayload }), // Send as { "batch": [...] }
        });

        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        const batchResults = await response.json(); // Expecting an array

        if (!Array.isArray(batchResults) || batchResults.length !== groups.length) {
            throw new Error('Batch response mismatch or invalid format.');
        }

        console.log(`[CDSS] Received ${batchResults.length} batch results.`);

        // 4. DISPLAY ALL RESULTS
        batchResults.forEach((alertData, index) => {
            const group = groups[index];
            const alertCell = group.alertCell;
            const endTime = performance.now();
            const startTime = parseFloat(alertCell.dataset.startTime || endTime);
            const duration = (endTime - startTime).toFixed(2);

            // Display alert immediately
            displayAlert(alertCell, alertData, duration);
        });
    } catch (error) {
        console.error('[CDSS] Batch analysis failed:', error);
        groups.forEach((group) => {
            displayAlert(group.alertCell, {
                alert: 'Batch Error',
                severity: 'CRITICAL',
            });
        });
    }
};

// =======================================================
//  HELPER FUNCTIONS (Display, Loading, Modal)
// =======================================================

// Get alert box height class from form
function getAlertHeightClass(alertCell) {
    const form = alertCell.closest('.cdss-form');
    return form?.dataset.alertHeightClass || 'h-[90px]'; // Default height
}

// Display alert result
function displayAlert(alertCell, alertData) {
    if (!alertCell) return;
    stopDotAnimation();
    
    let isFindings = alertData.alert && !alertData.alert.toLowerCase().includes('no findings');
    
    if (isFindings) {
        // ALERT FOUND: High visibility Yellow Icon
        alertCell.innerHTML = `
            <div class="alert-wrapper">
                <div class="alert-icon-btn is-active fade-in">
                    <span class="material-symbols-outlined">add_alert</span>
                </div>
                <div class="alert-bubble show-pop">
                    <span class="font-bold" style="color: #f59e0b;">Alert available!</span>
                </div>
            </div>
        `;
        alertCell.querySelector('.alert-icon-btn').addEventListener('click', () => openAlertModal(alertData));
    } else {
        // NO FINDINGS: Gray Icon
        alertCell.innerHTML = `
            <div class="alert-wrapper">
                <div class="alert-icon-btn">
                    <span class="material-symbols-outlined">notifications</span>
                </div>
                <div class="alert-bubble show-pop">
                    <span class="text-gray-400">No alerts.</span>
                </div>
            </div>
        `;
    }

    // Dissolve logic
    setTimeout(() => {
        const bubble = alertCell.querySelector('.alert-bubble');
        const wrapper = alertCell.querySelector('.alert-wrapper');
        
        if (bubble) {
            bubble.style.filter = 'blur(10px)';
            bubble.style.opacity = '0';
            bubble.style.transform = 'translateY(-10px)'; 
            
            setTimeout(() => {
                bubble.remove();
                // Only dim if it's NOT a yellow alert
                if (!isFindings && wrapper) {
                    wrapper.classList.add('is-dimmed');
                }
            }, 500);
        }
    }, 3000);
}

function showDefaultNoAlerts(alertCell) {
    if (!alertCell) return;
    alertCell.innerHTML = `
        <div class="alert-wrapper is-dimmed">
            <div class="alert-icon-btn">
                <span class="material-symbols-outlined">notifications</span>
            </div>
        </div>
    `;
}

function showAlertLoading(alertCell) {
    if (!alertCell) return;
    alertCell.innerHTML = `
        <div class="alert-wrapper">
            <div class="alert-icon-btn" style="background: rgba(59, 130, 246, 0.1);">
                <div class="glass-spinner"></div>
            </div>
            <div class="alert-bubble show-pop">
                <span class="text-blue-500 font-medium" id="loading-text">Analyzing</span>
            </div>
        </div>
    `;
    startDotAnimation(document.getElementById('loading-text'));
}
// Open modal with alert details
// alertData → object
function openAlertModal(alertData) {
    if (document.querySelector('.alert-modal-overlay')) return;

    const overlay = document.createElement('div');
    overlay.className = 'alert-modal-overlay';

    const alerts = alertData.alert.includes(';')
        ? `<ul class="list-disc list-inside text-left">${alertData.alert
              .split('; ')
              .map((a) => `<li>${a.trim()}</li>`)
              .join('')}</ul>`
        : `<p>${alertData.alert}</p>`;

    const modal = document.createElement('div');
    modal.className = 'alert-modal fade-in';
    modal.innerHTML = `
      <button class="close-btn">&times;</button>
      <h2>Alert Details</h2>
      ${alerts}
      ${alertData.recommendation ? `<h3>Recommendation:</h3><p>${alertData.recommendation}</p>` : ''}
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

// Add fade-in animation
(function () {
    const style = document.createElement('style');
    style.textContent = `
      .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
      @keyframes fadeIn { from { opacity: 0; transform: scale(0.98); } to { opacity: 1; transform: scale(1); } }
    `;
    document.head.appendChild(style);
})();

// =======================================================
//  GLOBAL EVENT LISTENERS
// =======================================================

/**
 * Initializes all CDSS forms on the page.
 * Runs both typing listeners and initial batch analysis.
 */
function initializeAllCdssForms() {
    console.log('[CDSS] Initializing all forms...');
    const cdssForms = document.querySelectorAll('.cdss-form');
    cdssForms.forEach((form) => {
        window.initializeCdssForForm(form); // Init typing
        window.triggerInitialCdssAnalysis(form); // Init batch load
    });
}

// --- Listener for when a patient is changed ---
// (Fired by patient-loader.js)
if (!window.cdssFormReloadListenerAttached) {
    window.cdssFormReloadListenerAttached = true;
    document.addEventListener('cdss:form-reloaded', (event) => {
        const formContainer = event.detail.formContainer;
        const cdssForm = formContainer.querySelector('.cdss-form');
        if (cdssForm) {
            console.log('[CDSS] Form reloaded — reinitializing CDSS');
            window.initializeCdssForForm(cdssForm); // Re-init typing
            window.triggerInitialCdssAnalysis(cdssForm); // Re-init batch load
        }
    });
}

// --- Listener for the very first page load ---
if (!window.cdssDomLoadListenerAttached) {
    window.cdssDomLoadListenerAttached = true;
    document.addEventListener('DOMContentLoaded', () => {
        // This flag is set by patient-loader.js to prevent double-loading
        if (window.cdssFormReloaded === true) return;

        console.log('[CDSS] DOM fully loaded — initializing all CDSS forms');
        initializeAllCdssForms();
    });
}