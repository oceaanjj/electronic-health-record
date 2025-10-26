// resources/js/search.js

document.addEventListener("DOMContentLoaded", function () {
    const dropdownContainer = document.querySelector(".searchable-dropdown");
    if (!dropdownContainer) return; // Exit if the component isn't on the page

    // Find the elements needed for the dropdown to function
    const searchInput = document.getElementById("patient_search_input");
    const hiddenInput = document.getElementById("patient_id_hidden");
    const optionsContainer = document.getElementById(
        "patient_options_container"
    );
    const options = optionsContainer.querySelectorAll(".option");

    // Get the parent form that needs to be submitted on selection
    const selectionForm = document.getElementById("patient-select-form");

    // Initially hide the options list
    optionsContainer.style.display = "none";

    // This function filters the patient list based on user input
    const filterAndShowOptions = () => {
        const filter = searchInput.value.toLowerCase().trim();
        options.forEach((option) => {
            const text = (option.textContent || option.innerText)
                .toLowerCase()
                .trim();
            option.style.display = text.includes(filter) ? "block" : "none";
        });
    };

    // Show options on focus
    searchInput.addEventListener("focus", () => {
        filterAndShowOptions();
        optionsContainer.style.display = "block";
    });

    // Filter as user types
    searchInput.addEventListener("keyup", filterAndShowOptions);

    // --- This is the key function ---
    const selectOption = (option) => {
        const patientId = option.getAttribute("data-value");
        const patientName = (option.textContent || option.innerText).trim();

        // 1. Set the values of the inputs
        searchInput.value = patientName;
        hiddenInput.value = patientId;
        optionsContainer.style.display = "none";

        // 2. Submit the form to reload the page with the selected patient
        if (selectionForm) {
            selectionForm.submit();
        }
    };

    // Handle selection when pressing "Enter"
    searchInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent default form submission
            const firstVisibleOption = optionsContainer.querySelector(
                '.option[style*="block"]'
            );
            if (firstVisibleOption) {
                selectOption(firstVisibleOption);
            }
        }
    });

    // Handle selection on click
    options.forEach((option) => {
        option.addEventListener("click", () => {
            selectOption(option);
        });
    });

    // Hide dropdown if user clicks away
    document.addEventListener("click", (event) => {
        if (!event.target.closest(".searchable-dropdown")) {
            optionsContainer.style.display = "none";
        }
    });
});
