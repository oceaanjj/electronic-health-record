/**
 * How it works:
 * ... (same as before) ...
 * * MODIFICATION: The initializeCdssForForm function is attached to the 'window'
 * object so that other scripts (like patient-loader.js) can call it
 * after replacing page content.
 */

let debounceTimer;

window.initializeCdssForForm = function (form) {
  const analyzeUrl = form.dataset.analyzeUrl;
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

  if (!analyzeUrl || !csrfToken) {
    console.error(
      'CDSS form missing "data-analyze-url" or CSRF token not found.',
      form
    );
    return;
  }

  const inputs = form.querySelectorAll(".cdss-input");

  // --- Initialize fields on load (removed to prevent redundant analysis on re-initialization) ---
  // inputs.forEach((input) => {
  //   const fieldName = input.dataset.fieldName;
  //   const finding = input.value.trim();
  //   const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);

  //   if (alertCell) {
  //     if (finding === "") showDefaultNoAlerts(alertCell);
  //     else analyzeField(fieldName, finding, analyzeUrl, csrfToken);
  //   }
  // });

  // --- Analyze while typing ---
  inputs.forEach((input) => {
    input.addEventListener("input", (e) => {
      clearTimeout(debounceTimer);

      const fieldName = e.target.dataset.fieldName;
      const finding = e.target.value.trim();
      const alertCell = document.querySelector(
        `[data-alert-for="${fieldName}"]`
      );

      if (alertCell) {
        if (finding === "") {
          showDefaultNoAlerts(alertCell);
        } else {
          if (!alertCell.classList.contains("alert-loading")) {
            showAlertLoading(alertCell);
          }
        }
      }

      debounceTimer = setTimeout(() => {
        if (fieldName && finding !== "") {
          analyzeField(fieldName, finding, analyzeUrl, csrfToken);
        }
      }, 300);
    });
  });
};

// --- Listen for form reload event to trigger initial analysis for pre-filled fields ---
document.addEventListener("cdss:form-reloaded", (event) => {
  const formContainer = event.detail.formContainer;
  const cdssForm = formContainer.querySelector(".cdss-form");

  if (cdssForm) {
    const analyzeUrl = cdssForm.dataset.analyzeUrl;
    const csrfToken = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");

    if (!analyzeUrl || !csrfToken) {
      console.error(
        'CDSS form reloaded: Missing "data-analyze-url" or CSRF token.',
        cdssForm
      );
      return;
    }

    const inputs = cdssForm.querySelectorAll(".cdss-input");
    inputs.forEach((input) => {
      const fieldName = input.dataset.fieldName;
      const finding = input.value.trim();
      const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);

      if (alertCell) {
        if (finding === "") {
          showDefaultNoAlerts(alertCell);
        } else {
          analyzeField(fieldName, finding, analyzeUrl, csrfToken);
        }
      }
    });
  }
});

// --- Initialize CDSS forms on page load ---
document.addEventListener("DOMContentLoaded", () => {
  const cdssForms = document.querySelectorAll(".cdss-form");
  cdssForms.forEach((form) => window.initializeCdssForForm(form));
});

// --- Function: Analyze input with backend ---
async function analyzeField(fieldName, finding, url, token) {
  const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);
  if (!alertCell) return;

  showAlertLoading(alertCell);

  try {
    const response = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": token,
      },
      body: JSON.stringify({ fieldName, finding }),
    });

    if (!response.ok) throw new Error(`Server error: ${response.status}`);

    const alertData = await response.json();

    setTimeout(() => {
      displayAlert(alertCell, alertData);
    }, 150);
  } catch (error) {
    console.error("CDSS analysis failed:", error);
    alertCell.innerHTML = `
      <div class="alert-box alert-red fade-in" style="height:90px;margin:2px;">
        <span class="alert-message">Error analyzing...</span>
      </div>
    `;
  }
}

