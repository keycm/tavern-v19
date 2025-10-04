document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('historyTableBody');
    const searchInput = document.getElementById('historySearch');
    const alertModal = document.getElementById('alertModal');
    const alertModalTitle = document.getElementById('alertModalTitle');
    const alertModalMessage = document.getElementById('alertModalMessage');
    const alertModalActions = document.getElementById('alertModalActions');
    const alertModalCloseBtn = alertModal.querySelector('.close-button');

    const showAlert = (title, message, callback = null) => {
        alertModalTitle.textContent = title;
        alertModalMessage.textContent = message;
        alertModalActions.innerHTML = '<button class="btn" id="alertOkBtn" style="background-color: #007bff; color: white;">OK</button>';
        alertModal.style.display = 'flex';

        document.getElementById('alertOkBtn').onclick = () => {
            alertModal.style.display = 'none';
            if (callback) callback();
        };
    };

    const showConfirm = (title, message, callback) => {
        alertModalTitle.textContent = title;
        alertModalMessage.textContent = message;
        alertModalActions.innerHTML = `
            <button class="btn" id="confirmCancelBtn" style="background-color: #6c757d; color: white;">Cancel</button>
            <button class="btn" id="confirmOkBtn" style="background-color: #dc3545; color: white;">Yes, Proceed</button>
        `;
        alertModal.style.display = 'flex';

        document.getElementById('confirmOkBtn').onclick = () => {
            alertModal.style.display = 'none';
            callback(true);
        };
        document.getElementById('confirmCancelBtn').onclick = () => {
            alertModal.style.display = 'none';
            callback(false);
        };
    };
    
    alertModalCloseBtn.onclick = () => alertModal.style.display = 'none';
    window.addEventListener('click', (event) => {
        if (event.target === alertModal) {
            alertModal.style.display = 'none';
        }
    });

    // --- Action Button Handlers (Restore / Purge) ---
    tableBody.addEventListener('click', (event) => {
        const target = event.target;
        const row = target.closest('tr');
        if (!row) return;

        const logId = row.dataset.logId;

        if (target.classList.contains('restore-btn')) {
            showConfirm('Confirm Restore', 'Are you sure you want to restore this item?', (confirmed) => {
                if (confirmed) {
                    handleDeletionAction(logId, 'restore', row);
                }
            });
        } else if (target.classList.contains('purge-btn')) {
            showConfirm('Confirm Permanent Deletion', 'Are you sure you want to permanently delete this item? This action cannot be undone.', (confirmed) => {
                if (confirmed) {
                    handleDeletionAction(logId, 'purge', row);
                }
            });
        }
    });

    // --- API Call to Manage Deletions ---
    async function handleDeletionAction(logId, action, rowElement) {
        const formData = new URLSearchParams();
        formData.append('log_id', logId);
        formData.append('action', action);

        try {
            const response = await fetch('manage_deletion.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            showAlert(result.success ? 'Success!' : 'Error', result.message, () => {
                if (result.success && rowElement) {
                    rowElement.remove();
                }
            });

        } catch (error) {
            console.error('Error:', error);
            showAlert('Error', 'An unexpected network error occurred.');
        }
    }

    // --- Search Functionality ---
    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                row.style.display = rowText.includes(filter) ? '' : 'none';
            });
        });
    }
});