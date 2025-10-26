document.addEventListener("DOMContentLoaded", function () {
    const dropdownContainer = document.querySelector(".searchable-dropdown");
    if (!dropdownContainer) return; // Exit if the component isn't on the page

    const searchInput = document.getElementById("patient_search_input");
    const hiddenInput = document.getElementById("patient_id_hidden");
    const optionsContainer = document.getElementById(
        "patient_options_container"
    );
    const options = optionsContainer.querySelectorAll(".option"); // Get the URL needed by patient-loader.js from the data attribute

    const selectUrl = dropdownContainer.dataset.selectUrl; // Initially hide the options list

    optionsContainer.style.display = "none";

    const filterAndShowOptions = () => {
        const filter = searchInput.value.toLowerCase();
        let visibleCount = 0;
        const limit = 10; // Show a max of 10 results

        options.forEach((option) => {
            const text = (option.textContent || option.innerText).toLowerCase();
            const shouldShow = text.includes(filter) && visibleCount < limit;
            option.style.display = shouldShow ? "block" : "none";
            if (shouldShow) {
                visibleCount++;
            }
        });
    };

    searchInput.addEventListener("focus", () => {
        filterAndShowOptions();
        optionsContainer.style.display = "block";
    });

    searchInput.addEventListener("keyup", filterAndShowOptions);

    const selectOption = (option) => {
        const patientId = option.getAttribute("data-value");
        const patientName = option.textContent || option.innerText; // Set the UI values

        searchInput.value = patientName;
        hiddenInput.value = patientId;
        optionsContainer.style.display = "none"; // *** KEY CHANGE *** // Instead of submitting a form, dispatch the custom event // that patient-loader.js is waiting for.

        const event = new CustomEvent("patient:selected", {
            bubbles: true, // Allows the event to bubble up through the DOM
            detail: {
                patientId: patientId,
                selectUrl: selectUrl,
            },
        });
        document.dispatchEvent(event);
    };

    searchInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent form submission
            const firstVisibleOption = optionsContainer.querySelector(
                '.option[style*="block"]'
            );
            if (firstVisibleOption) {
                selectOption(firstVisibleOption);
            }
        }
    });

    options.forEach((option) => {
        option.addEventListener("click", () => {
            selectOption(option);
        });
    });

    document.addEventListener("click", (event) => {
        if (!event.target.closest(".searchable-dropdown")) {
            optionsContainer.style.display = "none";
        }
    });
});
