<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

// --- Sorting Logic (remains the same) ---
$sort_by = $_GET['sort'] ?? 'deleted_at';
$sort_order = $_GET['order'] ?? 'DESC';
$allowed_sort_columns = ['deleted_at', 'purge_date']; // Item_type is now a filter, not a sort column
if (!in_array($sort_by, $allowed_sort_columns)) {
    $sort_by = 'deleted_at';
}
if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
    $sort_order = 'DESC';
}

// --- Fetching Logic ---
$deleted_items = [];
// Get all unique item types for the filter tabs
$item_types_result = mysqli_query($link, "SELECT DISTINCT item_type FROM deletion_history ORDER BY item_type ASC");
$item_types = [];
while($row = mysqli_fetch_assoc($item_types_result)) {
    $item_types[] = $row['item_type'];
}

$sql = "SELECT log_id, item_type, item_id, item_data, deleted_at, purge_date FROM deletion_history ORDER BY $sort_by $sort_order";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $deleted_items[] = $row;
    }
    mysqli_free_result($result);
} else {
    error_log("Deletion History page error: " . mysqli_error($link));
}
mysqli_close($link);

function get_sort_href($column, $current_sort, $current_order) {
    $order = ($current_sort === $column && $current_order === 'ASC') ? 'DESC' : 'ASC';
    return "?sort=$column&order=$order";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tavern Publico - Deletion History</title>
    <link rel="stylesheet" href="CSS/admin.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Tab styling for filtering */
        .tab-container { 
            display: flex; 
            flex-wrap: wrap;
            border-bottom: 2px solid #ccc; 
            margin-bottom: 20px; 
        }
        .tab-link {
            padding: 10px 20px; 
            cursor: pointer; 
            border: none; 
            background-color: transparent; 
            font-size: 16px;
            font-weight: 600; 
            color: #555; 
            text-decoration: none; 
            border-bottom: 3px solid transparent;
            margin-bottom: -2px; 
            transition: color 0.3s, border-bottom-color 0.3s, background-color 0.3s;
        }
        .tab-link:hover {
             background-color: #f0f2f5;
        }
        .tab-link.active { 
            color: #007bff; 
            border-bottom-color: #007bff;
            background-color: #e9ecef;
        }
        .sort-link { color: #555; text-decoration: none; display: inline-flex; align-items: center; }
        .sort-link:hover { color: #007bff; }
        .sort-link .material-icons { font-size: 16px; margin-left: 4px; }
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
                    <li class="menu-item"><a href="reservation.php"><i class="material-icons">event_note</i> Reservation</a></li>
                </ul>
                <div class="user-management-title">User Management</div>
                <ul class="sidebar-menu user-management-menu">
                     <li class="menu-item"><a href="notification_control.php"><i class="material-icons">notifications</i> Notification Control</a></li>
                    <li class="menu-item"><a href="table_management.php"><i class="material-icons">table_chart</i>Calendar Management</a></li>
                    <li class="menu-item"><a href="customer_database.php"><i class="material-icons">people</i> Customer Database</a></li>
                    <li class="menu-item"><a href="reports.php"><i class="material-icons">analytics</i>Reservation Reports</a></li>
                    <li class="menu-item active"><a href="deletion_history.php"><i class="material-icons">history</i>Archive</a></li>
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
                            <span class="admin-role">Admin</span>
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
                    <h1>Deletion History</h1>
                    <input type="text" id="historySearch" class="search-input" placeholder="Search deleted items...">
                </div>

                <div class="tab-container">
                    <a href="#" class="tab-link active" data-filter="all">All Items</a>
                    <?php foreach($item_types as $type): ?>
                        <a href="#" class="tab-link" data-filter="<?= htmlspecialchars($type) ?>">
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $type))) ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <section class="all-reservations-section">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ITEM TYPE</th>
                                    <th>ITEM DETAILS</th>
                                    <th>
                                        <a href="<?= get_sort_href('deleted_at', $sort_by, $sort_order) ?>" class="sort-link">
                                            Deleted At <i class="material-icons">sort</i>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?= get_sort_href('purge_date', $sort_by, $sort_order) ?>" class="sort-link">
                                            Purge Date <i class="material-icons">sort</i>
                                        </a>
                                    </th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <?php if (empty($deleted_items)): ?>
                                    <tr><td colspan="5" style="text-align: center;">No deleted items found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($deleted_items as $item): 
                                        $item_data = json_decode($item['item_data'], true);
                                        $details = 'ID: ' . htmlspecialchars($item['item_id']);
                                        if (is_array($item_data)) {
                                            if (isset($item_data['username'])) $details = "User: " . htmlspecialchars($item_data['username']);
                                            elseif (isset($item_data['res_name'])) $details = "Reservation: " . htmlspecialchars($item_data['res_name']);
                                            elseif (isset($item_data['name'])) $details = "Name: " . htmlspecialchars($item_data['name']);
                                            elseif (isset($item_data['title'])) $details = "Title: " . htmlspecialchars($item_data['title']);
                                            elseif (isset($item_data['subject'])) $details = "Subject: " . htmlspecialchars($item_data['subject']);
                                            elseif (isset($item_data['block_date'])) $details = "Date: " . htmlspecialchars($item_data['block_date']);
                                        }
                                    ?>
                                        <tr data-log-id="<?= $item['log_id']; ?>" data-item-type="<?= htmlspecialchars($item['item_type']); ?>">
                                            <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $item['item_type']))); ?></td>
                                            <td><?= $details; ?></td>
                                            <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($item['deleted_at']))); ?></td>
                                            <td><?= htmlspecialchars($item['purge_date']); ?></td>
                                            <td class="actions">
                                                <button class="btn btn-small restore-btn" style="background-color: #28a745;">Restore</button>
                                                <button class="btn btn-small purge-btn" style="background-color: #dc3545;">Delete Permanently</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <div id="alertModal" class="modal">
        <div class="modal-content" style="max-width: 450px; text-align: center;">
            <span class="close-button">&times;</span>
            <h2 id="alertModalTitle" style="margin-top: 0;"></h2>
            <p id="alertModalMessage"></p>
            <div id="alertModalActions" class="modal-actions" style="justify-content: center;"></div>
        </div>
    </div>

    <script src="JS/deletion_history.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterTabs = document.querySelectorAll('.tab-link');
            const tableRows = document.querySelectorAll('#historyTableBody tr');

            filterTabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Update active tab style
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const filterValue = this.getAttribute('data-filter');

                    // Show/hide rows based on filter
                    tableRows.forEach(row => {
                        if (filterValue === 'all' || row.getAttribute('data-item-type') === filterValue) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>