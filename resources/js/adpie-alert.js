// =======================================================
// ADPIE CDSS ALERT SYSTEM - BANNER VERSION
// Handles LIVE TYPING analysis with top banner notifications
// Page-load alerts are pre-rendered by Blade.
// =======================================================

const TYPING_DELAY_MS = 800; // Delay after typing stops before analysis
let debounceTimer;

// Find banner elements for alerts
function findBannersForInput(input) {
    return {
        recommendation: document.getElementById('recommendation-banner'),
        noRecommendation: document.getElementById('no-recommendation-banner')
    };
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
            const banners = findBannersForInput(e.target);

            // If field is cleared, show "no recommendation" banner
            if (finding === '') {
                showNoRecommendationBanner(banners);
                return;
            }

            debounceTimer = setTimeout(() => {
                if (fieldName && finding !== '') {
                    console.log(`[ADPIE] Input → Field: ${fieldNameFormatted} | Value: ${finding}`);

                    showBannerLoading(banners); // Show loading state
                    analyzeField(fieldName, finding, patientId, component, banners, analyzeUrl, csrfToken);
                }
            }, TYPING_DELAY_MS);
        };

        input.addEventListener('input', handleInput);
        input.dataset.alertListenerAttached = 'true';
    });
};

// Analyze ONE field via API (for live typing)
async function analyzeField(fieldName, finding, patientId, component, banners, url, token) {
    if (!banners.recommendation) return;
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
        displayBannerAlert(banners, alertData); // Display in banner
    } catch (error) {
        console.error('[ADPIE] Single analysis failed:', error);
        displayBannerAlert(banners, {
            message: 'Error analyzing field. Please try again.',
            level: 'CRITICAL',
        });
    }
}

// =======================================================
// 2. BANNER DISPLAY FUNCTIONS
// =======================================================

// Display alert in banner
function displayBannerAlert(banners, alertData) {
    if (!banners.recommendation) return;

    console.log(`[ADPIE] Displaying banner → Level: ${alertData.level} | Message: ${alertData.message}`);

    let colorClass = 'alert-green';
    let levelIcon = 'info';
    let levelText = 'Information';
    
    if (alertData.level === 'CRITICAL') {
        colorClass = 'alert-red';
        levelIcon = 'error';
        levelText = 'Critical Alert';
    } else if (alertData.level === 'WARNING') {
        colorClass = 'alert-orange';
        levelIcon = 'warning';
        levelText = 'Warning';
    }

    // Check if no recommendations - show "no recommendation" banner instead
    if (
        !alertData.message ||
        alertData.message.toLowerCase().includes('no findings') ||
        alertData.message.toLowerCase().includes('no recommendations') ||
        alertData.message.toLowerCase().includes('type more') ||
        alertData.message.trim() === ''
    ) {
        showNoRecommendationBanner(banners);
        return;
    }

    // Create short preview (first 60 chars)
    const preview = stripHtml(alertData.message).substring(0, 60) + '...';
    const fullMessage = alertData.message;

    // Hide "no recommendation" banner
    if (banners.noRecommendation) {
        banners.noRecommendation.classList.add('hidden');
    }

    // Show and update recommendation banner
    const banner = banners.recommendation;
    banner.className = `recommendation-banner ${colorClass}`;
    banner.classList.remove('hidden');
    
    banner.innerHTML = `
        <div class="banner-content">
            <div class="banner-icon">
                <span class="material-symbols-outlined">${levelIcon}</span>
            </div>
            <div class="banner-text">
                <div class="banner-title">${levelText}</div>
                <div class="banner-subtitle">
                    ${preview}
                </div>
            </div>
        </div>
        <div class="banner-action">
            <span>View Details</span>
            <span class="material-symbols-outlined">arrow_forward</span>
        </div>
    `;

    // Store the full message and level text directly on the banner element for the modal
    banner.dataset.fullMessage = fullMessage;
    banner.dataset.levelText = levelText;
    banner.dataset.levelIcon = levelIcon;
    banner.dataset.levelIconColor = getLevelIconColor(alertData.level);

    // Re-attach click handler
    banner.onclick = function() {
        window.openRecommendationModal(this);
    };
}

// Helper function to get icon color based on level
function getLevelIconColor(level) {
    if (level === 'CRITICAL') return '#ef4444';
    if (level === 'WARNING') return '#f59e0b';
    return '#059669';
}

// Show "no recommendation" banner and hide recommendation banner
function showNoRecommendationBanner(banners) {
    console.log('[ADPIE] Showing "No Recommendations Yet" banner');
    
    // Hide recommendation banner
    if (banners.recommendation) {
        banners.recommendation.classList.add('hidden');
    }
    
    // Show no recommendation banner
    if (banners.noRecommendation) {
        banners.noRecommendation.classList.remove('hidden');
    }
}

