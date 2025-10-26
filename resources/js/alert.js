/**
 * How it works:
 * 1. It finds any <form> on the page with the class "cdss-form".
 * 2. It reads the analysis URL from a 'data-analyze-url' attribute.
 * 3. It finds all <textarea> or <input> with class "cdss-input".
 * 4. ON PAGE LOAD, it checks all inputs and runs analysis on any that already have data.
 * 5. WHEN TYPING, it sends the data to the URL to get a real-time alert.
 * 6. It displays the returned alert in the corresponding element.
 */
document.addEventListener("DOMContentLoaded", () => {
    const cdssForms = document.querySelectorAll(".cdss-form");
    cdssForms.forEach((form) => {
        initializeCdssForForm(form);
    });
});

function initializeCdssForForm(form) {
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
        // Check if the input has a value when the page loads
        if (input.value.trim() !== "") {
            const fieldName = input.dataset.fieldName;
            const finding = input.value;
            // Run analysis immediately for this pre-filled field
            analyzeField(fieldName, finding, analyzeUrl, csrfToken);
        }
    });
    // ---

    // This part handles the real-time analysis while typing (remains the same)
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
}

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
