/**
 * vital-signs-charts.js - Optimized for instant loading
 */

// Store chart instances globally
window.vitalCharts = {
    tempChart: null,
    hrChart: null,
    rrChart: null,
    bpChart: null,
    spo2Chart: null
};

/**
 * Initialize all vital signs charts
 * @param {Array} timePoints - Array of time strings
 * @param {Object} vitalsData - Object with vital sign data keyed by time
 * @param {Object} options - { animate: boolean }
 */
window.initializeVitalSignsCharts = function(timePoints, vitalsData, options = {}) {
    const { animate = true } = options;
    console.log('[VitalCharts] Initializing charts...', { timePoints, vitalsData, animate });

    // Destroy existing charts first to prevent memory leaks
    Object.keys(window.vitalCharts).forEach(key => {
        if (window.vitalCharts[key]) {
            window.vitalCharts[key].destroy();
            window.vitalCharts[key] = null;
        }
    });

    const lineColors = ['#0D47A1', '#7B1FA2', '#1B5E20', '#B71C1C', '#37474F'];

    const vitals = {
        temperature: { label: 'Temperature (°C)', elementId: 'tempChart', field: 'temperature', chartKey: 'tempChart' },
        hr: { label: 'Heart Rate (bpm)', elementId: 'hrChart', field: 'hr', chartKey: 'hrChart' },
        rr: { label: 'Respiratory Rate (bpm)', elementId: 'rrChart', field: 'rr', chartKey: 'rrChart' },
        bp: { label: 'Blood Pressure (mmHg)', elementId: 'bpChart', field: 'bp', chartKey: 'bpChart' },
        spo2: { label: 'SpO₂ (%)', elementId: 'spo2Chart', field: 'spo2', chartKey: 'spo2Chart' },
    };

    // Format time labels efficiently
    const formatTimeLabel = (t) => {
        if (!t) return 'N/A';
        const parts = t.split(':');
        if (parts.length !== 2) return t;
        const hour = parseInt(parts[0], 10);
        const minute = parts[1];
        if (isNaN(hour)) return t;
        const h = ((hour + 11) % 12) + 1;
        const suffix = hour >= 12 ? 'PM' : 'AM';
        return `${h}:${minute} ${suffix}`;
    };

    // Pre-format labels once
    const formattedLabels = timePoints.map(formatTimeLabel);

    Object.entries(vitals).forEach(([key, vital]) => {
        const canvas = document.getElementById(vital.elementId);
        if (!canvas) {
            console.warn(`[VitalCharts] Canvas not found: ${vital.elementId}`);
            return;
        }

        const ctx = canvas.getContext('2d');

        // Extract data values - handle various data formats
        const dataValues = timePoints.map((time) => {
            const record = vitalsData?.[time];
            if (!record) return null;
            
            // Try to get the value
            let value = record[vital.field];
            
            // Parse the value
            if (value === null || value === undefined || value === '') {
                return null;
            }
            
            const parsed = parseFloat(value);
            return isNaN(parsed) ? null : parsed;
        });
        
        console.log(`[VitalCharts] ${vital.field} data:`, dataValues);

        // Create chart with optimized settings
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: formattedLabels,
                datasets: [{
                    label: vital.label,
                    data: dataValues,
                    borderColor: lineColors[0],
                    backgroundColor: lineColors[0],
                    borderWidth: 2.5,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: animate ? { 
                    duration: 600, 
                    easing: 'easeOutQuart' 
                } : false, // Disable animation for instant display
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { 
                            color: '#2c3e50', 
                            font: { size: 13, weight: 'bold' } 
                        },
                    },
                    tooltip: {
                        backgroundColor: '#333',
                        titleColor: '#fff',
                        bodyColor: '#f0f0f0',
                    },
                },
                scales: {
                    x: {
                        ticks: { 
                            color: '#2c3e50', 
                            font: { weight: 'bold' } 
                        },
                        grid: { color: 'rgba(0,0,0,0.1)' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            color: '#2c3e50', 
                            font: { weight: 'bold' } 
                        },
                        grid: { color: 'rgba(0,0,0,0.1)' },
                    },
                },
            },
        });

        // Store chart instance
        window.vitalCharts[vital.chartKey] = chart;

        // Attach input listeners for real-time updates
        const inputs = document.querySelectorAll(`input[data-field-name="${vital.field}"]`);
        inputs.forEach((input) => {
            // Remove old listeners by cloning
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            
            newInput.addEventListener('input', () => {
                const time = newInput.getAttribute('data-time');
                const value = parseFloat(newInput.value) || null;
                const index = timePoints.indexOf(time);
                if (index !== -1 && window.vitalCharts[vital.chartKey]) {
                    window.vitalCharts[vital.chartKey].data.datasets[0].data[index] = value;
                    window.vitalCharts[vital.chartKey].update('none'); // Update without animation
                }
            });
        });
    });

    console.log('[VitalCharts] Charts initialized successfully');
};

/**
 * Initialize chart scrolling functionality
 */
window.initializeChartScrolling = function() {
    let chartIndex = 0;
    const totalCharts = 5;
    const visibleCharts = 2;
    const chartHeight = 244;
    const maxIndex = totalCharts - visibleCharts;

    const track = document.getElementById('chart-track');
    const upBtn = document.getElementById('chart-up');
    const downBtn = document.getElementById('chart-down');
    const fadeTop = document.getElementById('fade-top');
    const fadeBottom = document.getElementById('fade-bottom');

    if (!track || !upBtn || !downBtn) {
        console.warn('[VitalCharts] Chart scrolling elements not found');
        return;
    }

    function updateChartScroll() {
        const offset = -(chartIndex * chartHeight);
        track.style.transform = `translateY(${offset}px)`;
        updateUIVisibility();
    }

    function updateUIVisibility() {
        if (chartIndex === 0) {
            upBtn.classList.add("hidden");
            if (fadeTop) fadeTop.classList.add("hidden");
        } else {
            upBtn.classList.remove("hidden");
            if (fadeTop) fadeTop.classList.remove("hidden");
        }

        if (chartIndex === maxIndex) {
            downBtn.classList.add("hidden");
            if (fadeBottom) fadeBottom.classList.add("hidden");
        } else {
            downBtn.classList.remove("hidden");
            if (fadeBottom) fadeBottom.classList.remove("hidden");
        }
    }

    // Clone buttons to remove old event listeners
    const newUpBtn = upBtn.cloneNode(true);
    const newDownBtn = downBtn.cloneNode(true);
    upBtn.parentNode.replaceChild(newUpBtn, upBtn);
    downBtn.parentNode.replaceChild(newDownBtn, downBtn);

    newUpBtn.addEventListener('click', () => {
        if (chartIndex > 0) chartIndex--;
        updateChartScroll();
    });

    newDownBtn.addEventListener('click', () => {
        if (chartIndex < maxIndex) chartIndex++;
        updateChartScroll();
    });

    // Reset to top and update UI
    chartIndex = 0;
    updateChartScroll();
    console.log('[VitalCharts] Chart scrolling initialized');
};