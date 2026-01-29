
window.vitalCharts = {
    tempChart: null,
    hrChart: null,
    rrChart: null,
    bpChart: null,
    spo2Chart: null,
};


window.modalChartInstance = null;


window.initializeVitalSignsCharts = function (timePoints, vitalsData, options = {}) {
    const { animate = true } = options;

    Object.keys(window.vitalCharts).forEach((key) => {
        if (window.vitalCharts[key]) {
            window.vitalCharts[key].destroy();
            window.vitalCharts[key] = null;
        }
    });


    const lineColors = {
        temperature: '#d32f2f', 
        hr: '#1976d2', 
        rr: '#388e3c',
        bp: '#7b1fa2', 
        spo2: '#f57c00', 
    };

    const vitals = {
        temperature: { label: 'Temp (°C)', elementId: 'tempChart', field: 'temperature', chartKey: 'tempChart' },
        hr: { label: 'HR (bpm)', elementId: 'hrChart', field: 'hr', chartKey: 'hrChart' },
        rr: { label: 'RR (bpm)', elementId: 'rrChart', field: 'rr', chartKey: 'rrChart' },
        bp: { label: 'BP (mmHg)', elementId: 'bpChart', field: 'bp', chartKey: 'bpChart' },
        spo2: { label: 'SpO₂ (%)', elementId: 'spo2Chart', field: 'spo2', chartKey: 'spo2Chart' },
    };

    const formatTimeLabel = (t) => {
        if (!t) return '';
        const parts = t.split(':');
        const hour = parseInt(parts[0], 10);
        const h = ((hour + 11) % 12) + 1;
        const suffix = hour >= 12 ? 'PM' : 'AM';
        return `${h}:${parts[1]} ${suffix}`;
    };

    const formattedLabels = timePoints.map(formatTimeLabel);

    Object.entries(vitals).forEach(([key, vital]) => {
        const canvas = document.getElementById(vital.elementId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const dataValues = timePoints.map((time) => {
            const val = vitalsData?.[time]?.[vital.field];
            return val === null || val === undefined || val === '' ? null : parseFloat(val);
        });

    
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: [
                    {
                        label: vital.label,
                        data: dataValues,
                        borderColor: lineColors[key],
                        backgroundColor: lineColors[key],
                        borderWidth: 1.5,
                        tension: 0.3,
                        pointRadius: 2,
                        pointHoverRadius: 5,
                        fill: false,
                        spanGaps: true,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 10, bottom: 5, left: 5, right: 10 } },
                animation: animate ? { duration: 400 } : false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: { boxWidth: 8, usePointStyle: true, font: { size: 10, weight: '600' } },
                    },
                    tooltip: { enabled: true, bodyFont: { size: 10 } },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 }, maxTicksLimit: 6 },
                    },
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { font: { size: 9 } },
                    },
                },
            },
        });

        canvas.style.cursor = 'zoom-in';
        canvas.onclick = () => {
            window.openChartModal(key, formattedLabels, dataValues, vital.label, lineColors[key]);
        };

        window.vitalCharts[vital.chartKey] = chart;

        
        const inputs = document.querySelectorAll(`input[data-field-name="${vital.field}"]`);
        inputs.forEach((input) => {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            newInput.addEventListener('input', () => {
                const time = newInput.getAttribute('data-time');
                const val = parseFloat(newInput.value) || null;
                const idx = timePoints.indexOf(time);
                if (idx !== -1 && window.vitalCharts[vital.chartKey]) {
                    window.vitalCharts[vital.chartKey].data.datasets[0].data[idx] = val;
                    window.vitalCharts[vital.chartKey].update('none');
                }
            });
        });
    });
};


window.openChartModal = function (vitalKey, labels, data, vitalLabel, color) {
    const modal = document.getElementById('chart-modal');
    const modalCanvas = document.getElementById('modalChartCanvas');
    const modalTitle = document.getElementById('modal-chart-title');

    if (!modal || !modalCanvas) return;

    modalTitle.innerText = `Detailed View: ${vitalLabel}`;
    modal.style.display = 'flex'; // Show modal

    if (window.modalChartInstance) window.modalChartInstance.destroy();

    window.modalChartInstance = new Chart(modalCanvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: vitalLabel,
                    data: data,
                    borderColor: color,
                    backgroundColor: color + '15', 
                    fill: true,
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { padding: 15, titleFont: { size: 16 }, bodyFont: { size: 14 } },
            },
            scales: {
                x: { ticks: { font: { size: 13, weight: 'bold' } } },
                y: { ticks: { font: { size: 13, weight: 'bold' } } },
            },
        },
    });
};

window.closeChartModal = function () {
    const modal = document.getElementById('chart-modal');
    if (modal) modal.style.display = 'none';
};


window.initializeChartScrolling = function () {
    let chartIndex = 0;
    const totalCharts = 5;
    const visibleCharts = 2;
    const chartHeight = 220;
    const maxIndex = totalCharts - visibleCharts;

    const track = document.getElementById('chart-track');
    const upBtn = document.getElementById('chart-up');
    const downBtn = document.getElementById('chart-down');

    if (!track || !upBtn || !downBtn) return;

    function updateChartScroll() {
        track.style.transform = `translateY(${-(chartIndex * chartHeight)}px)`;
        upBtn.style.opacity = chartIndex === 0 ? '0.3' : '1';
        downBtn.style.opacity = chartIndex === maxIndex ? '0.3' : '1';
    }

    const newUpBtn = upBtn.cloneNode(true);
    const newDownBtn = downBtn.cloneNode(true);
    upBtn.parentNode.replaceChild(newUpBtn, upBtn);
    downBtn.parentNode.replaceChild(newDownBtn, downBtn);

    newUpBtn.addEventListener('click', () => {
        if (chartIndex > 0) {
            chartIndex--;
            updateChartScroll();
        }
    });
    newDownBtn.addEventListener('click', () => {
        if (chartIndex < maxIndex) {
            chartIndex++;
            updateChartScroll();
        }
    });

    chartIndex = 0;
    updateChartScroll();
};
