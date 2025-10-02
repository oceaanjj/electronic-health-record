//ALERTS Fade out
document.addEventListener("DOMContentLoaded", function () {
    const alerts = document.querySelectorAll(".alert-success, .alert-danger");

    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = "0";

            alert.addEventListener("transitionend", function handler() {
                alert.remove();
                alert.removeEventListener("transitionend", handler);
            });
        }, 3000);
    });
});
