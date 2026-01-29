document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('audit-search');
    const tableBody = document.getElementById('audit-table-body');
    const loader = document.getElementById('audit-loading');

    if (!searchInput || !tableBody) return;

    let delay;

    searchInput.addEventListener('input', () => {
        clearTimeout(delay);
        delay = setTimeout(() => {
            const query = searchInput.value.trim();
            const url = new URL(window.location.href);
            if (query) url.searchParams.set('username_search', query);
            else url.searchParams.delete('username_search');

            loader.classList.remove('hidden');
            tableBody.style.transition = 'opacity 0.4s ease';
            tableBody.style.opacity = 0.3;

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                const newTbody = newDoc.querySelector('#audit-table-body');

                if (newTbody) {

                    tableBody.innerHTML = newTbody.innerHTML;

                    tableBody.style.opacity = 0;
                    requestAnimationFrame(() => {
                        tableBody.style.transition = 'opacity 0.4s ease';
                        tableBody.style.opacity = 1;
                    });
                }
            })
            .catch(() => console.error('Search failed'))
            .finally(() => {
                setTimeout(() => loader.classList.add('hidden'), 300);
            });
        }, 400);
    });
});
