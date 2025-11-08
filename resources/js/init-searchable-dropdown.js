function initializeSearchableDropdown() {
    const dropdownContainer = document.querySelector(
        ".searchable-dropdown:not([data-initialized])"
    );
    if (!dropdownContainer) return;
    dropdownContainer.setAttribute("data-initialized", "true");

    const searchInput = document.getElementById("patient_search_input");
    const hiddenInput = document.getElementById("patient_id_hidden");
    const optionsContainer = document.getElementById(
        "patient_options_container"
    );
    if (!searchInput || !optionsContainer) return;

    const options = optionsContainer.querySelectorAll(".option");
    const selectUrl = dropdownContainer.dataset.selectUrl;

    let currentFocus = -1;
    optionsContainer.style.display = "none";

    const removeActive = () => {
        optionsContainer
            .querySelectorAll(".option")
            .forEach((opt) => opt.classList.remove("active"));
    };

    const addActive = (n) => {
        removeActive();
        const visibleOptions = Array.from(
            optionsContainer.querySelectorAll(".option")
        ).filter((opt) => opt.style.display !== "none");
        if (visibleOptions.length === 0) return;
        currentFocus = (n + visibleOptions.length) % visibleOptions.length;
        const focusedOption = visibleOptions[currentFocus];
        focusedOption.classList.add("active");
        focusedOption.scrollIntoView({ block: "nearest", behavior: "smooth" });
    };

    const filterAndShowOptions = () => {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;
        options.forEach((option) => {
            const text = (option.textContent || option.innerText).toLowerCase();
            const shouldShow = text.includes(filter);
            option.style.display = shouldShow ? "block" : "none";
            if (shouldShow) visibleCount++;
        });
        currentFocus = -1;
        removeActive();
        optionsContainer.style.display = visibleCount > 0 ? "block" : "none";
    };

    searchInput.addEventListener("focus", filterAndShowOptions);
    searchInput.addEventListener("blur", () =>
        setTimeout(() => {
            if (
                document.activeElement !== searchInput &&
                !optionsContainer.contains(document.activeElement)
            ) {
                optionsContainer.style.display = "none";
            }
        }, 150)
    );

    searchInput.addEventListener("keyup", (event) => {
        if (!["ArrowUp", "ArrowDown", "Enter"].includes(event.key)) {
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

        document.dispatchEvent(
            new CustomEvent("patient:selected", {
                bubbles: true,
                detail: { patientId: hiddenInput.value, selectUrl: selectUrl },
            })
        );
    };

    searchInput.addEventListener("keydown", (event) => {
        const visibleOptions = Array.from(
            optionsContainer.querySelectorAll(".option")
        ).filter((opt) => opt.style.display !== "none");
        if (["ArrowDown", "ArrowUp"].includes(event.key)) {
            event.preventDefault();
            addActive(currentFocus + (event.key === "ArrowDown" ? 1 : -1));
        } else if (event.key === "Enter") {
            event.preventDefault();
            const activeOption =
                optionsContainer.querySelector(".option.active");
            selectOption(
                activeOption ||
                    (visibleOptions.length > 0 ? visibleOptions[0] : null)
            );
        }
    });

    optionsContainer.addEventListener("mousedown", (event) => {
        const option = event.target.closest(".option");
        if (option) {
            selectOption(option);
            event.preventDefault(); // Prevent blur from firing and closing the dropdown prematurely
        }
    });

    // --- New functionality for disabling/clearing form elements ---
    const formFieldset = document.querySelector("#adl-form fieldset");
    const dateSelector = document.getElementById("date_selector");
    const dayNoSelector = document.getElementById("day_no_selector");
    const vitalInputs = document.querySelectorAll(".cdss-input.vital-input");
    const cdssButton = document.querySelector(
        "#adl-form button.button-default:nth-of-type(1)"
    ); // Assuming first button is CDSS
    const submitButton = document.querySelector(
        "#adl-form button[type='submit']"
    );
    const alertBoxes = document.querySelectorAll(".alert-box");

    const disableForm = (isDisabled) => {
        if (formFieldset) {
            formFieldset.disabled = isDisabled;
        }
        if (dateSelector) {
            dateSelector.disabled = isDisabled;
        }
        if (dayNoSelector) {
            dayNoSelector.disabled = isDisabled;
        }
        vitalInputs.forEach((input) => {
            input.disabled = isDisabled;
        });
        if (cdssButton) {
            cdssButton.disabled = isDisabled;
        }
        if (submitButton) {
            submitButton.disabled = isDisabled;
        }
    };

    const clearFormInputs = () => {
        vitalInputs.forEach((input) => {
            input.value = "";
            input.style.backgroundColor = ""; // Clear background color
            input.style.color = ""; // Clear text color
        });
        clearAlerts(); // Call clearAlerts when inputs are cleared
    };

    const clearAlerts = () => {
        const alertBoxes = document.querySelectorAll(".alert-box"); // Re-query alert boxes
        alertBoxes.forEach((alertBox) => {
            alertBox.innerHTML =
                '<span class="opacity-70 text-white font-semibold">NO ALERTS</span>';
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
    // --- End new functionality ---
}

// Expose the function to the global window object so it can be called from blade files
window.initializeSearchableDropdown = initializeSearchableDropdown;
