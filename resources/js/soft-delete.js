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

        const patientId = button.dataset.patientId;
        const action = button.dataset.action;
        const isActivating = action === 'activate';
        const url = isActivating ? `/patients/${patientId}/recover` : `/patients/${patientId}`;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        console.log(`Button clicked! Action: ${action}, Patient ID: ${patientId}`);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                _method: isActivating ? 'POST' : 'DELETE',
            }),
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
                    console.log('Action successful, reloading page to reflect changes.');
                    // Reloading the page is the simplest way to ensure the UI is in a consistent state.
                    window.location.reload();
                } else {
                    console.error('Action failed:', data.message || 'No error message provided.');
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
            });
    });
});
