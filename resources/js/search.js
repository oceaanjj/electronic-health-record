document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("patient-select-form");
    const searchInput = document.getElementById("patient_search_input");
    const hiddenInput = document.getElementById("patient_id_hidden");
    const optionsContainer = document.getElementById(
        "patient_options_container"
    );
    const options = optionsContainer.querySelectorAll(".option");

    // Initially hide the options list
    optionsContainer.style.display = "none";

    // Reusable function to filter and display options with a limit
    const filterAndShowOptions = () => {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;

        options.forEach((option) => {
            const text = (option.textContent || option.innerText).toLowerCase();

            if (text.includes(filter) && visibleCount < 10) {
                option.style.display = "block";
                visibleCount++;
            } else {
                // Hide if it doesn't match OR if the limit of 10 is reached
                option.style.display = "none";
            }
        });
    };

    //Show and filter the options list on focus
    searchInput.addEventListener("focus", () => {
        filterAndShowOptions(); // Run the filter immediately on click
        optionsContainer.style.display = "block";
    });

    //Filter the options list as the user types
    searchInput.addEventListener("keyup", () => {
        filterAndShowOptions(); // Re-run the filter on every key press
    });

    //Enter
    searchInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            // Prevent the default form submission behavior
            event.preventDefault();

            // Find the first option that is currently visible
            const firstVisibleOption = optionsContainer.querySelector(
                '.option[style*="block"]'
            );

            // If a visible option exists, trigger a click on it
            if (firstVisibleOption) {
                firstVisibleOption.click();
            }
        }
    });

    //Handle what happens when a user clicks a patient from the list
    options.forEach((option) => {
        option.addEventListener("click", () => {
            // Set the search box text to the patient's name
            searchInput.value = option.textContent || option.innerText;
            // Set the hidden input's value to the patient's ID
            hiddenInput.value = option.getAttribute("data-value");
            // Hide the options list
            optionsContainer.style.display = "none";
            // Submit the form to update the page
            form.submit();
        });
    });

    //Hide the options list if the user clicks anywhere else on the page
    document.addEventListener("click", (event) => {
        // Check if the click was outside of our dropdown component
        if (!event.target.closest(".searchable-dropdown")) {
            optionsContainer.style.display = "none";
        }
    });
});
