document.addEventListener('DOMContentLoaded', () => {
    const logContainer = document.getElementById('admin-audit-logs');
    if (!logContainer) return;

    // Get the polling URL from a data attribute on the container
    const pollUrl = logContainer.getAttribute('data-url');
    if (!pollUrl) return;

    // Success message auto-hide logic
    const msg = document.getElementById('success-message');
    if (msg) {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.6s ease';
            msg.style.opacity = 0;
            setTimeout(() => msg.remove(), 600);
        }, 4000);
    }

    // Polling logic for Admin Dashboard
    setInterval(async () => {
        try {
            const response = await fetch(pollUrl);
            if (!response.ok) return;
            
            const data = await response.json();
            
            // Update Stats
            const totalUsers = document.getElementById('stat-total-users');
            const totalDoctors = document.getElementById('stat-total-doctors');
            const totalNurses = document.getElementById('stat-total-nurses');

            if (totalUsers) totalUsers.innerText = data.stats.total_users;
            if (totalDoctors) totalDoctors.innerText = data.stats.total_doctors;
            if (totalNurses) totalNurses.innerText = data.stats.total_nurses;
            
            // Update Audit Logs Table
            let html = '';
            data.logs.forEach(log => {
                const actionClass = log.action === 'Deleted' 
                    ? 'bg-red-100 text-red-800' 
                    : (log.action === 'Updated' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                
                html += `
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">${log.username}</td>
                        <td class="px-4 py-3">
                            <span class="${actionClass} inline-block rounded-full px-3 py-1 text-xs font-semibold">
                                ${log.action}
                            </span>
                        </td>
                        <td class="px-4 py-3 align-middle text-gray-800">
                            ${log.sentence}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                            ${log.created_at}
                        </td>
                    </tr>
                `;
            });
            
            logContainer.innerHTML = html;
        } catch (error) {
            console.error('Error polling admin data:', error);
        }
    }, 5000); // 5 seconds
});
