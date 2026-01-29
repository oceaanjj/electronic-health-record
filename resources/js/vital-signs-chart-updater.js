// resources/js/vital-signs-chart-updater.js

(function() {
    const ctx = document.getElementById('vitalSignChart');
    let vitalChart; // Declare vitalChart in a scope accessible by updateChart

    const lineColors = [
        '#0D47A1', // rich deep blue
        '#7B1FA2', // royal violet
        '#1B5E20', // dark green
        '#B71C1C', // rich red
        '#37474F', // steel gray
        '#4E342E', // cocoa brown
        '#006064', // deep cyan
        '#512DA8'  // indigo
    ];
    const timePoints = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
    const labels = ['TEMP', 'HR (bpm)', 'RR (bpm)', 'BP (mmHg)', 'SpO₂ (%)'];

    function initializeChart(initialVitalsData) {
        const datasets = createChartDatasets(initialVitalsData);

        if (ctx) {
            vitalChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#2c3e50', font: { size: 13, weight: 'bold' } }
                        },
                        tooltip: {
                            backgroundColor: '#333',
                            titleColor: '#fff',
                            bodyColor: '#f0f0f0'
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#2c3e50', font: { weight: 'bold' } },
                            grid: { color: 'rgba(0,0,0,0.1)' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#2c3e50', font: { weight: 'bold' } },
                            grid: { color: 'rgba(0,0,0,0.1)' }
                        }
                    }
                }
            });
        }
    }

    function createChartDatasets(vitalsData) {
        return timePoints.map((time, index) => {
            const vitalRecord = vitalsData[time] || {};
            return {
                label: formatTime(time),
                data: [
                    vitalRecord.temperature ?? null,
                    vitalRecord.hr ?? null,
                    vitalRecord.rr ?? null,
                    vitalRecord.bp ?? null,
                    vitalRecord.spo2 ?? null,
                ],
                borderColor: lineColors[index % lineColors.length],
                backgroundColor: lineColors[index % lineColors.length],
                borderWidth: 2.5,
                tension: 0,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: false
            };
        });
    }

    function formatTime(timeString) {
        const [hour, minute] = timeString.split(':');
        const date = new Date();
        date.setHours(parseInt(hour));
        date.setMinutes(parseInt(minute));
        return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
    }

    function updateChart(newVitalsData) {
        if (vitalChart) {
            vitalChart.data.datasets = createChartDatasets(newVitalsData);
            vitalChart.update();
        }
    }

    // Expose updateChart to the global scope if needed by other scripts
    window.updateVitalSignChart = updateChart;
    window.initializeVitalSignChart = initializeChart;

    // Initial chart rendering when the DOM is loaded
    document.addEventListener("DOMContentLoaded", function () {
        // Assuming initialVitalsData is passed from Blade as a global variable
        if (window.initialVitalsData) {
            initializeChart(window.initialVitalsData);
        }

        const patientIdHidden = document.getElementById('patient_id_hidden');
        const dateSelector = document.getElementById('date_selector');
        const dayNoSelector = document.getElementById('day_no_selector');
        const patientSelectForm = document.getElementById('patient-select-form');

        if (patientSelectForm) {
            // Prevent default form submission
            patientSelectForm.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        }

        // Function to fetch and update data
        async function fetchAndUpdateVitals() {
            const patientId = patientIdHidden ? patientIdHidden.value : '';
            const date = dateSelector ? dateSelector.value : '';
            const dayNo = dayNoSelector ? dayNoSelector.value : '';

            if (!patientId) {
                // Optionally clear chart or show message if no patient is selected
                updateChart({}); // Clear chart
                return;
            }

            try {
                const response = await fetch(patientSelectForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        patient_id: patientId,
                        date: date,
                        day_no: dayNo
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log("Fetched data:", data); // Debugging

                // Update the chart with the new data
                updateChart(data.vitalsData);

                // Update the form fields with the new data
                updateFormFields(data.vitalsData);

                // Update the alerts section
                updateAlerts(data.vitalsData);

            } catch (error) {
                console.error("Error fetching vital signs data:", error);
            }
        }

        // Attach event listeners to trigger update
        if (patientIdHidden) {
            patientIdHidden.addEventListener('change', fetchAndUpdateVitals);
        }
        if (dateSelector) {
            dateSelector.addEventListener('change', fetchAndUpdateVitals);
        }
        if (dayNoSelector) {
            dayNoSelector.addEventListener('change', fetchAndUpdateVitals);
        }

        function updateFormFields(vitalsData) {
            timePoints.forEach(time => {
                const vitalRecord = vitalsData[time] || {};
                document.querySelector(`input[name="temperature_${time}"]`).value = vitalRecord.temperature ?? '';
                document.querySelector(`input[name="hr_${time}"]`).value = vitalRecord.hr ?? '';
                document.querySelector(`input[name="rr_${time}"]`).value = vitalRecord.rr ?? '';
                document.querySelector(`input[name="bp_${time}"]`).value = vitalRecord.bp ?? '';
                document.querySelector(`input[name="spo2_${time}"]`).value = vitalRecord.spo2 ?? '';
            });
        }

        function updateAlerts(vitalsData) {
            timePoints.forEach(time => {
                const vitalRecord = vitalsData[time] || {};
                const alertBox = document.querySelector(`.alert-box[data-alert-for-time="${time}"]`);
                if (alertBox) {
                    const alerts = vitalRecord.alerts ? vitalRecord.alerts.split('; ') : [];
                    const severity = vitalRecord.news_severity || 'NONE';

                    let alertContent = '';
                    let colorClass = 'text-white'; // Default for NONE

                    if (alerts.length > 0 && severity !== 'NONE') {
                        alertContent = alerts.join('<br>');
                        if (severity === 'CRITICAL') {
                            colorClass = 'text-red-600';
                        } else if (severity === 'WARNING') {
                            colorClass = 'text-orange-500';
                        } else if (severity === 'INFO') {
                            colorClass = 'text-blue-500';
                        }
                    } else {
                        alertContent = '<span class="opacity-70 text-white font-semibold">NO ALERTS</span>';
                    }

                    alertBox.innerHTML = `<span class="${colorClass} font-semibold">${alertContent}</span>`;
                }
            });
        }
    });
})();
