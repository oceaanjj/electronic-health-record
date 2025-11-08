function initializeSearchableDropdown() {
    const dropdownContainer = document.querySelector(".searchable-dropdown:not([data-initialized])");
    if (!dropdownContainer) return;
    dropdownContainer.setAttribute("data-initialized", "true");

    const searchInput = document.getElementById("patient_search_input");
    const hiddenInput = document.getElementById("patient_id_hidden");
    const optionsContainer = document.getElementById("patient_options_container");
    if (!searchInput || !optionsContainer) return;

    const options = optionsContainer.querySelectorAll(".option");
    const selectUrl = dropdownContainer.dataset.selectUrl;

    let currentFocus = -1;
    optionsContainer.style.display = "none";

    const removeActive = () => {
        optionsContainer.querySelectorAll(".option").forEach(opt => opt.classList.remove("active"));
    };

    const addActive = (n) => {
        removeActive();
        const visibleOptions = Array.from(optionsContainer.querySelectorAll(".option")).filter(opt => opt.style.display !== "none");
        if (visibleOptions.length === 0) return;
        currentFocus = (n + visibleOptions.length) % visibleOptions.length;
        const focusedOption = visibleOptions[currentFocus];
        focusedOption.classList.add("active");
        focusedOption.scrollIntoView({ block: "nearest", behavior: "smooth" });
    };

    const filterAndShowOptions = () => {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;
        options.forEach(option => {
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
    searchInput.addEventListener("blur", () => setTimeout(() => {
        if (document.activeElement !== searchInput && !optionsContainer.contains(document.activeElement)) {
            optionsContainer.style.display = "none";
        }
    }, 150));

    searchInput.addEventListener("keyup", event => {
        if (!["ArrowUp", "ArrowDown", "Enter"].includes(event.key)) {
            filterAndShowOptions();
        }
    });

    const selectOption = (option) => {
        if (!option) return;
        searchInput.value = (option.textContent || option.innerText).trim();
        hiddenInput.value = option.getAttribute("data-value");
        optionsContainer.style.display = "none";
        currentFocus = -1;
        removeActive();
        document.dispatchEvent(new CustomEvent("patient:selected", {
            bubbles: true,
            detail: { patientId: hiddenInput.value, selectUrl: selectUrl },
        }));
    };

    searchInput.addEventListener("keydown", event => {
        const visibleOptions = Array.from(optionsContainer.querySelectorAll(".option")).filter(opt => opt.style.display !== "none");
        if (["ArrowDown", "ArrowUp"].includes(event.key)) {
            event.preventDefault();
            addActive(currentFocus + (event.key === "ArrowDown" ? 1 : -1));
        } else if (event.key === "Enter") {
            event.preventDefault();
            const activeOption = optionsContainer.querySelector(".option.active");
            selectOption(activeOption || (visibleOptions.length > 0 ? visibleOptions[0] : null));
        }
    });

    optionsContainer.addEventListener("mousedown", event => {
        const option = event.target.closest(".option");
        if (option) {
            selectOption(option);
            event.preventDefault(); // Prevent blur from firing and closing the dropdown prematurely
        }
    });
}

// Expose the function to the global window object so it can be called from blade files
window.initializeSearchableDropdown = initializeSearchableDropdown;
