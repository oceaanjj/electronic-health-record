if (!window.patientSelectedListenerAttached) {
    window.patientSelectedListenerAttached = true;

    document.addEventListener('patient:selected', async (event) => {
        const { patientId, selectUrl } = event.detail;
        const formContainer = document.getElementById('form-content-container');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!formContainer || !selectUrl || !patientId) return;

        const cuteLoader = document.createElement('div');
        cuteLoader.className = 'cute-loader-wrapper';
        cuteLoader.innerHTML = `
            <div class="cute-spinner"></div>
            <span class="loading-text">One moment please...</span>
        `;

        formContainer.classList.add('is-loading');
        formContainer.appendChild(cuteLoader);

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
            const newContentHTML = newDoc.getElementById('form-content-container')?.innerHTML;

            if (!newContentHTML) throw new Error('Content container missing in response');

            const vitalsForm = newDoc.querySelector('#vitals-form');
            let timePoints = [];
            let vitalsData = {};

            if (vitalsForm) {
                timePoints = JSON.parse(vitalsForm.dataset.times || '[]');
                const fetchUrl = vitalsForm.dataset.fetchUrl;
                const pId = vitalsForm.querySelector('input[name="patient_id"]')?.value;
                const dAt = vitalsForm.querySelector('#hidden_date_for_vitals_form')?.value;
                const dNo = vitalsForm.querySelector('#hidden_day_no_for_vitals_form')?.value;

                if (fetchUrl && pId && dAt) {
                    try {
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
                    } catch (e) {
                        console.warn('Vitals pre-fetch failed', e);
                    }
                }
            }

            formContainer.style.opacity = '0.3';

            setTimeout(() => {
                cuteLoader.remove();
                formContainer.classList.remove('is-loading');
                formContainer.innerHTML = newContentHTML;
                window.cdssFormReloaded = true;

                requestAnimationFrame(() => {
                    formContainer.style.opacity = '1';

                    initializeUI(timePoints, vitalsData, selectUrl);
                });
            }, 150);
        } catch (error) {
            console.error('Patient loading failed:', error);
            cuteLoader.remove();
            formContainer.classList.remove('is-loading');
            formContainer.style.opacity = '1';
        }
    });
}

function initializeUI(timePoints, vitalsData, selectUrl) {
    const formContainer = document.getElementById('form-content-container');

    if (window.initializeVitalSignsCharts && timePoints.length > 0) {
        window.initializeVitalSignsCharts(timePoints, vitalsData, { animate: true });
    }

    if (window.initializeChartScrolling) window.initializeChartScrolling();
    if (window.initializeSearchableDropdown) window.initializeSearchableDropdown();
    if (window.initializeVitalSignsDateSync) window.initializeVitalSignsDateSync();

    if (window.initializeDateDayLoader) {
        const headerDropdown = document.querySelector('.searchable-dropdown');
        const url = headerDropdown ? headerDropdown.dataset.selectUrl : selectUrl;
        window.initializeDateDayLoader(url);
    }

    document.dispatchEvent(
        new CustomEvent('cdss:form-reloaded', {
            bubbles: true,
            detail: { formContainer: formContainer },
        }),
    );
}
