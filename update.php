<?php
// Start output buffering to prevent "headers already sent" errors.
ob_start();

session_start(); // Start the session at the very beginning
require_once 'db_connect.php';

// Check if the user is logged in AND is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['is_admin'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in or not admin
    exit;
}

// Replace these with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tavern_publico";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function for logging and soft-deleting
function log_and_soft_delete($conn, $item_id, $item_type) {
    $table_info = [
        'hero_slide' => ['table' => 'hero_slides', 'pk' => 'id'],
        'event' => ['table' => 'events', 'pk' => 'id'],
        'gallery_image' => ['table' => 'gallery', 'pk' => 'id'],
        'menu_item' => ['table' => 'menu', 'pk' => 'id'],
        'team_member' => ['table' => 'team', 'pk' => 'id'],
    ];

    if (!isset($table_info[$item_type])) return false;

    $table = $table_info[$item_type]['table'];
    $pk = $table_info[$item_type]['pk'];

    $sql_select = "SELECT * FROM {$table} WHERE {$pk} = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $item_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $item_data = $result->fetch_assoc();
    $stmt_select->close();

    if ($item_data) {
        $item_data_json = json_encode($item_data);
        
        $conn->begin_transaction();
        try {
            $sql_log = "INSERT INTO deletion_history (item_type, item_id, item_data, purge_date) VALUES (?, ?, ?, DATE_ADD(CURDATE(), INTERVAL 30 DAY))";
            $stmt_log = $conn->prepare($sql_log);
            $stmt_log->bind_param("sis", $item_type, $item_id, $item_data_json);
            $stmt_log->execute();
            $stmt_log->close();

            $sql_soft_delete = "UPDATE {$table} SET deleted_at = NOW() WHERE {$pk} = ?";
            $stmt_soft_delete = $conn->prepare($sql_soft_delete);
            $stmt_soft_delete->bind_param("i", $item_id);
            $stmt_soft_delete->execute();
            $stmt_soft_delete->close();
            
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Soft delete failed: " . $e->getMessage());
            return false;
        }
    }
    return false;
}

// Generates a unique filename to prevent conflicts
function uploadFile($file, $targetDir, $allowedTypes) {
    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid('', true) . '.' . $fileType;
    $targetFile = $targetDir . $newFileName;
    $uploadOk = 1;

    if ($file["size"] > 50000000) { $uploadOk = 0; }
    if(!in_array($fileType, $allowedTypes)) { $uploadOk = 0; }

    if ($uploadOk == 0) {
        return false;
    } else {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $newFileName;
        } else {
            return false;
        }
    }
}

function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, strip_tags($data));
}

