/**
 * This script handles the initialization and re-initialization of page-specific JavaScript functions
 * after the DOM is loaded and after dynamic content is injected by other scripts (e.g., patient-loader.js).
 *
 * It relies on a `window.pageInitializers` array being defined in a small inline script
 * on the specific Blade template. This array should contain references to the functions
 * that need to be run for that page.
 */

let observer;
let debounceTimer;

// Function to pause the observer
function pauseObserver() {
    if (observer) {
        observer.disconnect();
        console.log('MutationObserver disconnected.');
    }
}

// Function to resume the observer
function resumeObserver() {
    const container = document.getElementById('form-content-container');
    if (observer && container) {
        observer.observe(container, { childList: true, subtree: true });
        console.log('MutationObserver reconnected.');
    }
}

// Function to execute all registered initializer functions.
function runPageInitializers() {
    pauseObserver(); // Pause observer before running initializers
    if (window.pageInitializers && Array.isArray(window.pageInitializers)) {
        window.pageInitializers.forEach(initializer => {
            // Check if the item in the array is a function before calling it.
            if (typeof initializer === 'function') {
                try {
                    initializer();
                } catch (error) {
                    console.error('Error running initializer:', initializer.name, error);
                }
            }
        });
    }
    resumeObserver(); // Resume observer after running initializers
}

// Main initialization on first page load.
document.addEventListener('DOMContentLoaded', function() {
    runPageInitializers();

    // Set up a MutationObserver to re-initialize scripts whenever the main content container is updated.
    const container = document.getElementById('form-content-container');
    if (container) {
        observer = new MutationObserver(function(mutations) {
            console.log('MutationObserver triggered. Mutations:', mutations);
            // Check if any significant changes occurred (e.g., child nodes added/removed)
            const hasSignificantChanges = mutations.some(mutation => mutation.type === 'childList' && mutation.addedNodes.length > 0);

            if (hasSignificantChanges) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    console.log('Running page initializers after debounce (triggered by observer).');
                    runPageInitializers();
                }, 50); // Debounce by 50ms
            }
        });

        // Configure and start the observer.
        observer.observe(container, { childList: true, subtree: true });
    }
});
