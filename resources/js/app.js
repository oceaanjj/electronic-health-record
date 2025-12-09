// Imports
import {
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showConfirm,
    showDeleteConfirm,
    showLoginRequired,
    showLoading,
    closeAlert,
} from "./sweetalert.js";

// Import commonly used scripts globally
import "./soft-delete.js";
import "./patient-search.js";
import "./patient-loader.js";
import "./searchable-dropdown.js";
import "./date-day-loader.js";
import "./compute-age.js";
import "./init-searchable-dropdown.js";
import "./page-initializer.js";
import "./intake-output-patient-loader.js";
import "./form-disable-alert.js";

// Import CSS
import "../css/app.css";

//  SweetAlert global
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
window.showConfirm = showConfirm;
window.showDeleteConfirm = showDeleteConfirm;
window.showLoginRequired = showLoginRequired;
window.showLoading = showLoading;
window.closeAlert = closeAlert;
// Legacy function names for backward compatibility
window.showSuccessAlert = showSuccess;
window.showErrorAlert = showError;
window.showWarningAlert = showWarning;
window.showInfoAlert = showInfo;

//ALERTS Fade out
document.addEventListener("DOMContentLoaded", function () {
    const alerts = document.querySelectorAll(
        ".alert-success, .alert-danger, .alert-danger"
    );

    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = "0";
            alert.style.pointerEvents = "none";
            alert.addEventListener("transitionend", function handler() {
                alert.remove();
                alert.removeEventListener("transitionend", handler);
            });
        }, 3000);
    });
});

// Placeholder for CDSS form initialization
window.initializeCdssForForm = function (formElement) {
    console.log("initializeCdssForForm called for:", formElement);
    // TODO: Add actual CDSS initialization logic here
};

// Placeholder for Date/Day loader initialization
window.initializeDateDayLoader = function (selectUrl) {
    console.log("initializeDateDayLoader called with selectUrl:", selectUrl);
    // TODO: Add actual Date/Day loader initialization logic here
};
