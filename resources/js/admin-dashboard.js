document.addEventListener('DOMContentLoaded', () => {
    console.log('Admin Dashboard JS loaded and running...');
    
    const logContainer = document.getElementById('admin-audit-logs');
    if (!logContainer) {
        console.warn('Admin audit logs container not found.');
        return;
    }

    const pollUrl = logContainer.getAttribute('data-url');
    if (!pollUrl) {
        console.warn('Polling URL (data-url) not found on audit logs container.');
        return;
    }

    let lastDataHash = null;

    // Success message auto-hide
    const msg = document.getElementById('success-message');
    if (msg) {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.6s ease';
            msg.style.opacity = 0;
            setTimeout(() => msg.remove(), 600);
        }, 4000);
    }

    async function refreshDashboard() {
        try {
            const timestamp = new Date().getTime();
            console.log(`Fetching dashboard data... (${timestamp})`);
            
            const response = await fetch(`${pollUrl}${pollUrl.includes('?') ? '&' : '?'}t=${timestamp}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                console.error('Failed to fetch dashboard data:', response.statusText);
                return;
            }
            
            const data = await response.json();
            console.log('Dashboard data received:', data);
            
            // Check if data has actually changed
            const currentDataHash = JSON.stringify(data);
            if (lastDataHash === currentDataHash) {
                console.log('No data changes detected.');
                return;
            }
            lastDataHash = currentDataHash;
            console.log('Updating dashboard UI...');

            // Update Stats
            const statsMap = {
                'stat-total-users': data.stats.total_users,
                'stat-total-doctors': data.stats.total_doctors,
                'stat-total-nurses': data.stats.total_nurses
            };

            for (const [id, value] of Object.entries(statsMap)) {
                const el = document.getElementById(id);
                if (el) {
                    el.innerText = value;
                } else {
                    console.warn(`Stat element #${id} not found.`);
                }
            }
            
            // Update Audit Logs Table
            let html = '';
            if (data.logs && data.logs.length > 0) {
                data.logs.forEach(log => {
                    const action = (log.action || '').toLowerCase();
                    let actionClass = 'bg-green-100 text-green-800';
                    
                    if (action.includes('delete')) {
                        actionClass = 'bg-red-100 text-red-800';
                    } else if (action.includes('update') || action.includes('edit') || action.includes('change') || action.includes('modified')) {
                        actionClass = 'bg-yellow-100 text-yellow-800';
                    }
                    
                    html += `
                        <tr class="transition-all duration-500 hover:bg-gray-50 border-b border-gray-100">
                            <td class="px-4 py-3 font-medium text-gray-700">${log.username}</td>
                            <td class="px-4 py-3">
                                <span class="${actionClass} inline-block rounded-full px-3 py-1 text-xs font-semibold">
                                    ${log.action}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle text-gray-700">
                                ${log.sentence}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                ${log.created_at}
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500 italic">No recent logs found.</td></tr>';
            }
            
            logContainer.innerHTML = html;
            console.log('Dashboard UI updated successfully.');
            
        } catch (error) {
            console.error('Dashboard Polling Error:', error);
        }
    }

    // Initial load
    refreshDashboard();

    // Poll every 5 seconds
    setInterval(refreshDashboard, 5000);
});
