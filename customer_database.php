<?php
session_start();
require_once 'db_connect.php';

// Check if the user is logged in AND is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

// Fetch all non-admin users from the database
$users = [];
$sql = "SELECT user_id, username, email, created_at, is_verified FROM users WHERE is_admin = 0 ORDER BY created_at DESC";

if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_free_result($result);
} else {
    error_log("Customer Database page error: " . mysqli_error($link));
}

mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tavern Publico - Customer Database</title>
    <link rel="stylesheet" href="CSS/admin.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
                    <li class="menu-item"><a href="reservation.php"><i class="material-icons">event_note</i> Reservation</a></li>
                </ul>
                <div class="user-management-title">User Management</div>
                <ul class="sidebar-menu user-management-menu">
                    <li class="menu-item"><a href="notification_control.php"><i class="material-icons">notifications</i> Notification Control</a></li>
                    <li class="menu-item"><a href="table_management.php"><i class="material-icons">table_chart</i> Calendar Management</a></li>
                    <li class="menu-item active"><a href="customer_database.php"><i class="material-icons">people</i> Customer Database</a></li>
                    <li class="menu-item"><a href="reports.php"><i class="material-icons">analytics</i>Reservation Reports</a></li>
                    <li class="menu-item"><a href="deletion_history.php"><i class="material-icons">history</i> Archive</a></li>
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
                    <h1>Customer Database</h1>
                    <input type="text" id="userSearch" class="search-input" placeholder="Search customers...">
                    <button id="addNewUserBtn" class="btn btn-primary" style="background-color: #28a745;">Add New Customer</button>
                </div>

                <section class="all-reservations-section">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>USER ID</th>
                                    <th>USERNAME</th>
                                    <th>EMAIL</th>
                                    <th>DATE JOINED</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <?php if (empty($users)): ?>
                                    <tr><td colspan="6" style="text-align: center;">No customers found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr data-user-id="<?= $user['user_id']; ?>"
                                            data-username="<?= htmlspecialchars($user['username'], ENT_QUOTES); ?>"
                                            data-email="<?= htmlspecialchars($user['email'], ENT_QUOTES); ?>">
                                            <td><?= sprintf('%04d', $user['user_id']); ?></td>
                                            <td><?= htmlspecialchars($user['username']); ?></td>
                                            <td><?= htmlspecialchars($user['email']); ?></td>
                                            <td><?= htmlspecialchars(date('Y-m-d', strtotime($user['created_at']))); ?></td>
                                            <td>
                                                <?php if ($user['is_verified']): ?>
                                                    <span class="status-badge confirmed">Verified</span>
                                                <?php else: ?>
                                                    <span class="status-badge pending">Not Verified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="actions">
                                                <?php if (!$user['is_verified']): ?>
                                                    <button class="btn btn-small verify-btn" style="background-color: #17a2b8;">Verify</button>
                                                <?php endif; ?>
                                                <button class="btn btn-small view-edit-btn">Edit</button>
                                                <button class="btn btn-small delete-btn">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>

            <div id="userModal" class="modal">
                <div class="modal-content">
                    <span class="close-button">&times;</span>
                    <h2 id="modalTitle">Add New Customer</h2>
                    <form id="userForm">
                        <input type="hidden" id="userId" name="user_id">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password">
                            <small id="passwordHelp">Leave blank to keep the current password.</small>
                        </div>
                        <div class="form-group">
                            <label for="retype_password">Retype Password</label>
                            <input type="password" id="retype_password" name="retype_password">
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn modal-save-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    
    <div id="alertModal" class="modal">
        <div class="modal-content" style="max-width: 450px; text-align: center;">
            <span class="close-button">&times;</span>
            <h2 id="alertModalTitle" style="margin-top: 0;"></h2>
            <p id="alertModalMessage"></p>
            <div id="alertModalActions" class="modal-actions" style="justify-content: center;">
                </div>
        </div>
    </div>


    <script src="JS/customer_database.js"></script>
</body>
</html>