// Show loading state in banner
function showBannerLoading(banners) {
    const banner = banners.recommendation;
    if (!banner) return;
    
    console.log('[ADPIE] Analyzing... showing banner loader');
    
    // Hide "no recommendation" banner during loading
    if (banners.noRecommendation) {
        banners.noRecommendation.classList.add('hidden');
    }
    
    banner.className = 'recommendation-banner alert-green';
    banner.classList.remove('hidden');
    
    banner.innerHTML = `
        <div class="banner-content">
            <div class="banner-icon">
                <div class="banner-loading-spinner"></div>
            </div>
            <div class="banner-text">
                <div class="banner-title">Analyzing...</div>
                <div class="banner-subtitle">Please wait while we review your input</div>
            </div>
        </div>
        <div class="banner-action" style="opacity: 0.5; pointer-events: none;">
            <div class="banner-loading-spinner" style="width: 16px; height: 16px;"></div>
        </div>
    `;
    
    banner.onclick = null;
}

// =======================================================
// 3. HELPER FUNCTIONS
// =======================================================

// Strip HTML tags
function stripHtml(html) {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
}

// Escape HTML for data attributes
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Add modal & banner styles
(function () {
    if (document.getElementById('adpie-banner-styles')) return;
    const style = document.createElement('style');
    style.id = 'adpie-banner-styles';
    style.textContent = `
        .fade-in { 
            animation: fadeIn 0.3s ease-in-out forwards; 
        }
        
        @keyframes fadeIn { 
            from { opacity: 0; transform: scale(0.98); } 
            to { opacity: 1; transform: scale(1); } 
        }
        
        .alert-modal-overlay {
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex; justify-content: center; align-items: center;
            z-index: 1000; backdrop-filter: blur(5px);
        }
        
        .alert-modal {
            background: white; 
            padding: 2rem; 
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 600px; 
            width: 90%; 
            max-height: 80vh;
            position: relative; 
            color: #333;
            overflow: hidden;
        }
        
        .alert-modal h2 {
            margin: 0;
            font-size: 1.5rem; 
            font-weight: 600; 
            color: #222;
        }
        
        .alert-modal h3 {
            font-size: 1.1rem; 
            font-weight: 600;
            margin-top: 1.5rem; 
            margin-bottom: 0.75rem; 
            color: #444;
        }
        
        .alert-modal p { 
            font-size: 1rem; 
            line-height: 1.6; 
            margin-bottom: 0.75rem;
        }

        .alert-modal ul, .alert-modal ol {
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }

        .alert-modal li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .alert-modal .close-btn {
            position: absolute; 
            top: 15px; 
            right: 20px;
            font-size: 24px;
            font-weight: 300; 
            color: #9ca3af;
            background: transparent; 
            border: none !important;
            outline: none !important;
            cursor: pointer; 
            line-height: 1;
            transition: all 0.2s ease;
            z-index: 10;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 0;
            box-shadow: none !important;
        }
        
        .alert-modal .close-btn:hover { 
            color: #1f2937; 
            background: #f3f4f6 !important;
            transform: scale(1.1);
            border: none !important;
            outline: none !important;
        }

        .alert-modal .close-btn:active {
            transform: scale(0.95);
            background: #e5e7eb !important;
            border: none !important;
            outline: none !important;
        }

        .alert-modal .close-btn:focus {
            outline: none !important;
            border: none !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        }

        .modal-content-scroll {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .modal-content-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .modal-content-scroll::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        .modal-content-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        .modal-content-scroll::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        kbd {
            padding: 2px 6px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.875em;
        }
    `;
    document.head.appendChild(style);
})();

// =======================================================
// 4. GLOBAL EVENT LISTENERS
// =======================================================

// Initialize all ADPIE CDSS forms
function initializeAllAdpieCdssForms() {
    console.log('[ADPIE] Initializing all forms with banner system');
    const cdssForms = document.querySelectorAll('.cdss-form');
    cdssForms.forEach((form) => {
        if (form.dataset.component) {
            window.initializeAdpieCdssForForm(form);
        }
    });
}

// Form reload listener
if (!window.adpieCdssFormReloadListenerAttached) {
    window.adpieCdssFormReloadListenerAttached = true;
    document.addEventListener('cdss:form-reloaded', (event) => {
        const formContainer = event.detail.formContainer;
        const cdssForm = formContainer.querySelector('.cdss-form');
        if (cdssForm && cdssForm.dataset.component) {
            console.log('[ADPIE] Form reloaded — reinitializing banner listeners');
            window.initializeAdpieCdssForForm(cdssForm);
        }
    });
}

// Initial page load listener
if (!window.adpieCdssDomLoadListenerAttached) {
    window.adpieCdssDomLoadListenerAttached = true;
    document.addEventListener('DOMContentLoaded', () => {
        if (window.cdssFormReloaded === true) return;
        console.log('[ADPIE] DOM loaded — initializing banner system');
        initializeAllAdpieCdssForms();
    });
}