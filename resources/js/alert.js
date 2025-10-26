/**
 *
 * How it works:
 * 1. It finds any <form> on the page with the class "cdss-form".
 * 2. For each form, it reads the analysis URL from a 'data-analyze-url' attribute.
 * 3. It finds all <textarea> or <input> elements with the class "cdss-input" inside that form.
 * 4. When a user types into one of these inputs, it sends the data to the specified URL.
 * 5. It displays the returned alert in the corresponding element that has a 'data-alert-for' attribute.
 */
document.addEventListener("DOMContentLoaded", () => {
    // Find all forms that require real-time CDSS functionality
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

    // A debounce timer to avoid sending requests on every single keystroke
    let debounceTimer;

    if (!analyzeUrl || !csrfToken) {
        console.error(
            'CDSS form is missing "data-analyze-url" or CSRF token is not found.',
            form
        );
        return;
    }

    const inputs = form.querySelectorAll(".cdss-input");

    inputs.forEach((input) => {
        input.addEventListener("input", (e) => {
            clearTimeout(debounceTimer);
            const fieldName = e.target.dataset.fieldName;
            const finding = e.target.value;

            debounceTimer = setTimeout(() => {
                if (fieldName && finding !== null) {
                    analyzeField(fieldName, finding, analyzeUrl, csrfToken);
                }
            }, 500); // Wait for 500ms of inactivity before sending request
        });
    });
}

async function analyzeField(fieldName, finding, url, token) {
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);
    if (!alertCell) return;

    // If the input is empty, clear the alert and stop.
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

    if (alertData && alertData.alert) {
        let colorClass = "alert-green"; // Default for NONE
        if (alertData.severity === "CRITICAL") {
            colorClass = "alert-red";
        } else if (alertData.severity === "WARNING") {
            colorClass = "alert-orange";
        } else if (alertData.severity === "INFO") {
            colorClass = "alert-green";
        }

        // show alert
        const alertBox = document.createElement("div");
        alertBox.className = `alert-box ${colorClass}`;
        alertBox.innerHTML = `<span class="alert-message">${alertData.alert}</span>`;
        alertCell.appendChild(alertBox);
    }
}