// --- Hero Slide Handling ---
if (isset($_POST['add_hero_slide'])) {
    $title = sanitize($conn, $_POST['hero_title']);
    $subtitle = sanitize($conn, $_POST['hero_subtitle']);
    $media_type = sanitize($conn, $_POST['media_type']);
    $image_path = '';
    $video_path = '';

    if ($media_type === 'image' && !empty($_FILES['hero_image']['name'])) {
        $new_filename = uploadFile($_FILES['hero_image'], "uploads/", ['jpg', 'png', 'jpeg', 'gif']);
        if ($new_filename) { $image_path = 'uploads/' . $new_filename; }
    } elseif ($media_type === 'video' && !empty($_FILES['hero_video']['name'])) {
        $new_filename = uploadFile($_FILES['hero_video'], "uploads/", ['mp4', 'webm', 'ogg']);
        if ($new_filename) { $video_path = 'uploads/' . $new_filename; }
    }

    $sql = "INSERT INTO hero_slides (image_path, video_path, title, subtitle, media_type) VALUES ('$image_path', '$video_path', '$title', '$subtitle', '$media_type')";
    if ($conn->query($sql) === TRUE) { $_SESSION['message'] = "New hero slide added successfully."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=hero_section');
    exit;
}

if (isset($_POST['delete_hero_slide'])) {
    $id = (int)$_POST['slide_id'];
    if(log_and_soft_delete($conn, $id, 'hero_slide')) { $_SESSION['message'] = "Hero slide moved to deletion history."; } 
    else { $_SESSION['message'] = "Error deleting hero slide."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=hero_section');
    exit;
}

// --- Event Handling ---
if (isset($_POST['add_event'])) {
    $title = sanitize($conn, $_POST['event_title']);
    $date = sanitize($conn, $_POST['event_date']);
    $end_date = !empty($_POST['event_end_date']) ? "'" . sanitize($conn, $_POST['event_end_date']) . "'" : "NULL";
    $description = sanitize($conn, $_POST['event_description']);
    $image = '';
    if (!empty($_FILES['event_image']['name'])) {
        $new_filename = uploadFile($_FILES['event_image'], "uploads/", ['jpg', 'png', 'jpeg', 'gif']);
        if ($new_filename) { $image = 'uploads/' . $new_filename; }
    }
    $sql = "INSERT INTO events (title, date, end_date, description, image) VALUES ('$title', '$date', $end_date, '$description', '$image')";
    if ($conn->query($sql) === TRUE) { $_SESSION['message'] = "New event added successfully."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=events');
    exit;
}

if (isset($_POST['delete_event'])) {
    $id = (int)$_POST['event_id'];
    if (log_and_soft_delete($conn, $id, 'event')) { $_SESSION['message'] = "Event moved to deletion history."; } 
    else { $_SESSION['message'] = "Error deleting event."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=events');
    exit;
}

// --- Gallery Handling ---
if (isset($_POST['add_gallery_image'])) {
    $description = sanitize($conn, $_POST['gallery_description']);
    if (!empty($_FILES['gallery_image']['name'])) {
        $new_filename = uploadFile($_FILES['gallery_image'], "uploads/", ['jpg', 'png', 'jpeg', 'gif']);
        if ($new_filename) {
            $image = 'uploads/' . $new_filename;
            $sql = "INSERT INTO gallery (image, description) VALUES ('$image', '$description')";
            if ($conn->query($sql) === TRUE) { $_SESSION['message'] = "New gallery image added successfully."; }
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=gallery');
    exit;
}

if (isset($_POST['delete_gallery_image'])) {
    $id = (int)$_POST['gallery_id'];
    if (log_and_soft_delete($conn, $id, 'gallery_image')) { $_SESSION['message'] = "Gallery image moved to deletion history."; } 
    else { $_SESSION['message'] = "Error deleting gallery image."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=gallery');
    exit;
}

// --- Menu Handling ---
if (isset($_POST['add_menu_item'])) {
    $name = sanitize($conn, $_POST['menu_name']);
    $category = sanitize($conn, $_POST['menu_category']);
    $price = sanitize($conn, $_POST['menu_price']);
    $description = sanitize($conn, $_POST['menu_description']);
    $image = '';
    if (!empty($_FILES['menu_image']['name'])) {
        $new_filename = uploadFile($_FILES['menu_image'], "uploads/", ['jpg', 'png', 'jpeg', 'gif']);
        if ($new_filename) { $image = 'uploads/' . $new_filename; }
    }
    $sql = "INSERT INTO menu (name, category, price, description, image) VALUES ('$name', '$category', '$price', '$description', '$image')";
    if ($conn->query($sql) === TRUE) { $_SESSION['message'] = "New menu item added successfully."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=menu');
    exit;
}

if (isset($_POST['delete_menu_item'])) {
    $id = (int)$_POST['menu_id'];
    if (log_and_soft_delete($conn, $id, 'menu_item')) { $_SESSION['message'] = "Menu item moved to deletion history."; } 
    else { $_SESSION['message'] = "Error deleting menu item."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=menu');
    exit;
}

// --- Team Handling ---
if (isset($_POST['add_team_member'])) {
    $name = sanitize($conn, $_POST['team_name']);
    $title = sanitize($conn, $_POST['team_title']);
    $bio = sanitize($conn, $_POST['team_bio']);
    $image = '';
    if (!empty($_FILES['team_image']['name'])) {
        $new_filename = uploadFile($_FILES['team_image'], "uploads/", ['jpg', 'png', 'jpeg', 'gif']);
        if ($new_filename) { $image = 'uploads/' . $new_filename; }
    }
    $sql = "INSERT INTO team (name, title, bio, image) VALUES ('$name', '$title', '$bio', '$image')";
    if ($conn->query($sql) === TRUE) { $_SESSION['message'] = "New team member added successfully."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=team_members');
    exit;
}

if (isset($_POST['delete_team_member'])) {
    $id = (int)$_POST['team_id'];
    if (log_and_soft_delete($conn, $id, 'team_member')) { $_SESSION['message'] = "Team member moved to deletion history."; } 
    else { $_SESSION['message'] = "Error deleting team member."; }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?section=team_members');
    exit;
}

$section = $_GET['section'] ?? 'hero_section';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tavern Publico - Admin Dashboard</title>
    <link rel="stylesheet" href="CSS/admin.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .content-card { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); margin-bottom: 20px; }
        .content-card h2, .content-card h3, .content-card h4 { color: #2c3e50; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 1px solid #eee; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-group input[type="text"], .form-group input[type="date"], .form-group input[type="file"], .form-group textarea, .form-group select, .form-group input[type="number"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; transition: border-color 0.3s ease; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: #3498db; outline: none; }
        .data-item { background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; border-radius: 6px; display: flex; flex-direction: column; gap: 10px; position: relative; transition: box-shadow 0.3s ease; }
        .data-item:hover { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08); }
        .data-item h4 { margin: 0; color: #3498db; }
        .data-item p { margin: 0; color: #666; font-size: 0.9em; }
        .data-item img, .data-item video { border-radius: 4px; max-width: 100%; height: auto; object-fit: cover; }
        button, .btn { display: inline-block; padding: 10px 20px; font-size: 16px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s ease, transform 0.2s ease; }
        button[type="submit"], .btn-primary { background-color: #27ae60; color: #fff; }
        button[type="submit"]:hover, .btn-primary:hover { background-color: #2ecc71; transform: translateY(-2px); }
        .delete-btn { background-color: #e74c3c; color: #fff; font-size: 0.9em; padding: 8px 15px; }
        .delete-btn:hover { background-color: #c0392b; transform: translateY(-2px); }
        .image-grid-admin { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .menu-grid-admin { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .menu-item-admin { background-color: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); display: flex; flex-direction: column; align-items: flex-start; gap: 10px; }
        .menu-item-admin img { align-self: center; }
        .menu-nav { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ccc; }
        .menu-nav-link { padding: 8px 15px; background-color: #3498db; color: #fff; text-decoration: none; border-radius: 20px; font-size: 0.9em; transition: background-color 0.3s ease, transform 0.2s ease; cursor: pointer; }
        .menu-nav-link:hover { background-color: #2980b9; transform: translateY(-2px); }
        .menu-nav-link.active { background-color: #2980b9; font-weight: bold; }
        .message-box { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); padding: 15px 30px; background-color: #4CAF50; color: white; border-radius: 8px; z-index: 1000; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); font-weight: bold; display: none; opacity: 0; transition: opacity 0.5s ease-in-out; }
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.6); justify-content: center; align-items: center; }
        .modal-content { background-color: #fff; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); position: relative; text-align: center; }
        .modal-content h2 { margin-top: 0; font-size: 1.5em; color: #333; }
        .modal-content p { margin-bottom: 25px; color: #555; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }

        .events-grid-admin, .hero-slides-grid-admin { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .events-grid-admin .data-item, .hero-slides-grid-admin .data-item { text-align: left; }
        @media (max-width: 992px) { .events-grid-admin, .hero-slides-grid-admin { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .events-grid-admin, .hero-slides-grid-admin { grid-template-columns: 1fr; } }
        
        .tab-container { display: flex; border-bottom: 2px solid #ccc; margin-bottom: 20px; }
        .tab-link { padding: 10px 20px; cursor: pointer; border: none; background-color: transparent; font-size: 16px; font-weight: 600; color: #555; text-decoration: none; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: color 0.3s, border-bottom-color 0.3s; }
        .tab-link.active { color: #3498db; border-bottom-color: #3498db; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .filter-buttons { margin-bottom: 20px; display: flex; gap: 10px; }
        .filter-btn { background-color: #f0f0f0; border: 1px solid #ddd; border-radius: 20px; padding: 8px 16px; cursor: pointer; }
        .filter-btn.active { background-color: #3498db; color: white; border-color: #3498db; }
    </style>
</head>
<body>

    <div class="page-wrapper">
        <aside class="admin-sidebar">
             <div class="sidebar-header"><img src="Tavern.png" alt="Home Icon" class="home-icon"></div>
            <nav>
                <ul class="sidebar-menu">
                    <li class="menu-item"><a href="admin.php"><i class="material-icons">dashboard</i> Dashboard</a></li>
                     <li class="menu-item active"><a href="update.php"><i class="material-icons">file_upload</i> Upload Management</a></li>
                    <li class="menu-item"><a href="reservation.php"><i class="material-icons">event_note</i> Reservation</a></li>
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
                <div id="message-box" class="message-box"></div>
                <h1 class="dashboard-heading">Content Management</h1>

                <div class="tab-container">
                    <a href="?section=hero_section" class="tab-link <?php echo ($section == 'hero_section') ? 'active' : ''; ?>">Hero Section</a>
                    <a href="?section=team_members" class="tab-link <?php echo ($section == 'team_members') ? 'active' : ''; ?>">Team Members</a>
                    <a href="?section=events" class="tab-link <?php echo ($section == 'events') ? 'active' : ''; ?>">Events</a>
                    <a href="?section=gallery" class="tab-link <?php echo ($section == 'gallery') ? 'active' : ''; ?>">Gallery</a>
                    <a href="?section=menu" class="tab-link <?php echo ($section == 'menu') ? 'active' : ''; ?>">Menu</a>
                </div>

                <div id="hero_section" class="tab-content <?php echo ($section == 'hero_section') ? 'active' : ''; ?>">
                    <section class="content-card">
                        <h2>Hero Section Slides</h2>
                        <h3>Add New Slide</h3>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group" id="hero_text_inputs">
                                <div class="form-group"><label for="hero_title">Title:</label><input type="text" id="hero_title" name="hero_title"></div>
                                <div class="form-group"><label for="hero_subtitle">Subtitle (Optional):</label><input type="text" id="hero_subtitle" name="hero_subtitle"></div>
                            </div>
                            <div class="form-group">
                                <label for="media_type">Media Type:</label>
                                <select id="media_type" name="media_type" required>
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                </select>
                            </div>
                            <div class="form-group" id="hero_image_group"><label for="hero_image">Image:</label><input type="file" id="hero_image" name="hero_image" accept="image/*"></div>
                            <div class="form-group" id="hero_video_group" style="display: none;"><label for="hero_video">Video:</label><input type="file" id="hero_video" name="hero_video" accept="video/*"></div>
                            <button type="submit" name="add_hero_slide">Add Slide</button>
                        </form>
                        <h3>Existing Slides</h3>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">All</button>
                            <button class="filter-btn" data-filter="video">Videos</button>
                            <button class="filter-btn" data-filter="image">Images</button>
                        </div>
                        <div class="hero-slides-grid-admin">
                            <?php
                            $sql = "SELECT * FROM hero_slides WHERE deleted_at IS NULL ORDER BY media_type DESC, id DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<div class='data-item' data-media-type='" . $row['media_type'] . "'>";
                                    if ($row['media_type'] === 'image' && $row['image_path']) {
                                        echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='Hero Slide Image' style='width: 100%; height: 180px; object-fit: cover; margin-bottom: 10px;'>";
                                    } elseif ($row['media_type'] === 'video' && $row['video_path']) {
                                        echo "<video src='" . htmlspecialchars($row['video_path']) . "' controls style='width: 100%; height: 180px; object-fit: cover; margin-bottom: 10px;'></video>";
                                    }
                                    echo "<h4>" . htmlspecialchars($row['title']) . "</h4><p>" . htmlspecialchars($row['subtitle']) . "</p>";
                                    echo "<form action='' method='post' class='delete-form' style='display:inline; margin-top: auto;'><input type='hidden' name='slide_id' value='" . $row['id'] . "'><button type='button' class='delete-btn delete-trigger-btn'>Delete</button><input type='hidden' name='delete_hero_slide' value='1'></form>";
                                    echo "</div>";
                                }
                            } else { echo "<p>No hero slides found.</p>"; }
                            ?>
                        </div>
                    </section>
                </div>
                
                <div id="team_members" class="tab-content <?php echo ($section == 'team_members') ? 'active' : ''; ?>">
                     <section class="content-card">
                        <h2>Team Members</h2>
                        <h3>Add New Team Member</h3>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group"><label for="team_name">Name:</label><input type="text" id="team_name" name="team_name" required></div>
                            <div class="form-group"><label for="team_title">Title / Position:</label><input type="text" id="team_title" name="team_title" required></div>
                            <div class="form-group"><label for="team_bio">Short Bio:</label><textarea id="team_bio" name="team_bio" required></textarea></div>
                            <div class="form-group"><label for="team_image">Image:</label><input type="file" id="team_image" name="team_image" required></div>
                            <button type="submit" name="add_team_member">Add Team Member</button>
                        </form>
                        <h3>Existing Team Members</h3>
                        <div class="data-list image-grid-admin">
                            <?php
                            $sql = "SELECT * FROM team WHERE deleted_at IS NULL ORDER BY id DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<div class='data-item' style='text-align: center;'>";
                                    if ($row['image']) { echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Team Member' style='width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin: 0 auto 10px;'>"; }
                                    echo "<h4>" . htmlspecialchars($row['name']) . "</h4><p><strong>" . htmlspecialchars($row['title']) . "</strong></p><p>" . htmlspecialchars($row['bio']) . "</p>";
                                    echo "<form action='' method='post' class='delete-form' style='margin-top: 10px;'><input type='hidden' name='team_id' value='" . $row['id'] . "'><button type='button' class='delete-btn delete-trigger-btn'>Delete</button><input type='hidden' name='delete_team_member' value='1'></form>";
                                    echo "</div>";
                                }
                            } else { echo "<p>No team members found.</p>"; }
                            ?>
                        </div>
                    </section>
                </div>
                <div id="events" class="tab-content <?php echo ($section == 'events') ? 'active' : ''; ?>">
                    <section class="content-card">
                        <h2>Events</h2>
                        <h3>Add New Event</h3>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group"><label for="event_title">Title:</label><input type="text" id="event_title" name="event_title" required></div>
                            <div class="form-group"><label for="event_date">Start Date:</label><input type="date" id="event_date" name="event_date" min="<?php echo date('Y-m-d'); ?>" required></div>
                            <div class="form-group"><label for="event_end_date">End Date (Optional):</label><input type="date" id="event_end_date" name="event_end_date" min="<?php echo date('Y-m-d'); ?>"></div>
                            <div class="form-group"><label for="event_description">Description:</label><textarea id="event_description" name="event_description" required></textarea></div>
                            <div class="form-group"><label for="event_image">Image:</label><input type="file" id="event_image" name="event_image"></div>
                            <button type="submit" name="add_event">Add Event</button>
                        </form>
                        <h3>Existing Events</h3>
                        <div class="events-grid-admin">
                            <?php
                            $sql = "SELECT * FROM events WHERE deleted_at IS NULL ORDER BY date DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $start_date_formatted = date("d/m/Y", strtotime($row['date']));
                                    $date_display = $start_date_formatted;
                                    if (!empty($row['end_date'])) {
                                        $end_date_formatted = date("d/m/Y", strtotime($row['end_date']));
                                        if ($start_date_formatted !== $end_date_formatted) { $date_display .= " - " . $end_date_formatted; }
                                    }
                                    echo "<div class='data-item'>";
                                    if ($row['image']) { echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Event Image' style='width: 100%; height: 180px; object-fit: cover; margin-bottom: 10px;'>"; }
                                    echo "<h4>" . htmlspecialchars($row['title']) . "</h4><p><strong>Date(s):</strong> " . $date_display . "</p><p>" . htmlspecialchars($row['description']) . "</p>";
                                    echo "<form action='' method='post' class='delete-form' style='display:inline; margin-top: auto;'><input type='hidden' name='event_id' value='" . $row['id'] . "'><button type='button' class='delete-btn delete-trigger-btn'>Delete</button><input type='hidden' name='delete_event' value='1'></form>";
                                    echo "</div>";
                                }
                            } else { echo "<p>No events found.</p>"; }
                            ?>
                        </div>
                    </section>
                </div>
                <div id="gallery" class="tab-content <?php echo ($section == 'gallery') ? 'active' : ''; ?>">
                    <section class="content-card">
                        <h2>Gallery</h2>
                        <h3>Add New Gallery Image</h3>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group"><label for="gallery_image">Image:</label><input type="file" id="gallery_image" name="gallery_image" required></div>
                            <div class="form-group"><label for="gallery_description">Description:</label><textarea id="gallery_description" name="gallery_description" required></textarea></div>
                            <button type="submit" name="add_gallery_image">Add Image</button>
                        </form>
                        <h3>Existing Gallery Images</h3>
                        <div class="data-list image-grid-admin">
                            <?php
                            $sql = "SELECT * FROM gallery WHERE deleted_at IS NULL ORDER BY id DESC";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<div class='data-item' style='flex-direction: column; align-items: center;'>";
                                    echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Gallery Image' style='max-width: 100%; height: auto;'><p>" . htmlspecialchars($row['description']) . "</p>";
                                    echo "<form action='' method='post' class='delete-form' style='display:block; margin-top: 10px;'><input type='hidden' name='gallery_id' value='" . $row['id'] . "'><button type='button' class='delete-btn delete-trigger-btn'>Delete</button><input type='hidden' name='delete_gallery_image' value='1'></form>";
                                    echo "</div>";
                                }
                            } else { echo "<p>No gallery images found.</p>"; }
                            ?>
                        </div>
                    </section>
                </div>

                <div id="menu" class="tab-content <?php echo ($section == 'menu') ? 'active' : ''; ?>">
                    <section class="content-card">
                        <h2>Menu</h2>
                        <h3>Add New Menu Item</h3>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="menu_category">Category:</label>
                                <select id="menu_category" name="menu_category" required>
                                    <option value='Specialty'>Specialty</option>
                                    <option value='Breakfast'>All Day Breakfast</option>
                                    <option value='Lunch'>Ala Carte/For Sharing</option>
                                    <option value='Sizzlers'>Sizzling Plates</option>
                                    <option value='Coffee'>Cafe Drinks</option>
                                    <option value='Cool Creations'>Frappe</option>
                                </select>
                            </div>
                            <div class="form-group"><label for="menu_name">Name:</label><input type="text" id="menu_name" name="menu_name" required></div>
                            <div class="form-group"><label for="menu_description">Description:</label><textarea id="menu_description" name="menu_description" required></textarea></div>
                            <div class="form-group"><label for="menu_price">Price:</label><input type="number" id="menu_price" name="menu_price" step="0.01" min="0" required></div>
                            <div class="form-group"><label for="menu_image">Image:</label><input type="file" id="menu_image" name="menu_image"></div>
                            <button type="submit" name="add_menu_item">Add Menu Item</button>
                        </form>
                        
                        <h3>Existing Menu Items</h3>
                        <nav class="menu-nav">
                            <a href="#" class="menu-nav-link" data-category="all">View All</a>
                            <a href="#" class="menu-nav-link" data-category="Specialty">Specialty</a>
                            <a href="#" class="menu-nav-link" data-category="Breakfast">All Day Breakfast</a>
                            <a href="#" class="menu-nav-link" data-category="Lunch">Ala Carte/For Sharing</a>
                            <a href="#" class="menu-nav-link" data-category="Sizzlers">Sizzling Plates</a>
                            <a href="#" class="menu-nav-link" data-category="Coffee">Cafe Drinks</a>
                            <a href="#" class="menu-nav-link" data-category="Cool Creations">Frappe</a>
                        </nav>

                        <div class="menu-container">
                            <?php
                            $sql_menu = "SELECT * FROM menu WHERE deleted_at IS NULL ORDER BY category, name ASC";
                            $result_menu = $conn->query($sql_menu);
                            if ($result_menu->num_rows > 0) {
                                $menu_items_by_category = [];
                                while($row_menu = $result_menu->fetch_assoc()) {
                                    $menu_items_by_category[$row_menu['category']][] = $row_menu;
                                }

                                foreach ($menu_items_by_category as $category => $items) {
                                    echo "<div class='category-items-wrapper' data-category='" . htmlspecialchars($category) . "'>";
                                    echo "<h4>" . htmlspecialchars($category) . "</h4>";
                                    echo "<div class='menu-grid-admin'>";
                                    foreach ($items as $row) {
                                        echo "<div class='data-item menu-item-admin'>";
                                        echo "<h4>" . htmlspecialchars($row['name']) . "</h4>";
                                        if ($row['image']) { echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Menu Image' style='width: 100%; height: 200px; object-fit: cover;'>"; }
                                        echo "<p><strong>Price:</strong> ₱" . number_format($row['price'], 2) . "</p>";
                                        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                                        echo "<form action='' method='post' class='delete-form' style='display:block; margin-top: auto;'><input type='hidden' name='menu_id' value='" . $row['id'] . "'><button type='button' class='delete-btn delete-trigger-btn'>Delete</button><input type='hidden' name='delete_menu_item' value='1'></form>";
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<p>No menu items found.</p>";
                            }
                            ?>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
    
    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to move this item to the deletion history? It can be restored later.</p>
            <div class="modal-actions">
                <button type="button" class="btn" id="cancelDeleteBtn" style="background-color: #6c757d; color: white;">Cancel</button>
                <button type="button" class="btn delete-btn" id="confirmDeleteBtn">Yes, Delete</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mediaTypeSelect = document.getElementById('media_type');
            const heroImageGroup = document.getElementById('hero_image_group');
            const heroVideoGroup = document.getElementById('hero_video_group');
            const heroTextInputs = document.getElementById('hero_text_inputs');
            
            if (mediaTypeSelect) {
                mediaTypeSelect.addEventListener('change', () => {
                    if (mediaTypeSelect.value === 'image') {
                        heroImageGroup.style.display = 'block';
                        heroVideoGroup.style.display = 'none';
                        heroTextInputs.style.display = 'block';
                    } else {
                        heroImageGroup.style.display = 'none';
                        heroVideoGroup.style.display = 'block';
                        heroTextInputs.style.display = 'none';
                    }
                });
            }

            const confirmDeleteModal = document.getElementById('confirmDeleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const deleteTriggerButtons = document.querySelectorAll('.delete-trigger-btn');
            let formToSubmit = null;

            deleteTriggerButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    formToSubmit = e.target.closest('form');
                    if (confirmDeleteModal) confirmDeleteModal.style.display = 'flex';
                });
            });

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', () => {
                    if (formToSubmit) formToSubmit.submit();
                });
            }

            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', () => {
                    confirmDeleteModal.style.display = 'none';
                    formToSubmit = null;
                });
            }

            window.addEventListener('click', (event) => {
                if (event.target == confirmDeleteModal) {
                    confirmDeleteModal.style.display = 'none';
                    formToSubmit = null;
                }
            });

            const messageBox = document.getElementById('message-box');
            <?php
            if (isset($_SESSION['message'])) {
                echo "if(messageBox) { messageBox.textContent = '{$_SESSION['message']}'; messageBox.style.display = 'block'; setTimeout(() => { messageBox.style.opacity = '1'; }, 10); setTimeout(() => { messageBox.style.opacity = '0'; }, 3000); setTimeout(() => { messageBox.style.display = 'none'; }, 3500); }";
                unset($_SESSION['message']);
            }
            ?>

            // --- HERO SLIDE FILTERING LOGIC ---
            const filterButtons = document.querySelectorAll('.filter-btn');
            const slides = document.querySelectorAll('.hero-slides-grid-admin .data-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    const filter = button.dataset.filter;
                    slides.forEach(slide => {
                        if (filter === 'all' || slide.dataset.mediaType === filter) {
                            slide.style.display = 'flex';
                        } else {
                            slide.style.display = 'none';
                        }
                    });
                });
            });

            // --- BUG FIX: MENU CATEGORY FILTERING ---
            const menuNavLinks = document.querySelectorAll('.menu-nav-link');
            const categoryWrappers = document.querySelectorAll('.category-items-wrapper');

            menuNavLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    menuNavLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                    
                    const selectedCategory = link.dataset.category;

                    categoryWrappers.forEach(wrapper => {
                        if (selectedCategory === 'all' || wrapper.dataset.category === selectedCategory) {
                            wrapper.style.display = 'block';
                        } else {
                            wrapper.style.display = 'none';
                        }
                    });
                });
            });
            
            // Trigger a click on the "View All" button on page load if we are on the menu section
            const activeMenuTab = document.querySelector('.tab-link[href="?section=menu"].active');
            if (activeMenuTab) {
                 document.querySelector('.menu-nav-link[data-category="all"]').click();
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
ob_end_flush();
?>