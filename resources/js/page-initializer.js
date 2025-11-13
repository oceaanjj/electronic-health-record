/**
 * This script handles the initialization of page-specific JavaScript functions
 * when the DOM is first loaded.
 *
 * It relies on a `window.pageInitializers` array being defined in a small inline script
 * on the specific Blade template. This array should contain references to the functions
 * that need to be run for that page.
 *
 * NOTE: The MutationObserver has been removed. Re-initialization after
 * dynamic content injection is now handled manually by the script
 * that loads the content (e.g., patient-loader.js), which calls
 * specific init functions like 'window.initializeCdssForForm'.
 */

// Function to execute all registered initializer functions.
function runPageInitializers() {
    // pauseObserver(); // Does nothing, but afe to call
    if (window.pageInitializers && Array.isArray(window.pageInitializers)) {
        window.pageInitializers.forEach((initializer) => {
            // Check if the item in the array is a function before calling it.
            if (typeof initializer === "function") {
                try {
                    initializer();
                } catch (error) {
                    console.error(
                        "Error running initializer:",
                        initializer.name,
                        error
                    );
                }
            }
        });
    }
    // resumeObserver(); // Does nothing, but safe to call
}

// Main initialization on first page load.
document.addEventListener("DOMContentLoaded", function () {
    runPageInitializers();

    // The MutationObserver has been removed.
    // The 'patient-loader.js' script is responsible for re-initializing
    // scripts (like alert.js) after it loads new content.
});
