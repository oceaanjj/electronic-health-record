if (!window.patientSelectedListenerAttached) {
    window.patientSelectedListenerAttached = true;

    document.addEventListener('patient:selected', async (event) => {
        const { patientId, selectUrl } = event.detail;
        const formContainer = document.getElementById('form-content-container');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!formContainer || !selectUrl || !patientId) return;

        // 1. Create and Show Pumping Logo Loader
        const cuteLoader = document.createElement('div');
        cuteLoader.className = 'cute-loader-wrapper';
        cuteLoader.innerHTML = `
            <div class="loader-card">
                <div class="logo-container">
                    <img src="img/loading.png" alt="Logo" class="shining-logo">
                </div>
                <span class="loading-text">One moment please...</span>
            </div>
        `;

        document.body.classList.add('is-loading');
        document.body.appendChild(cuteLoader);

        try {
            const response = await fetch(selectUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: `patient_id=${encodeURIComponent(patientId)}`,
            });

            if (!response.ok) throw new Error(`Status: ${response.status}`);

            const htmlText = await response.text();
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(htmlText, 'text/html');
            
            // FIX: Guard against undefined/null content
            const newContainer = newDoc.getElementById('form-content-container');
            const newContentHTML = newContainer ? newContainer.innerHTML : null;

            if (!newContentHTML) {
                throw new Error('Content container missing in response');
            }

            // Extract Vitals Data
            const vitalsForm = newDoc.querySelector('#vitals-form');
            let timePoints = [], vitalsData = {};
            
            if (vitalsForm) {
                try {
                    timePoints = JSON.parse(vitalsForm.dataset.times || '[]');
                    const fetchUrl = vitalsForm.dataset.fetchUrl;
                    const pId = vitalsForm.querySelector('input[name="patient_id"]')?.value;
                    const dAt = vitalsForm.querySelector('#hidden_date_for_vitals_form')?.value;
                    const dNo = vitalsForm.querySelector('#hidden_day_no_for_vitals_form')?.value;

                    if (fetchUrl && pId && dAt) {
                        const vRes = await fetch(fetchUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-TOKEN': csrfToken,
                                Accept: 'application/json',
                            },
                            body: `patient_id=${encodeURIComponent(pId)}&date=${encodeURIComponent(dAt)}&day_no=${encodeURIComponent(dNo)}`,
                        });
                        if (vRes.ok) vitalsData = await vRes.json();
                    }
                } catch (e) {
                    console.warn('Vitals processing failed', e);
                }
            }

            formContainer.style.transition = 'opacity 0.4s ease';
            formContainer.style.opacity = '0';

            setTimeout(() => {

                cuteLoader.style.transition = 'opacity 0.3s ease';
                cuteLoader.style.opacity = '0';

                setTimeout(() => {
                cuteLoader.remove();
        document.body.classList.remove('is-loading');
        
        formContainer.innerHTML = newContentHTML;
        
        requestAnimationFrame(() => {
            // 3. Fade the new content back in slowly
            formContainer.style.opacity = '1';
            
            // 4. Sequence the UI initialization
                    initializeUI(timePoints, vitalsData, selectUrl);
                });
            }, 300); // Wait for loader to fade out
        }, 100);
                } catch (error) {
                    console.error('Patient loading failed:', error);
                    if (cuteLoader) cuteLoader.remove();
                    document.body.classList.remove('is-loading');
                    formContainer.style.opacity = '1';
                }
            });
        }

function initializeUI(timePoints, vitalsData, selectUrl) {
    const formContainer = document.getElementById('form-content-container');
    if (!formContainer) return;

    if (window.initializeVitalSignsCharts && timePoints.length > 0) {
        window.initializeVitalSignsCharts(timePoints, vitalsData, { animate: true });
    }

    if (window.initializeChartScrolling) window.initializeChartScrolling();
    if (window.initializeSearchableDropdown) window.initializeSearchableDropdown();
    if (window.initializeVitalSignsDateSync) window.initializeVitalSignsDateSync();

    if (window.initializeDateDayLoader) {
        const headerDropdown = document.querySelector('.searchable-dropdown');
        // FIX: Added fallback to prevent undefined URL
        const url = headerDropdown?.dataset?.selectUrl || selectUrl;
        if (url) window.initializeDateDayLoader(url);
    }

    document.dispatchEvent(
        new CustomEvent('cdss:form-reloaded', {
            bubbles: true,
            detail: { formContainer: formContainer },
        }),
    );
}