window.closeCdssAlert = function () {
    const wrapper = document.getElementById('cdss-alert-wrapper');
    const content = document.getElementById('cdss-alert-content');

    if (wrapper && content) {
        wrapper.classList.add('alert-exit');
        content.classList.add('alert-exit');

        setTimeout(() => {
            wrapper.remove();
        }, 500);
    }
};
