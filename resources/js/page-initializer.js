/**
 * This script handles the initialization and re-initialization of page-specific JavaScript functions
 * after the DOM is loaded and after dynamic content is injected by other scripts (e.g., patient-loader.js).
 *
 * It relies on a `window.pageInitializers` array being defined in a small inline script
 * on the specific Blade template. This array should contain references to the functions
 * that need to be run for that page.
 */

// Function to execute all registered initializer functions.
function runPageInitializers() {
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
}

// Main initialization on first page load.
document.addEventListener('DOMContentLoaded', function() {
    runPageInitializers();

    // Set up a MutationObserver to re-initialize scripts whenever the main content container is updated.
    const container = document.getElementById('form-content-container');
    if (container) {
        const observer = new MutationObserver(function(mutations) {
            // We only need to know that a change happened, not what the change was.
            for (let mutation of mutations) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    runPageInitializers();
                    // Once we've run the initializers for a set of mutations, we can stop observing.
                    break;
                }
            }
        });

        // Configure and start the observer.
        observer.observe(container, { childList: true, subtree: true });
    }
});
