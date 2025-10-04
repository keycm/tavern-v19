// admin.js

document.addEventListener('DOMContentLoaded', () => {
    // --- Modal Functionality ---
    const reservationModal = document.getElementById('reservationModal');
    const closeButton = document.querySelector('.modal .close-button');
    const modalDetails = document.getElementById('modalDetails');
    const modalConfirmBtn = document.querySelector('.modal-confirm-btn');
    const modalDeleteBtn = document.querySelector('.modal-delete-btn');
    const modalDeclineBtn = document.querySelector('.modal-decline-btn');

    let currentReservationId = null;

    // Open Modal
    function openModal(reservationData) {
        // Log when the modal is attempting to open
        console.log("Attempting to open modal with data:", reservationData);

        // Defensive check: only open if reservationData is valid and not empty
        if (!reservationData || Object.keys(reservationData).length === 0) {
            console.warn("openModal called with no valid reservation data. Modal not opened.");
            return;
        }

        modalDetails.innerHTML = ''; // Clear previous content

        // Extract reservation ID and store it
        currentReservationId = reservationData['Reservation ID'];

        for (const key in reservationData) {
            if (Object.hasOwnProperty.call(reservationData, key)) {
                const p = document.createElement('p');
                p.innerHTML = `<strong>${key}:</strong> ${reservationData[key]}`;
                modalDetails.appendChild(p);
            }
        }
        reservationModal.style.display = 'flex'; // FIXED: Changed 'block' to 'flex' for centering
    }

    // Close Modal
    if (closeButton) {
        closeButton.addEventListener('click', () => {
            reservationModal.style.display = 'none';
            currentReservationId = null;
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target === reservationModal) {
            reservationModal.style.display = 'none';
            currentReservationId = null;
        }
    });

    // --- Action Buttons Functionality (View, Confirm, Delete, Decline) ---
    const reservationTableBody = document.querySelector('table tbody');

    if (reservationTableBody) {
        reservationTableBody.addEventListener('click', async (event) => {
            const target = event.target;

            // Ensure a button inside actions cell was clicked
            if (target.tagName === 'BUTTON' && target.closest('.actions')) {
                const row = target.closest('tr');
                if (!row) return;

                const reservationId = row.dataset.reservationId;
                const fullReservationJson = row.dataset.fullReservation;
                let fullReservationData = {};
                try {
                    fullReservationData = JSON.parse(fullReservationJson);
                } catch (e) {
                    console.error("Error parsing reservation data:", e);
                    return;
                }

                if (target.classList.contains('view-btn')) {
                    openModal(fullReservationData);
                }
            }
        });
    }


    // Event listeners for buttons INSIDE the modal
    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', async () => {
            if (currentReservationId) {
                const row = document.querySelector(`tr[data-reservation-id="${currentReservationId}"]`);
                // FIXED: Point to the correct file for status updates
                await updateReservation(currentReservationId, 'Confirmed', 'update', row, 'update_reservation_status.php');
                reservationModal.style.display = 'none';
            } else {
                console.error('No reservation selected for update.');
            }
        });
    }

    if (modalDeclineBtn) {
        modalDeclineBtn.addEventListener('click', async () => {
            if (currentReservationId) {
                const row = document.querySelector(`tr[data-reservation-id="${currentReservationId}"]`);
                // FIXED: Point to the correct file for status updates
                await updateReservation(currentReservationId, 'Declined', 'update', row, 'update_reservation_status.php');
                reservationModal.style.display = 'none';
            } else {
                console.error('No reservation selected for update.');
            }
        });
    }

    if (modalDeleteBtn) {
        modalDeleteBtn.addEventListener('click', async () => {
            if (currentReservationId && confirm('Are you sure you want to move this reservation to the deletion history?')) {
                const row = document.querySelector(`tr[data-reservation-id="${currentReservationId}"]`);
                // FIXED: Point to the correct file for soft-deleting
                await updateReservation(currentReservationId, null, 'delete', row, 'update_reservation.php');
                reservationModal.style.display = 'none';
            }
        });
    }

    // --- Generic Function to send status update/delete request ---
    async function updateReservation(reservationId, newStatus, actionType, rowElement, targetPhpFile) {
        const formData = new URLSearchParams();
        
        if (targetPhpFile === 'manage_deletion.php') {
            // This is for soft-deleting
            formData.append('item_id', reservationId);
            formData.append('item_type', 'Reservation');
             // Fetch item name from the row to log it
            const itemName = rowElement.querySelector('.customer-info strong').textContent;
            formData.append('item_name', itemName);
            formData.append('action', 'soft_delete');
        } else {
            // This is for status updates or soft deletes via update_reservation.php
            formData.append('reservation_id', reservationId);
            formData.append('action', actionType);
            if (actionType === 'update') {
                formData.append('status', newStatus);
            }
        }


        try {
            const response = await fetch(targetPhpFile, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (actionType === 'delete' || actionType === 'soft_delete') {
                    if (rowElement) {
                        rowElement.remove(); // Remove from view
                    }
                     console.log(result.message || 'Reservation moved to deletion history!');
                } else if (actionType === 'update') {
                    const statusSpan = rowElement.querySelector('.status-badge');
                    if (statusSpan) {
                        statusSpan.textContent = newStatus;
                        statusSpan.className = `status-badge ${newStatus.toLowerCase()}`;
                    }
                    console.log(result.message || 'Reservation status updated successfully!');
                }
            } else {
                console.error('Error: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }


    // --- Calendar Widget Functionality ---
    const calendarElement = document.getElementById('calendar');
    if (calendarElement) {
        function renderCalendar(year, month) {
            calendarElement.innerHTML = ''; // Clear existing content

            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            const date = new Date(year, month);
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const calendarHeader = document.createElement('div');
            calendarHeader.className = 'calendar-header';
            calendarHeader.innerHTML = `
                <button id="prevMonth" class="calendar-nav-btn">&laquo;</button>
                <h4 class="calendar-month-year">${monthNames[month]} ${year}</h4>
                <button id="nextMonth" class="calendar-nav-btn">&raquo;</button>
            `;
            calendarElement.appendChild(calendarHeader);

            const calendarGrid = document.createElement('div');
            calendarGrid.className = 'calendar-grid';

            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayNames.forEach(day => {
                const dayName = document.createElement('div');
                dayName.className = 'calendar-day-name';
                dayName.textContent = day;
                calendarGrid.appendChild(dayName);
            });

            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'calendar-day empty';
                calendarGrid.appendChild(emptyCell);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dayCell = document.createElement('div');
                dayCell.className = 'calendar-day';
                dayCell.textContent = day;
                if (day === new Date().getDate() && month === new Date().getMonth() && year === new Date().getFullYear()) {
                    dayCell.classList.add('current-day');
                }
                calendarGrid.appendChild(dayCell);
            }

            calendarElement.appendChild(calendarGrid);

            document.getElementById('prevMonth').addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
                renderCalendar(currentYear, currentMonth);
            });

            document.getElementById('nextMonth').addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
                renderCalendar(currentYear, currentMonth);
            });
        }

        let currentYear = new Date().getFullYear();
        let currentMonth = new Date().getMonth();
        renderCalendar(currentYear, currentMonth);
    }

    // --- Search and Stats Update ---
    const searchInputTop = document.getElementById('reservationSearchTop');
    if (searchInputTop) {
        searchInputTop.addEventListener('keyup', () => {
            const filter = searchInputTop.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const customerName = row.querySelector('.customer-info strong')?.textContent.toLowerCase() || '';
                const customerEmail = row.querySelector('.customer-info small')?.textContent.toLowerCase() || '';
                if (customerName.includes(filter) || customerEmail.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    const fetchCustomerStats = async () => {
        try {
            const response = await fetch('get_customer_stats.php');
            const data = await response.json();
            if (data.success) {
                document.getElementById('weeklyCustomerCount').textContent = data.weekly_customers;
                document.getElementById('monthlyCustomerCount').textContent = data.monthly_customers;
                document.getElementById('reservationsTodayCount').textContent = data.reservations_today;
                document.getElementById('totalUsersCount').textContent = data.total_users;
            }
        } catch (error) {
            console.error('Error fetching customer stats:', error);
        }
    };

    fetchCustomerStats();
    setInterval(fetchCustomerStats, 60000); // Refresh every 60 seconds
});