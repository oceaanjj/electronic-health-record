window.initializeAdlDateSync = function () {
    const dateSelector = document.getElementById("date_selector");
    const dayNoSelector = document.getElementById("day_no_selector");
    const dropdownContainer = document.querySelector(".searchable-dropdown");

    if (!dateSelector || !dayNoSelector || !dropdownContainer) {
        return;
    }

    const admissionDateStr = dropdownContainer.dataset.admissionDate;
    if (!admissionDateStr) {
        return;
    }
    const admissionDate = new Date(admissionDateStr);
    // to account for timezone differences
    admissionDate.setMinutes(
        admissionDate.getMinutes() + admissionDate.getTimezoneOffset()
    );

    function updateDate() {
        const dayNo = parseInt(dayNoSelector.value, 10);
        if (isNaN(dayNo)) {
            return;
        }

        const newDate = new Date(admissionDate.getTime());
        newDate.setDate(newDate.getDate() + dayNo);

        const year = newDate.getFullYear();
        const month = String(newDate.getMonth() + 1).padStart(2, '0');
        const day = String(newDate.getDate()).padStart(2, '0');

        dateSelector.value = `${year}-${month}-${day}`;
        dateSelector.dispatchEvent(new Event('change'));
    }

    function updateDayNo() {
        const selectedDate = new Date(dateSelector.value);
        if (isNaN(selectedDate.getTime())) {
            return;
        }
        // to account for timezone differences
        selectedDate.setMinutes(selectedDate.getMinutes() + selectedDate.getTimezoneOffset());

        const diffTime = Math.abs(selectedDate - admissionDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        dayNoSelector.value = diffDays;
    }

    dayNoSelector.addEventListener("change", updateDate);
    dateSelector.addEventListener("change", updateDayNo);
};
