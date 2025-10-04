<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Invalid request.'];

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

    // Get the log details
    $log_sql = "SELECT item_type, item_id FROM deletion_history WHERE log_id = ?";
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
        $response['message'] = 'Invalid item type.';
        echo json_encode($response);
        exit;
    }

    if ($action === 'restore') {
        // Restore: Set deleted_at to NULL in the original table
        $restore_sql = "UPDATE {$table_info['name']} SET deleted_at = NULL WHERE {$table_info['pk']} = ?";
        $stmt_restore = mysqli_prepare($link, $restore_sql);
        mysqli_stmt_bind_param($stmt_restore, "i", $item_id);
        
        if (mysqli_stmt_execute($stmt_restore)) {
            // Delete the log from history
            $delete_log_sql = "DELETE FROM deletion_history WHERE log_id = ?";
            $stmt_log = mysqli_prepare($link, $delete_log_sql);
            mysqli_stmt_bind_param($stmt_log, "i", $log_id);
            mysqli_stmt_execute($stmt_log);
            mysqli_stmt_close($stmt_log);
            
            $response['success'] = true;
            $response['message'] = 'Item restored successfully.';
        } else {
            $response['message'] = 'Failed to restore item.';
        }
        mysqli_stmt_close($stmt_restore);

    } elseif ($action === 'purge') {
        // Purge: Permanently delete from the original table
        // For blocked_date, we delete from the main table, not a soft-deleted one.
        if ($item_type === 'blocked_date') {
            $purge_sql = "DELETE FROM {$table_info['name']} WHERE {$table_info['pk']} = ?";
        } else {
            $purge_sql = "DELETE FROM {$table_info['name']} WHERE {$table_info['pk']} = ? AND deleted_at IS NOT NULL";
        }
        
        $stmt_purge = mysqli_prepare($link, $purge_sql);
        mysqli_stmt_bind_param($stmt_purge, "i", $item_id);

        if (mysqli_stmt_execute($stmt_purge)) {
             // Delete the log from history
            $delete_log_sql = "DELETE FROM deletion_history WHERE log_id = ?";
            $stmt_log = mysqli_prepare($link, $delete_log_sql);
            mysqli_stmt_bind_param($stmt_log, "i", $log_id);
            mysqli_stmt_execute($stmt_log);
            mysqli_stmt_close($stmt_log);

            $response['success'] = true;
            $response['message'] = 'Item permanently deleted.';
        } else {
            $response['message'] = 'Failed to permanently delete item.';
        }
        mysqli_stmt_close($stmt_purge);
    }

    mysqli_close($link);
}

echo json_encode($response);

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
?>