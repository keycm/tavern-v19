<?php
session_start();
require_once 'db_connect.php';

// If the user is not logged in, redirect them to the homepage.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

// Fetch blocked dates
$blocked_dates = [];
$sql = "SELECT block_date FROM blocked_dates";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $blocked_dates[] = $row['block_date'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tavern Publico - Reservation</title>
    <link rel="stylesheet" href="CSS/main.css">
    <link rel="stylesheet" href="CSS/reservation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* --- Styles for the new notification modal --- */
        #notificationModal .modal-content {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            max-width: 500px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            animation: fadeIn 0.4s ease-out;
            text-align: center;
        }
        #notificationModal .close-button {
            position: absolute;
            top: 15px;
            right: 20px;
            color: #aaa;
            font-size: 2.2em;
            font-weight: lighter;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        #notificationModal .close-button:hover {
            color: #555;
        }
        #notificationModal .modal-header-icon {
            font-size: 4.5em;
            margin-bottom: 20px;
            animation: bounceIn 0.8s ease-out;
        }
        /* Color for success icon */
        #notificationModal .modal-header-icon.success {
            color: #28a745; 
        }
        /* Color for error icon */
        #notificationModal .modal-header-icon.error {
            color: #dc3545;
        }
        #notificationModal h2 {
            font-size: 2.2em;
            margin-bottom: 15px;
            color: #333;
            font-weight: 700;
        }
        #notificationModal p {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 35px;
            line-height: 1.6;
        }
        #notificationModal .modal-close-btn {
            background-color: #FFD700;
            color: #333;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            min-width: 120px;
        }
        #notificationModal .modal-close-btn:hover {
            background-color: #e6c200;
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); opacity: 1; }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>

    <?php include 'partials/header.php'; ?>

    <section class="reservation-hero-section">
        <img src="images/1st.jpg" alt="Tavern Publico exterior at night" class="reservation-bg-image">
        <div class="reservation-overlay">
            <div class="reservation-container">
                <div class="reservation-form-card">
                    <h2>Schedule a Reservation</h2>
                    <form id="reservationForm" class="reservation-form" action="process_reservation.php" method="POST">
                        <div class="form-group-inline">
                            <div class="form-group">
                                <label for="resDate">Date</label>
                                <input type="date" id="resDate" name="resDate" required>
                            </div>
                            <div class="form-group">
                                <label for="resTime">Time</label>
                                <select id="resTime" name="resTime" required>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">1:00 PM</option>
                                    <option value="14:00">2:00 PM</option>
                                    <option value="15:00">3:00 PM</option>
                                    <option value="16:00">4:00 PM</option>
                                    <option value="17:00">5:00 PM</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="numGuests">Number of Guests</label>
                            <input type="number" id="numGuests" name="numGuests" min="1" placeholder="Enter number of guests" required>
                        </div>
                        <div class="form-group">
                            <label for="resName">Name</label>
                            <input type="text" id="resName" name="resName" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <label for="resPhone">Phone Number</label>
                            <input type="tel" id="resPhone" name="resPhone" placeholder="e.g., 09123456789" required>
                        </div>
                        <div class="form-group">
                            <label for="resEmail">Email</label>
                            <input type="email" id="resEmail" name="resEmail" placeholder="Your Email" required>
                        </div>
                        <button type="submit" class="btn btn-primary confirm-reservation-btn">Confirm Reservation</button>
                    </form>
                </div>

                <div class="hours-card">
                    <h3>Hours of Operation</h3>
                    <p><strong>Monday - Thursday</strong><br>11:00 AM - 6:00 PM</p>
                    <p><strong>Friday - Saturday</strong><br>11:00 AM - 7:00 PM</p>
                    <p><strong>Sunday</strong><br>12:00 PM - 9:00 PM</p>
                </div>
            </div>
        </div>
    </section>
    
    <div id="notificationModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div id="modalHeaderIcon" class="modal-header-icon">
                </div>
            <h2 id="modalTitle"></h2>
            <p id="modalMessage"></p>
            <button class="btn modal-close-btn">OK</button>
        </div>
    </div>


    <?php include 'partials/footer.php'; ?>
    <?php include 'partials/Signin-Signup.php'; ?>

    <script src="JS/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const resDateInput = document.getElementById('resDate');
            const blockedDates = <?php echo json_encode($blocked_dates); ?>;
            
            // Modal elements
            const notificationModal = document.getElementById('notificationModal');
            const modalHeaderIcon = document.getElementById('modalHeaderIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const closeButton = notificationModal.querySelector('.close-button');
            const okButton = notificationModal.querySelector('.modal-close-btn');

            // Function to show the modal with dynamic content
            function showModal(type, title, message) {
                modalHeaderIcon.innerHTML = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';
                modalHeaderIcon.className = 'modal-header-icon ' + type; // sets 'success' or 'error' class
                modalTitle.textContent = title;
                modalMessage.textContent = message;
                notificationModal.style.display = 'flex';
            }

            // Frontend check for blocked dates
            if (resDateInput) {
                const today = new Date();
                const day = String(today.getDate()).padStart(2, '0');
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const year = today.getFullYear();
                resDateInput.min = `${year}-${month}-${day}`;
                resDateInput.value = `${year}-${month}-${day}`;

                resDateInput.addEventListener('change', function() {
                    if (blockedDates.includes(this.value)) {
                        showModal('error', 'Date Not Available', 'The selected date is fully booked or unavailable. Please choose a different date.');
                        this.value = ''; // Clear the invalid selection
                    }
                });
            }
            
            // Check for status from server-side validation
            const urlParams = new URLSearchParams(window.location.search);
            const cleanUrl = window.location.pathname;

            if (urlParams.has('status')) {
                const status = urlParams.get('status');
                if (status === 'success') {
                    showModal(
                        'success', 
                        'Reservation Submitted!', 
                        'Your reservation request has been received. Please wait for the administrator to confirm your booking. You will receive a notification once it is approved.'
                    );
                } else if (status === 'error') {
                    const errorMessage = urlParams.get('message') || 'An unknown error occurred.';
                    showModal('error', 'Reservation Failed', errorMessage);
                }
                // Clean the URL to prevent the modal from showing on refresh
                history.replaceState(null, '', cleanUrl);
            }

            // Modal close events
            const closeModal = () => notificationModal.style.display = 'none';
            closeButton.addEventListener('click', closeModal);
            okButton.addEventListener('click', closeModal);
            window.addEventListener('click', (event) => {
                if (event.target == notificationModal) {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>