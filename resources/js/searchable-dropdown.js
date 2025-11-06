document.addEventListener("DOMContentLoaded", function () {
    const dropdownContainer = document.querySelector(".searchable-dropdown");
    if (!dropdownContainer) return;

    const searchInput = document.getElementById("patient_search_input");
    const hiddenInput = document.getElementById("patient_id_hidden");
    const optionsContainer = document.getElementById(
        "patient_options_container"
    );
    const options = optionsContainer.querySelectorAll(".option");
    const selectUrl = dropdownContainer.dataset.selectUrl;

    // State variable to track the currently highlighted option (for keyboard navigation)
    let currentFocus = -1;

    // Initially hide the options list
    optionsContainer.style.display = "none";

    // Helper function to remove the active class from all options
    const removeActive = () => {
        options.forEach((option) => {
            option.classList.remove("active");
        });
    };

    // Helper function to add the active class to the currently focused option
    const addActive = (n) => {
        removeActive();
        if (n >= options.length) n = 0;
        if (n < 0) n = options.length - 1;
        currentFocus = n;

        // Find the visible options only
        const visibleOptions = Array.from(options).filter(
            (opt) => opt.style.display !== "none"
        );

        if (visibleOptions.length > 0) {
            // Find the index of the currently focused item within the VISIBLE options list
            const focusedOption =
                visibleOptions[currentFocus % visibleOptions.length];
            if (focusedOption) {
                focusedOption.classList.add("active");
                // Optional: Scroll the container to ensure the element is visible
                focusedOption.scrollIntoView({
                    block: "nearest",
                    behavior: "smooth",
                });
            }
        }
    };

    const filterAndShowOptions = () => {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;

        options.forEach((option) => {
            const text = (option.textContent || option.innerText).toLowerCase();
            const shouldShow = text.includes(filter);
            option.style.display = shouldShow ? "block" : "none";
            if (shouldShow) {
                visibleCount++;
            }
        });

        // Reset focus when filtering changes
        currentFocus = -1;
        removeActive();

        if (visibleCount > 0) {
            optionsContainer.style.display = "block";
        } else {
            optionsContainer.style.display = "none";
        }
    };

    searchInput.addEventListener("focus", () => {
        filterAndShowOptions();
    });

    searchInput.addEventListener("keyup", (event) => {
        // Only run filter on keyup for normal text input, not for navigation keys
        if (
            event.key !== "ArrowUp" &&
            event.key !== "ArrowDown" &&
            event.key !== "Enter"
        ) {
            filterAndShowOptions();
        }
    });

    const selectOption = (option) => {
        const patientId = option.getAttribute("data-value");
        const patientName = (option.textContent || option.innerText).trim();

        // Set the UI values
        searchInput.value = patientName;
        hiddenInput.value = patientId;
        optionsContainer.style.display = "none";

        // Reset focus state
        currentFocus = -1;
        removeActive();

        // Dispatch the custom event for patient-loader.js
        const event = new CustomEvent("patient:selected", {
            bubbles: true,
            detail: {
                patientId: patientId,
                selectUrl: selectUrl,
            },
        });
        document.dispatchEvent(event);
    };

    // New and updated keydown listener for navigation
    searchInput.addEventListener("keydown", (event) => {
        const visibleOptions = Array.from(options).filter(
            (opt) => opt.style.display !== "none"
        );

        if (event.key === "ArrowDown" || event.key === "ArrowUp") {
            event.preventDefault(); // Stop cursor movement in input

            if (visibleOptions.length > 0) {
                // Determine the next focus index
                const direction = event.key === "ArrowDown" ? 1 : -1;
                let nextFocus = currentFocus + direction;

                // Loop the focus
                if (nextFocus >= visibleOptions.length) {
                    nextFocus = 0;
                } else if (nextFocus < 0) {
                    nextFocus = visibleOptions.length - 1;
                }

                // Set the active class on the determined option
                addActive(nextFocus);
            }
        } else if (event.key === "Enter") {
            event.preventDefault(); // Prevent form submission

            // Check if an option is currently focused (highlighted by keyboard)
            const activeOption =
                optionsContainer.querySelector(".option.active");

            if (activeOption) {
                // Select the actively highlighted option
                selectOption(activeOption);
            } else {
                // Fallback: If no option is highlighted, select the first visible option
                const firstVisibleOption = visibleOptions[0];
                if (firstVisibleOption) {
                    selectOption(firstVisibleOption);
                }
            }
        }
    });

    options.forEach((option) => {
        option.addEventListener("click", () => {
            selectOption(option);
        });
    });

    // Fix for accidental close during text selection
    document.addEventListener("click", (event) => {
        setTimeout(() => {
            if (!event.target.closest(".searchable-dropdown")) {
                if (document.activeElement !== searchInput) {
                    optionsContainer.style.display = "none";
                }
            }
        }, 100);
    });
});
