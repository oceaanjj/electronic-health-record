const initSearchableDropdown = () => {
    const dropdownContainer = document.querySelector(".searchable-dropdown");

    // If the dropdown doesn't exist or is already initialized, do nothing.
    if (!dropdownContainer || dropdownContainer.dataset.initialized) {
        return;
    }
    dropdownContainer.dataset.initialized = "true";

    const searchInput = document.getElementById("patient_search_input");
    const hiddenInput = document.getElementById("patient_id_hidden");
    const optionsContainer = document.getElementById("patient_options_container");

    if (!searchInput || !optionsContainer) return;

    const options = optionsContainer.querySelectorAll(".option");
    const selectUrl = dropdownContainer.dataset.selectUrl;

    let currentFocus = -1;
    optionsContainer.style.display = "none";

    const removeActive = () => {
        const currentOptions = optionsContainer.querySelectorAll(".option");
        currentOptions.forEach((option) => {
            option.classList.remove("active");
        });
    };

    const addActive = (n) => {
        removeActive();
        const visibleOptions = Array.from(
            optionsContainer.querySelectorAll(".option")
        ).filter((opt) => opt.style.display !== "none");

        if (visibleOptions.length === 0) return;

        if (n >= visibleOptions.length) n = 0;
        if (n < 0) n = visibleOptions.length - 1;
        currentFocus = n;

        visibleOptions[currentFocus].classList.add("active");
        visibleOptions[currentFocus].scrollIntoView({
            block: "nearest",
            behavior: "smooth",
        });
    };

    const filterAndShowOptions = () => {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;
        const currentOptions = optionsContainer.querySelectorAll(".option");

        currentOptions.forEach((option) => {
            const text = (option.textContent || option.innerText).toLowerCase();
            const shouldShow = text.includes(filter);
            option.style.display = shouldShow ? "block" : "none";
            if (shouldShow) {
                visibleCount++;
            }
        });

        currentFocus = -1;
        removeActive();

        if (visibleCount > 0) {
            optionsContainer.style.display = "block";
        } else {
            optionsContainer.style.display = "none";
        }
    };

    searchInput.addEventListener("focus", filterAndShowOptions);

    searchInput.addEventListener("keyup", (event) => {
        if (
            event.key !== "ArrowUp" &&
            event.key !== "ArrowDown" &&
            event.key !== "Enter"
        ) {
            filterAndShowOptions();
        }
    });

    searchInput.addEventListener("input", () => {
        if (searchInput.value === "") {
            if (hiddenInput) {
                hiddenInput.value = "";
            }
            disableForm(true);
            clearFormInputs();
        }
    });

    const selectOption = (option) => {
        if (!option) return;
        const patientId = option.getAttribute("data-value");
        const patientName = (option.textContent || option.innerText).trim();

        searchInput.value = patientName;
        if (hiddenInput) {
            hiddenInput.value = patientId;
        }
        optionsContainer.style.display = "none";

        currentFocus = -1;
        removeActive();

        disableForm(false); // Enable form when a patient is selected

        const event = new CustomEvent("patient:selected", {
            bubbles: true,
            detail: {
                patientId: patientId,
                selectUrl: selectUrl,
            },
        });
        document.dispatchEvent(event);
    };

    searchInput.addEventListener("keydown", (event) => {
        const visibleOptions = Array.from(
            optionsContainer.querySelectorAll(".option")
        ).filter((opt) => opt.style.display !== "none");

        if (event.key === "ArrowDown" || event.key === "ArrowUp") {
            event.preventDefault();
            const direction = event.key === "ArrowDown" ? 1 : -1;
            addActive(currentFocus + direction);
        } else if (event.key === "Enter") {
            event.preventDefault();
            const activeOption = optionsContainer.querySelector(".option.active");
            if (activeOption) {
                selectOption(activeOption);
            } else if (visibleOptions.length > 0) {
                selectOption(visibleOptions[0]);
            }
        }
    });

    optionsContainer.addEventListener("click", (event) => {
        const option = event.target.closest(".option");
        if (option) {
            selectOption(option);
        }
    });

    const disableForm = (isDisabled) => {
        const formFieldset = document.querySelector("#form-content-container fieldset");
        const dateSelector = document.getElementById("date_selector");
        const dayNoSelector = document.getElementById("day_no_selector");
        const vitalInputs = document.querySelectorAll(".vital-input");
        const cdssButton = document.querySelector(".cdss-form button[type='button']");
        const submitButton = document.querySelector(".cdss-form button[type='submit']");
        const insertButtons = document.querySelectorAll(".insert-btn"); // Select all insert buttons
        const clearButtons = document.querySelectorAll(".clear-btn"); // Select all clear buttons

        if (formFieldset) {
            formFieldset.disabled = isDisabled;
        }
        if (dateSelector) {
            dateSelector.disabled = isDisabled;
        }
        if (dayNoSelector) {
            dayNoSelector.disabled = isDisabled;
        }
        vitalInputs.forEach(input => {
            input.disabled = isDisabled;
        });
        if (cdssButton) {
            cdssButton.disabled = isDisabled;
        }
        if (submitButton) {
            submitButton.disabled = isDisabled;
        }

        // Explicitly disable/enable insert and clear buttons
        insertButtons.forEach(button => {
            if (isDisabled) {
                button.classList.add('disabled');
                button.setAttribute('disabled', 'true');
            } else {
                button.classList.remove('disabled');
                button.removeAttribute('disabled');
            }
        });

        clearButtons.forEach(button => {
            button.disabled = isDisabled;
        });
    };

    const clearFormInputs = () => {
        const formFieldset = document.querySelector("#form-content-container fieldset");
        if (formFieldset) {
            const inputs = formFieldset.querySelectorAll("input, textarea, textarea.notepad-lines");
            inputs.forEach(input => {
                if (input.type !== 'hidden') { // Do not clear hidden inputs
                    input.value = "";
                }
                input.style.backgroundColor = ""; // Clear background color
                input.style.color = ""; // Clear text color
            });

            // Clear image previews and file inputs
            const diagnosticPanels = formFieldset.querySelectorAll(".diagnostic-panel");
            diagnosticPanels.forEach(panel => {
                const type = panel.dataset.type;
                const previewContainer = document.getElementById('preview-' + type);
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                }
                const uploadedFilesContainer = document.getElementById('uploaded-files-' + type);
                if (uploadedFilesContainer) {
                    uploadedFilesContainer.innerHTML = '';
                }
                // Also clear the file input itself
                const fileInput = document.getElementById('file-input-' + type);
                if (fileInput) {
                    fileInput.value = '';
                }
            });
        }
        clearAlerts(); // Call clearAlerts when inputs are cleared
    };

    const clearAlerts = () => {
        const alertBoxes = document.querySelectorAll(".alert-box");
        alertBoxes.forEach(alertBox => {
            alertBox.innerHTML = '<span class="opacity-70 text-white font-semibold text-center">NO ALERTS</span>';
            alertBox.style.backgroundColor = ""; // Clear background color
            alertBox.onclick = null; // Ensure the alert box is not clickable
        });
    };

    // Initial state check
    if (hiddenInput && hiddenInput.value === "") {
        disableForm(true);
    } else {
        disableForm(false);
    }
};

// This listener handles clicks outside the dropdown to close it.
// It's self-contained and only added once.
if (!window.searchableDropdownDocumentListener) {
    document.addEventListener("click", (event) => {
        const dropdownContainer = document.querySelector(".searchable-dropdown");
        if (dropdownContainer && !dropdownContainer.contains(event.target)) {
            const optionsContainer = document.getElementById("patient_options_container");
            if (optionsContainer) {
                optionsContainer.style.display = "none";
            }
        }
    });
    window.searchableDropdownDocumentListener = true;
}

// Initialize on initial page load
document.addEventListener("DOMContentLoaded", () => {
    initSearchableDropdown();
});

// Expose the function to be called from other scripts
window.initSearchableDropdown = initSearchableDropdown;

