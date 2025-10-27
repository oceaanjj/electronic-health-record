/**
 * How it works:
 * ... (same as before) ...
 * * MODIFICATION: The initializeCdssForForm function is attached to the 'window'
 * object so that other scripts (like patient-loader.js) can call it
 * after replacing page content.
 */

// Make this function globally accessible
window.initializeCdssForForm = function (form) {
    const analyzeUrl = form.dataset.analyzeUrl;
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    let debounceTimer;

    if (!analyzeUrl || !csrfToken) {
        console.error(
            'CDSS form is missing "data-analyze-url" or CSRF token is not found.',
            form
        );
        return;
    }

    const inputs = form.querySelectorAll(".cdss-input");

    // --- Analyze Pre-filled Data on Load ---
    inputs.forEach((input) => {
        if (input.value.trim() !== "") {
            const fieldName = input.dataset.fieldName;
            const finding = input.value;
            analyzeField(fieldName, finding, analyzeUrl, csrfToken);
        }
    });

    // --- Analyze on Type ---
    inputs.forEach((input) => {
        input.addEventListener("input", (e) => {
            clearTimeout(debounceTimer);
            const fieldName = e.target.dataset.fieldName;
            const finding = e.target.value;

            debounceTimer = setTimeout(() => {
                if (fieldName && finding !== null) {
                    analyzeField(fieldName, finding, analyzeUrl, csrfToken);
                }
            }, 500);
        });
    });
};

// This runs on the initial page load
document.addEventListener("DOMContentLoaded", () => {
    const cdssForms = document.querySelectorAll(".cdss-form");
    cdssForms.forEach((form) => {
        // Call the global function
        window.initializeCdssForForm(form);
    });
});

async function analyzeField(fieldName, finding, url, token) {
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);
    if (!alertCell) return;

    if (finding.trim() === "") {
        alertCell.innerHTML = "";
        return;
    }

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify({
                fieldName: fieldName,
                finding: finding,
            }),
        });

        if (!response.ok) {
            throw new Error(`Server responded with status: ${response.status}`);
        }

        const alertData = await response.json();
        displayAlert(alertCell, alertData);
    } catch (error) {
        console.error("CDSS analysis failed:", error);
        alertCell.innerHTML = `<div class="alert-box alert-red"><span class="alert-message">Error analyzing...</span></div>`;
    }
}

function displayAlert(alertCell, alertData) {
    alertCell.innerHTML = ""; // Clear previous alert

    let colorClass = "alert-green"; // Default for NONE
    if (alertData.severity === "CRITICAL") {
        colorClass = "alert-red";
    } else if (alertData.severity === "WARNING") {
        colorClass = "alert-orange";
    } else if (alertData.severity === "INFO") {
        colorClass = "alert-green";
    }

    //show alert
    const alertBox = document.createElement("div");
    alertBox.className = `alert-box ${colorClass}`;
    alertBox.innerHTML = `<span class="alert-message">${alertData.alert}</span>`;
    alertCell.appendChild(alertBox);
}
