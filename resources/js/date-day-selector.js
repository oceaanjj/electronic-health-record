/**
 * Reusable Date and Day Selector Script
 *
 * How it works:
 * 1. It finds elements with the IDs 'date_selector' and 'day_no' when the page loads.
 * 2. It attaches a 'change' event listener to both.
 * 3. When the user changes the value of either the date or day, the script finds
 * the parent <form> element.
 * 4. It then automatically submits that form. This is used to reload the page
 * with the data corresponding to the newly selected date/day.
 *
 * How to use:
 * - Ensure your date input has id="date_selector".
 * - Ensure your day number select has id="day_no".
 * - Place both elements inside the form you want to be submitted on change.
 * - Include this script file in your Vite build for the page.
 */
function initializeDateDaySelector() {
    const dateSelector = document.getElementById("date_selector");
    const daySelector = document.getElementById("day_no");

    const handleChange = (event) => {
        // Find the closest form ancestor of the element that changed
        const form = event.target.closest("form");
        if (form) {
            form.submit();
        } else {
            console.error(
                "Date/Day selector could not find a parent form to submit."
            );
        }
    };

    if (dateSelector) {
        dateSelector.addEventListener("change", handleChange);
    }

    if (daySelector) {
        daySelector.addEventListener("change", handleChange);
    }
}

// Run on initial page load
document.addEventListener("DOMContentLoaded", () => {
    initializeDateDaySelector();
});

// Also make it globally available so patient-loader.js can re-run it
// after replacing the form content.
window.initializeDateDaySelector = initializeDateDaySelector;
