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
