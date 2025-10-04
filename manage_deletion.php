<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Invalid request.'];

// Helper function to get table name and primary key based on item type
function get_table_info($type) {
    $map = [
        'user' => ['name' => 'users', 'pk' => 'user_id'],
        'reservation' => ['name' => 'reservations', 'pk' => 'reservation_id'],
        'menu_item' => ['name' => 'menu', 'pk' => 'id'],
        'gallery_image' => ['name' => 'gallery', 'pk' => 'id'],
        'event' => ['name' => 'events', 'pk' => 'id'],
        'team_member' => ['name' => 'team', 'pk' => 'id'],
        'hero_slide' => ['name' => 'hero_slides', 'pk' => 'id'],
        'contact_message' => ['name' => 'contact_messages', 'pk' => 'id'],
        'testimonial' => ['name' => 'testimonials', 'pk' => 'id'],
        'blocked_date' => ['name' => 'blocked_dates', 'pk' => 'id']
    ];
    return $map[$type] ?? null;
}


if (!isset($_SESSION['loggedin']) || !$_SESSION['is_admin']) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log_id = filter_input(INPUT_POST, 'log_id', FILTER_SANITIZE_NUMBER_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if (!$log_id || !$action) {
        echo json_encode($response);
        exit;
    }

    // Get the log details including the item_data
    $log_sql = "SELECT item_type, item_id, item_data FROM deletion_history WHERE log_id = ?";
    $stmt = mysqli_prepare($link, $log_sql);
    mysqli_stmt_bind_param($stmt, "i", $log_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $log = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$log) {
        $response['message'] = 'Log entry not found.';
        echo json_encode($response);
        exit;
    }

    $item_type = $log['item_type'];
    $item_id = $log['item_id'];
    $table_info = get_table_info($item_type);

    if (!$table_info) {
        $response['message'] = 'Invalid item type specified in log.';
        echo json_encode($response);
        exit;
    }
    
    // Begin a transaction to ensure all database changes succeed or fail together
    mysqli_begin_transaction($link);

    try {
        if ($action === 'restore') {
            if ($item_type === 'blocked_date') {
                $data = json_decode($log['item_data'], true);
                $date_to_restore = $data['block_date'];
                $restore_sql = "INSERT IGNORE INTO blocked_dates (id, block_date) VALUES (?, ?)";
                $stmt_restore = mysqli_prepare($link, $restore_sql);
                mysqli_stmt_bind_param($stmt_restore, "is", $item_id, $date_to_restore);
                mysqli_stmt_execute($stmt_restore);
                mysqli_stmt_close($stmt_restore);
            } else {
                $restore_sql = "UPDATE {$table_info['name']} SET deleted_at = NULL WHERE {$table_info['pk']} = ?";
                $stmt_restore = mysqli_prepare($link, $restore_sql);
                mysqli_stmt_bind_param($stmt_restore, "i", $item_id);
                mysqli_stmt_execute($stmt_restore);
                mysqli_stmt_close($stmt_restore);
            }
            $response['message'] = 'Item restored successfully.';
        } elseif ($action === 'purge') {
            $purge_sql = "DELETE FROM {$table_info['name']} WHERE {$table_info['pk']} = ?";
            $stmt_purge = mysqli_prepare($link, $purge_sql);
            mysqli_stmt_bind_param($stmt_purge, "i", $item_id);
            mysqli_stmt_execute($stmt_purge);
            mysqli_stmt_close($stmt_purge);
            $response['message'] = 'Item permanently deleted.';
        }

        // For both actions, if successful, delete the log entry
        $delete_log_sql = "DELETE FROM deletion_history WHERE log_id = ?";
        $stmt_log = mysqli_prepare($link, $delete_log_sql);
        mysqli_stmt_bind_param($stmt_log, "i", $log_id);
        mysqli_stmt_execute($stmt_log);
        mysqli_stmt_close($stmt_log);
        
        mysqli_commit($link);
        $response['success'] = true;

    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($link); 
        $response['message'] = 'A database error occurred during the operation.';
        error_log("Deletion management failed: " . $exception->getMessage());
    }

    mysqli_close($link);
}

echo json_encode($response);
?>
