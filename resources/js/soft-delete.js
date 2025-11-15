document.addEventListener('DOMContentLoaded', function () {
    const ehrTable = document.querySelector('.ehr-table');
    if (!ehrTable) return; // Exit if table doesn't exist on this page
    
    ehrTable.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-delete') || e.target.classList.contains('btn-recover')) {
            e.preventDefault();

            const button = e.target;
            const form = button.closest('form');
            const row = button.closest('tr');
            const isDelete = button.classList.contains('btn-delete');
            const url = form.action;
            const token = form.querySelector('input[name="_token"]').value;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: isDelete ? 'DELETE' : 'POST'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const actionsCell = row.querySelector('td:last-child');
                    const patientId = row.dataset.id;
                    if (isDelete) {
                        row.style.backgroundColor = '#ffdddd';
                        row.style.color = 'red';
                        actionsCell.innerHTML = `
                            <form action="/patients/${patientId}/recover" method="POST" style="display:inline;">
                                <input type="hidden" name="_token" value="${token}">
                                <button type="submit" class="btn-recover">Recover</button>
                            </form>
                        `;
                    } else {
                        row.style.backgroundColor = '';
                        row.style.color = '';
                        actionsCell.innerHTML = `
                            <a href="/patients/${patientId}/edit" class="btn-edit">Edit</a>
                            <form action="/patients/${patientId}" method="POST" style="display:inline;">
                                <input type="hidden" name="_token" value="${token}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        `;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
});
