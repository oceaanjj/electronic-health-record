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
window.initializeCdssForForm = function(formElement) {
    console.log("initializeCdssForForm called for:", formElement);
    // TODO: Add actual CDSS initialization logic here
};

// Placeholder for Date/Day loader initialization
window.initializeDateDayLoader = function(selectUrl) {
    console.log("initializeDateDayLoader called with selectUrl:", selectUrl);
    // TODO: Add actual Date/Day loader initialization logic here
};