// --- Display alert content ---
function displayAlert(alertCell, alertData) {
  alertCell.innerHTML = "";

  const alertBox = document.createElement("div");
  alertBox.className = "alert-box fade-in";
  alertBox.style.height = "90px";
  alertBox.style.margin = "2px";

  // Set color by severity
  let colorClass = "alert-green";
  if (alertData.severity === "CRITICAL") colorClass = "alert-red";
  else if (alertData.severity === "WARNING") colorClass = "alert-orange";
  else if (alertData.severity === "INFO") colorClass = "alert-green";

  alertBox.classList.add(colorClass);

  const alertMessage = document.createElement("div");
  alertMessage.className = "alert-message";
  alertMessage.style.padding = "8px"; // ✅ add padding instead of fade/gradient

  // --- Handle NO FINDINGS ---
  if (alertData.alert?.toLowerCase().includes("no findings")) {
    alertBox.classList.add("has-no-alert");
    alertMessage.innerHTML = `
      <span class="text-white text-center uppercase font-semibold opacity-80">
        NO FINDINGS
      </span>
    `;
  } else {
    alertMessage.innerHTML = `<span>${alertData.alert}</span>`;
  }

  alertBox.appendChild(alertMessage);
  alertCell.appendChild(alertBox);

  // --- Simple scroll behavior without gradient ---
  alertMessage.addEventListener("scroll", () => {
    // No visual fade — clean scroll only
  });

  if (!alertData.alert?.toLowerCase().includes("no findings")) {
    alertBox.addEventListener("click", () => openAlertModal(alertData));
  }
}

// --- Function: Trigger initial CDSS analysis for pre-filled fields ---
window.triggerInitialCdssAnalysis = function (form) {
  const analyzeUrl = form.dataset.analyzeUrl;
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

  if (!analyzeUrl || !csrfToken) {
    console.error(
      'Initial CDSS analysis missing "data-analyze-url" or CSRF token.',
      form
    );
    return;
  }

  const inputs = form.querySelectorAll(".cdss-input");
  inputs.forEach((input) => {
    const fieldName = input.dataset.fieldName;
    const finding = input.value.trim();
    const alertCell = document.querySelector(`[data-alert-for="${fieldName}"]`);

    if (alertCell && finding !== "") {
      analyzeField(fieldName, finding, analyzeUrl, csrfToken);
    } else if (alertCell && finding === "") {
      showDefaultNoAlerts(alertCell);
    }
  });
};

// --- Default NO ALERTS state ---
function showDefaultNoAlerts(alertCell) {
  alertCell.className = "alert-box has-no-alert alert-green fade-in";
  alertCell.style.height = "90px";
  alertCell.style.margin = "2.8px";
  alertCell.innerHTML = `
    <span class="alert-message opacity-80 text-white text-center font-semibold uppercase">
      NO ALERTS
    </span>
  `;
  alertCell.onclick = null;
}

// --- Loading spinner (continuous) ---
function showAlertLoading(alertCell) {
  alertCell.className = "alert-box alert-green alert-loading fade-in";
  alertCell.style.height = "90px";
  alertCell.style.margin = "2px";
  alertCell.innerHTML = `
    <div class="alert-loading">
      <div class="loading-spinner"></div>
      <span>Analyzing...</span>
    </div>
  `;
  alertCell.onclick = null;
}

// --- Modal popup for details ---
function openAlertModal(alertData) {
  const overlay = document.createElement("div");
  overlay.className = "alert-modal-overlay";

  const modal = document.createElement("div");
  modal.className = "alert-modal fade-in";
  modal.innerHTML = `
    <button class="close-btn">&times;</button>
    <h2>Alert Details</h2>
    <p>${alertData.alert}</p>
  `;

  overlay.appendChild(modal);
  document.body.appendChild(overlay);

  const closeModal = () => overlay.remove();
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) closeModal();
  });
  modal.querySelector(".close-btn").addEventListener("click", closeModal);
}

// --- Fade-in animation ---
const style = document.createElement("style");
style.textContent = `
  .fade-in { animation: fadeIn 0.25s ease-in-out forwards; }
  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
  }
`;
document.head.appendChild(style);