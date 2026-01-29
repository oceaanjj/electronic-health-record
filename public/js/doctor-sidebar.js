window.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('mySidenav');
    const main = document.getElementById('main');
    const arrow = document.getElementById('arrowBtn');

    if (!sidebar || !main || !arrow) {
        return;
    }

    sidebar.style.transition = 'none';
    main.style.transition = 'none';

    const isOpen = localStorage.getItem('sidebarOpen') === 'true';

    if (isOpen) {
        sidebar.classList.remove('-translate-x-full');
        main.classList.add('ml-[260px]');
        arrow.classList.replace('-right-24', '-right-10');
        arrow.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        main.classList.remove('ml-[260px]');
        arrow.classList.replace('-right-10', '-right-24');
        arrow.classList.add('hidden');
    }

    void sidebar.offsetHeight;

    requestAnimationFrame(() => {
        sidebar.style.transition = '';
        main.style.transition = '';
    });
});

function openNav() {
    const sidebar = document.getElementById('mySidenav');
    const main = document.getElementById('main');
    const arrow = document.getElementById('arrowBtn');

    if (!sidebar || !main || !arrow) {
        return;
    }

    sidebar.classList.remove('-translate-x-full');
    main.classList.add('ml-[260px]');
    arrow.classList.replace('-right-24', '-right-10');
    arrow.classList.remove('hidden');

    localStorage.setItem('sidebarOpen', 'true');
}

function closeNav() {
    const sidebar = document.getElementById('mySidenav');
    const main = document.getElementById('main');
    const arrow = document.getElementById('arrowBtn');

    if (!sidebar || !main || !arrow) {
        return;
    }

    sidebar.classList.add('-translate-x-full');
    main.classList.remove('ml-[260px]');
    arrow.classList.replace('-right-10', '-right-24');

    localStorage.setItem('sidebarOpen', 'false');

    setTimeout(() => {
        arrow.classList.add('hidden');
    }, 0);
}
