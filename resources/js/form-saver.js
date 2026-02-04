/**
 * Handles enabling/disabling of the Submit button based on form state.
 * - Disables Submit if no inputs are filled (empty form).
 * - Disables Submit if values haven't changed from their initial state (clean form).
 */

function initializeFormSaver(form) {
    // Find the submit button(s). Exclude CDSS buttons.
    const submitBtns = Array.from(form.querySelectorAll('button[type="submit"]')).filter(
        (btn) => !btn.classList.contains('cdss-btn'),
    );

    if (submitBtns.length === 0) return;

    // Capture initial state of visible inputs
    const inputs = Array.from(form.querySelectorAll('input, textarea, select')).filter((input) => {
        return input.type !== 'hidden' && input.type !== 'submit' && input.type !== 'button' && input.name !== '_token';
    });

    const initialState = new Map();
    inputs.forEach((input) => {
        initialState.set(input, input.value);
    });

    function checkState() {
        let hasInput = false;
        let isDirty = false;

        inputs.forEach((input) => {
            const val = input.value.trim();
            const initialVal = initialState.get(input) || '';

            if (val !== '') {
                hasInput = true;
            }

            if (val !== initialVal.trim()) {
                isDirty = true;
            }
        });

        // Enable only if there's at least one input AND the form is dirty
        const shouldEnable = hasInput && isDirty;

        submitBtns.forEach((btn) => {
            if (shouldEnable) {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }

    // Run check immediately
    checkState();

    // Add listeners
    inputs.forEach((input) => {
        input.addEventListener('input', checkState);
        input.addEventListener('change', checkState);
    });
}

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    // Target forms specifically within the content container OR forms with 'cdss-form' class
    const forms = document.querySelectorAll('#form-content-container form, form.cdss-form');
    forms.forEach((form) => initializeFormSaver(form));
});

// Initialize on form reload (via patient-loader.js)
document.addEventListener('cdss:form-reloaded', (event) => {
    const formContainer = event.detail.formContainer;
    if (formContainer) {
        const forms = formContainer.querySelectorAll('form');
        forms.forEach((form) => initializeFormSaver(form));
    }
});
