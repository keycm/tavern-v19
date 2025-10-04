<?php
session_start();
require_once 'db_connect.php'; // Include your database connection

// Check if the user is logged in AND is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Fetch all reservations from the database
$allReservations = [];
$sql = "SELECT r.reservation_id, r.user_id, r.res_date, r.res_time, r.num_guests, r.res_name, r.res_phone, r.res_email, r.status, r.created_at, u.avatar 
        FROM reservations r
        LEFT JOIN users u ON r.user_id = u.user_id
        WHERE r.deleted_at IS NULL 
        ORDER BY r.created_at DESC";

if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $allReservations[] = $row;
    }
    mysqli_free_result($result);
} else {
    error_log("Reservation page database error: " . mysqli_error($link));
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tavern Publico - All Reservations</title>
    <link rel="stylesheet" href="CSS/admin.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        /* Inlined Modal CSS to override cache */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            position: relative;
            animation-name: animatetop;
            animation-duration: 0.4s;
        }

        @keyframes animatetop {
            from {top: -300px; opacity: 0}
            to {top: 0; opacity: 1}
        }

        .modal-content h2 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 26px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .modal-content p {
            margin-bottom: 12px;
            line-height: 1.8;
            color: #555;
            font-size: 15px;
        }

        .close-button {
            color: #888;
            font-size: 32px;
            font-weight: bold;
            position: absolute;
            top: 15px;
            right: 25px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-button:hover,
        .close-button:focus {
            color: #333;
            text-decoration: none;
        }

        .modal-actions {
            margin-top: 30px;
            text-align: right;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="page-wrapper">

        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <img src="Tavern.png" alt="Home Icon" class="home-icon">
            </div>
            <nav>
                <ul class="sidebar-menu">
                    <li class="menu-item"><a href="admin.php"><i class="material-icons">dashboard</i> Dashboard</a></li>
                     <li class="menu-item"><a href="update.php"><i class="material-icons">file_upload</i> Upload Management</a></li>
                    <li class="menu-item active"><a href="reservation.php"><i class="material-icons">event_note</i> Reservation</a></li>
                </ul>
                <div class="user-management-title">User Management</div>
                <ul class="sidebar-menu user-management-menu">
                   <li class="menu-item"><a href="notification_control.php"><i class="material-icons">notifications</i> Notification Control</a></li>
                    <li class="menu-item"><a href="table_management.php"><i class="material-icons">table_chart</i>Calendar Management</a></li>
                    <li class="menu-item"><a href="customer_database.php"><i class="material-icons">people</i> Customer Database</a></li>
                    <li class="menu-item"><a href="reports.php"><i class="material-icons">analytics</i>Reservation Reports</a></li>
                    <li class="menu-item"><a href="deletion_history.php"><i class="material-icons">history</i>Archive</a></li>
                    <li class="menu-item"><a href="logout.php"><i class="material-icons">logout</i> Log out</a></li>
                </ul>
            </nav>
        </aside>

        <div class="admin-content-area">
            <header class="main-header">
                <div class="header-content">
                    <div class="admin-header-right">
                         <div class="admin-user-info">
                            <span class="admin-username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <span class="admin-role"></span>
                        </div>
                        <?php
                        $admin_avatar_path = isset($_SESSION['avatar']) && file_exists($_SESSION['avatar']) 
                                            ? htmlspecialchars($_SESSION['avatar']) 
                                            : 'images/default_avatar.png';
                        ?>
                        <img src="<?php echo $admin_avatar_path; ?>" alt="Admin Avatar" class="admin-avatar">
                    </div>
                </div>
            </header>

            <main class="dashboard-main-content">
                <div class="reservation-page-header">
                    <h1>All Reservations</h1>
                    <input type="text" id="reservationSearch" class="search-input" placeholder="Search reservations...">
                    <button id="addReservationBtn" class="btn btn-primary" style="background-color: #28a745;">Add New Reservation</button>
                </div>

                <section class="all-reservations-section">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>CUSTOMER</th>
                                    <th>DATE</th>
                                    <th>TIME</th>
                                    <th>GUESTS</th>
                                    <th>PHONE</th>
                                    <th>STATUS</th>
                                    <th>BOOKED AT</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($allReservations)): ?>
                                    <tr><td colspan="8" style="text-align: center;">No reservations found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($allReservations as $reservation): ?>
                                        <?php
                                            $statusClass = strtolower($reservation['status']);
                                            $fullReservationData = [
                                                'reservation_id' => $reservation['reservation_id'], 'user_id' => $reservation['user_id'] ?? 'N/A',
                                                'res_date' => $reservation['res_date'], 'res_time' => $reservation['res_time'],
                                                'num_guests' => $reservation['num_guests'], 'res_name' => $reservation['res_name'],
                                                'res_phone' => $reservation['res_phone'], 'res_email' => $reservation['res_email'],
                                                'status' => $reservation['status'], 'created_at' => $reservation['created_at']
                                            ];
                                            $fullReservationJson = htmlspecialchars(json_encode($fullReservationData), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <tr data-reservation-id="<?php echo $reservation['reservation_id']; ?>" data-full-reservation='<?php echo $fullReservationJson; ?>'>
                                            <td>
                                                <?php
                                                $avatar_path = !empty($reservation['avatar']) && file_exists($reservation['avatar']) ? $reservation['avatar'] : 'images/default_avatar.png';
                                                
                                                $customer_info_html = '
                                                    <div class="customer-info">
                                                        <img src="' . htmlspecialchars($avatar_path) . '" alt="Customer Avatar" class="customer-avatar">
                                                        <div>
                                                            <strong>' . htmlspecialchars($reservation['res_name']) . '</strong><br>
                                                            <small>' . htmlspecialchars($reservation['res_email']) . '</small>
                                                        </div>
                                                    </div>';
                                                
                                                if (!empty($reservation['user_id'])) {
                                                    // BUG FIX: Added return_to=reservation parameter
                                                    echo '<a href="view_customer.php?id=' . $reservation['user_id'] . '&return_to=reservation" style="text-decoration: none; color: inherit;">' . $customer_info_html . '</a>';
                                                } else {
                                                    echo $customer_info_html;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($reservation['res_date']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['res_time']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['num_guests']); ?></td>
                                            <td><?php echo htmlspecialchars($reservation['res_phone']); ?></td>
                                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($reservation['status']); ?></span></td>
                                            <td><?php echo htmlspecialchars($reservation['created_at']); ?></td>
                                            <td class="actions">
                                                <button class="btn btn-small view-edit-btn">View/Edit</button>
                                                <button class="btn btn-small delete-btn">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container">
                        <button class="btn" id="prevPageBtn" disabled>&laquo; Previous</button>
                        <div id="pageNumbers"></div>
                        <button class="btn" id="nextPageBtn">Next &raquo;</button>
                    </div>
                </section>
            </main>

            <div id="reservationModal" class="modal">
                <div class="modal-content">
                    <span class="close-button">&times;</span>
                    <h2>Reservation Details & Edit</h2>
                    <form id="editReservationForm">
                        <input type="hidden" id="modalReservationId" name="reservation_id">
                        <div class="form-group"><label for="modalResName">Customer Name:</label><input type="text" id="modalResName" name="res_name" required></div>
                        <div class="form-group"><label for="modalResEmail">Email:</label><input type="email" id="modalResEmail" name="res_email" required></div>
                        <div class="form-group"><label for="modalResPhone">Phone:</label><input type="tel" id="modalResPhone" name="res_phone"></div>
                        <div class="form-group"><label for="modalResDate">Date:</label><input type="date" id="modalResDate" name="res_date" required></div>
                        <div class="form-group"><label for="modalResTime">Time:</label><input type="time" id="modalResTime" name="res_time" required></div>
                        <div class="form-group"><label for="modalNumGuests">Number of Guests:</label><input type="number" id="modalNumGuests" name="num_guests" min="1" required></div>
                        <div class="form-group"><label for="modalStatus">Status:</label><select id="modalStatus" name="status"><option value="Pending">Pending</option><option value="Confirmed">Confirmed</option><option value="Cancelled">Cancelled</option><option value="Declined">Declined</option></select></div>
                        <div class="form-group"><label for="modalCreatedAt">Booked At:</label><input type="text" id="modalCreatedAt" name="created_at" readonly></div>
                        <div class="modal-actions">
                            <button type="submit" class="btn modal-save-btn">Save Changes</button>
                            <button type="button" class="btn modal-delete-btn">Delete Reservation</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="addReservationModal" class="modal">
                <div class="modal-content">
                    <span class="close-button">&times;</span>
                    <h2>Add New Walk-in Reservation</h2>
                    <form id="addReservationForm">
                        <div class="form-group"><label for="addResName">Customer Name:</label><input type="text" id="addResName" name="res_name" required></div>
                        <div class="form-group"><label for="addResEmail">Email:</label><input type="email" id="addResEmail" name="res_email" required></div>
                        <div class="form-group"><label for="addResPhone">Phone:</label><input type="tel" id="addResPhone" name="res_phone" required></div>
                        <div class="form-group"><label for="addResDate">Date:</label><input type="date" id="addResDate" name="res_date" required></div>
                        <div class="form-group"><label for="addResTime">Time:</label><input type="time" id="addResTime" name="res_time" required></div>
                        <div class="form-group"><label for="addNumGuests">Number of Guests:</label><input type="number" id="addNumGuests" name="num_guests" min="1" required></div>
                        <div class="modal-actions">
                            <button type="submit" class="btn modal-save-btn">Add Reservation</button>
                        </div>
                    </form>
                </div>
            </div>


            <div id="confirmDeleteModal" class="modal">
                <div class="modal-content" style="max-width: 500px;">
                    <span class="close-button">&times;</span>
                    <h2>Confirm Deletion</h2>
                    <p>Are you sure you want to move this reservation to the deletion history? It will be permanently deleted after 30 days.</p>
                    <div class="modal-actions">
                        <button type="button" class="btn" id="cancelDeleteBtn" style="background-color: #6c757d; color: white;">Cancel</button>
                        <button type="button" class="btn delete-btn" id="confirmDeleteBtn">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="JS/reservation.js"></script>
</body>
</html>