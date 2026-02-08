document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('medication-administration-form');
    if (!form) {
        return;
    }

    const submitButton = form.querySelector('button[type="submit"]');
    const inputs = form.querySelectorAll('.medication-input');

    const checkFormState = () => {
        if (!submitButton) return;

        let hasData = false;
        inputs.forEach((input) => {
            if (input.value.trim() !== '') {
                hasData = true;
            }
        });

        if (hasData) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'pointer-events-none');
        }
    };

    // Check state on initial load
    checkFormState();

    // Check state on any input event in the form
    form.addEventListener('input', checkFormState);

    // Re-initialize when the form content is reloaded (e.g., after patient selection)
    const observer = new MutationObserver((mutationsList, observer) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                // When the form is reloaded, re-run the logic
                const newForm = document.getElementById('medication-administration-form');
                if (newForm) {
                    const newSubmitButton = newForm.querySelector('button[type="submit"]');
                    const newInputs = newForm.querySelectorAll('.medication-input');

                    const newCheckFormState = () => {
                        if (!newSubmitButton) return;
                        let hasData = false;
                        newInputs.forEach((input) => {
                            if (input.value.trim() !== '') {
                                hasData = true;
                            }
                        });

                        if (hasData) {
                            newSubmitButton.disabled = false;
                            newSubmitButton.classList.remove('opacity-50', 'pointer-events-none');
                        } else {
                            newSubmitButton.disabled = true;
                            newSubmitButton.classList.add('opacity-50', 'pointer-events-none');
                        }
                    };

                    newCheckFormState();
                    newForm.addEventListener('input', newCheckFormState);
                }
            }
        }
    });

    const formContainer = document.getElementById('form-content-container');
    if (formContainer) {
        observer.observe(formContainer, { childList: true, subtree: true });
    }
});
