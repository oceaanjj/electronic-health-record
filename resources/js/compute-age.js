document.addEventListener('DOMContentLoaded', function () {
    const birthdateInput = document.getElementById('birthdate');
    const ageInput = document.getElementById('age');

    if (!birthdateInput) {
        return;
    }

    function calculateAge() {
        const birthdate = new Date(birthdateInput.value);
        const today = new Date();

        if (isNaN(birthdate.getTime())) {
            ageInput.value = '';
            return;
        }

        let age = today.getFullYear() - birthdate.getFullYear();
        const monthDifference = today.getMonth() - birthdate.getMonth();

        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }
        ageInput.value = age;
    }

    birthdateInput.addEventListener('change', calculateAge);
    calculateAge();
});
