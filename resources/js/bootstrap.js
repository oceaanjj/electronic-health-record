import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Import the Bootstrap JavaScript bundle
import * as bootstrap from "bootstrap";

// Attach Bootstrap to the window object for global access
window.bootstrap = bootstrap;

// FOR ALERTS!
document.addEventListener("DOMContentLoaded", function () {
    const successAlert = document.getElementById("success-alert");
    const errorAlert = document.getElementById("error-alert");

    if (successAlert) {
        setTimeout(() => {
            new bootstrap.Alert(successAlert).close();
        }, 3000); // 3 seconds
    }

    if (errorAlert) {
        setTimeout(() => {
            new bootstrap.Alert(errorAlert).close();
        }, 3000);
    }
});
