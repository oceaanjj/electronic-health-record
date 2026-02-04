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
                        beginAtZero: false, 
                        grace: '10%'
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


function openChartModal(chartId, title) {
    // 1. Start closing the sidebar immediately
    window.closeNav();

    // 2. Wait for the sidebar transition (300ms) before showing the modal
    setTimeout(() => {
        const modal = document.getElementById('chart-modal');
        const modalContent = modal.querySelector('div'); // The white card inside
        const modalTitle = document.getElementById('modal-chart-title');
        const originalCanvas = document.getElementById(chartId);
        const modalCanvas = document.getElementById('modalChartCanvas');

        if (!originalCanvas || !modal) return;

        // Prepare Modal Content
        modalTitle.innerText = title;
        
        // Apply the reveal animation class
        modalContent.classList.remove('modal-reveal');
        void modalContent.offsetWidth; // Trigger reflow to restart animation
        modalContent.classList.add('modal-reveal');

        // Show the modal
        modal.style.display = 'flex';

        // 3. Initialize/Clone the Chart
        if (modalChartInstance) modalChartInstance.destroy();
        const originalChart = Chart.getChart(originalCanvas);
        
        if (originalChart) {
            modalChartInstance = new Chart(modalCanvas, {
                type: originalChart.config.type,
                data: JSON.parse(JSON.stringify(originalChart.config.data)),
                options: {
                    ...originalChart.config.options,
                    maintainAspectRatio: false,
                    responsive: true,
                    animation: {
                        duration: 800, // Smoothly draw the lines after reveal
                        easing: 'easeOutQuart'
                    }
                }
            });
        }
    }, 300); // This 300ms matches the sidebar slide duration
}

function closeChartModal() {
    const modal = document.getElementById('chart-modal');
    if (!modal) return;

    // Hide the modal immediately
    modal.style.display = 'none';

    // Destroy the Chart.js instance to prevent memory leaks
    if (window.modalChartInstance) {
        window.modalChartInstance.destroy();
        window.modalChartInstance = null;
    }
}


// This function should be linked to your Hamburger Menu button
window.handleSidebarToggle = function() {
    const modal = document.getElementById('chart-modal');
    
    // If a chart is open, close it first with the animation
    if (modal && modal.style.display !== 'none') {
        closeChartModal();
        
        // Delay the sidebar opening slightly so the modal is almost gone
        setTimeout(() => {
            window.openNav();
        }, 150); 
    } else {
        // If no modal is open, just open the sidebar normally
        window.openNav();
    }
};




document.addEventListener('DOMContentLoaded', function() {
    const chartCards = [
        { id: 'tempChart', title: 'Temperature Trend' },
        { id: 'hrChart', title: 'Heart Rate Trend' },
        { id: 'rrChart', title: 'Respiratory Rate Trend' },
        { id: 'bpChart', title: 'Blood Pressure Trend' },
        { id: 'spo2Chart', title: 'SpO2 Trend' }
    ];

    // Optimized click handler using Event Delegation
document.addEventListener('click', function(e) {
    // Check if the clicked element (or its parent) is a chart canvas
    const canvas = e.target.closest('canvas');
    if (canvas && (canvas.id.endsWith('Chart') && canvas.id !== 'modalChartCanvas')) {
        const chartId = canvas.id;
        
        // Map IDs to Titles
        const titles = {
            'tempChart': 'Temperature Trend',
            'hrChart': 'Heart Rate Trend',
            'rrChart': 'Respiratory Rate Trend',
            'bpChart': 'Blood Pressure Trend',
            'spo2Chart': 'SpO2 Trend'
        };

        openChartModal(chartId, titles[chartId] || 'Vital Trend');
    }
});
    
    // Close modal on background click
    document.getElementById('chart-modal').addEventListener('click', function(e) {
        if (e.target === this) closeChartModal();
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // 1. Target the (X) Close Button
    // Ensure your HTML has <button id="closeModalBtn">...</button> or change the ID here
    const closeBtn = document.querySelector('.close-modal-btn') || document.getElementById('close-chart-modal');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closeChartModal();
        });
    }

    // 2. Close on Background Click
    const modalWrapper = document.getElementById('chart-modal');
    if (modalWrapper) {
        modalWrapper.addEventListener('click', function(e) {
            // This checks if you clicked the dark area, NOT the white box
            if (e.target === modalWrapper) {
                closeChartModal();
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // 1. Detect when ANY modal starts to open
    $('.modal').on('show.bs.modal', function () {
        closeNav(); // This calls your existing sidebar close function
    });

    // 2. Ensure your closeNav function is defined like this:
    window.closeNav = function() {
        const sidebar = document.getElementById("mySidenav");
        if (sidebar) {
            // Add the class that slides it left (off-screen)
            sidebar.classList.add("-translate-x-full");
            // Remove the class that keeps it on-screen
            sidebar.classList.remove("translate-x-0");
        }
    }
});



document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('mySidenav');
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const isSidebarOpen = !sidebar.classList.contains('-translate-x-full');
                
                // If the sidebar is opening...
                if (isSidebarOpen) {
                    const modal = document.getElementById('chart-modal');
                    const modalContent = modal ? modal.querySelector('div') : null;

                    if (modal && modal.style.display !== 'none') {
                        // 1. Instantly hide the modal to prevent the "error" look
                        modal.style.opacity = '0'; 
                        
                        // 2. Cleanup the data/chart
                        closeChartModal();
                        
                        // 3. Reset opacity for the next time it's used
                        setTimeout(() => { modal.style.opacity = '1'; }, 300);
                    }
                }
            }
        });
    });

    if (sidebar) {
        observer.observe(sidebar, { attributes: true });
    }
});

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
