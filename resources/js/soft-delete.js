document.addEventListener('DOMContentLoaded', function () {
    // Using event delegation on the document to ensure buttons are captured even if the DOM changes.
    document.addEventListener('click', function (e) {
        // Use closest() to find the button, handles clicks on the button or its children (like the <span>)
        const button = e.target.closest('.js-toggle-patient-status');

        // If a button with the correct class was not clicked, do nothing.
        if (!button) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        const patientId = button.dataset.patientId;
        const action = button.dataset.action;
        const isActivating = action === 'activate';
        
        // Match the routes in web.php
        const url = isActivating ? `/patients/${patientId}/activate` : `/patients/${patientId}/deactivate`;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        console.log(`Button clicked! Action: ${action}, Patient ID: ${patientId}`);

        fetch(url, {
            method: isActivating ? 'POST' : 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                Accept: 'application/json',
            }
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then((data) => {
                console.log('Response from server:', data);
                if (data.success) {
                    console.log('Action successful, updating UI.');
                    
                    // Update UI without reload
                    const row = document.querySelector(`tr[data-id="${patientId}"]`);
                    if (row) {
                        const isActive = isActivating;
                        const actionsCell = row.querySelector('td:last-child');

                        // 1. Update Row Styling
                        if (!isActive) {
                            row.classList.remove('bg-beige');
                            row.classList.add('bg-red-100', 'text-red-700');
                        } else {
                            row.classList.remove('bg-red-100', 'text-red-700');
                            row.classList.add('bg-beige');
                        }

                        // 2. Update Action Buttons
                        if (!isActive) {
                            actionsCell.innerHTML = `
                                <button type="button"
                                    class="js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-red-50 border border-red-600 px-3 py-1 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-100 cursor-pointer"
                                    data-patient-id="${patientId}" data-action="activate">
                                    RESTORE
                                </button>
                            `;
                        } else {
                            actionsCell.innerHTML = `
                                <div class="flex justify-center gap-2">
                                    <a href="/patients/${patientId}/edit"
                                        class="inline-flex items-center justify-center rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition hover:bg-green-600 cursor-pointer">
                                        EDIT
                                    </a>
                                    <button type="button"
                                        class="js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition hover:bg-dark-red cursor-pointer"
                                        data-patient-id="${patientId}" data-action="deactivate">
                                        SET INACTIVE
                                    </button>
                                </div>
                            `;
                        }
                    }

                    // Removed the dispatchEvent('input') to avoid full table re-render/animation
                    
                } else {
                    console.error('Action failed:', data.message || 'No error message provided.');
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
            });
    });
});
