<?php
session_start(); // Start the session
require_once 'db_connect.php'; // Include your database connection

header('Content-Type: application/json'); // Set header to return JSON response

$response = ['success' => false, 'message' => ''];

// Basic authentication check: Only logged-in admin can update status
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['is_admin'] !== true) {
    $response['message'] = 'Unauthorized access. Please log in as an administrator.';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Expect reservation_id instead of index
    $reservation_id = $_POST['reservation_id'] ?? null;
    $newStatus = $_POST['status'] ?? null;
    $action = $_POST['action'] ?? null; // To differentiate between status update and delete

    if ($reservation_id === null || ($action === 'update' && $newStatus === null) || empty($action)) {
        $response['message'] = 'Missing reservation ID, action, or new status for update.';
        echo json_encode($response);
        exit;
    }

    if ($action === 'update') {
        // Update reservation status
        $sql = "UPDATE reservations SET status = ? WHERE reservation_id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $newStatus, $reservation_id);

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Reservation status updated successfully.';
                } else {
                    $response['message'] = 'No reservation found with the given ID or status is the same.';
                }
            } else {
                $response['message'] = 'Database error: Could not update reservation status.';
                error_log("Update reservation error: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Database error: Could not prepare statement for update.';
            error_log("Prepare update statement error: " . mysqli_error($link));
        }
    } elseif ($action === 'delete') {
        // Delete reservation
        $sql = "DELETE FROM reservations WHERE reservation_id = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            $param_id = $reservation_id;

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Reservation deleted successfully.';
                } else {
                    $response['message'] = 'No reservation found with the given ID.';
                }
            } else {
                $response['message'] = 'Database error: Could not delete reservation.';
                error_log("Delete reservation error: " . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            $response['message'] = 'Database error: Could not prepare statement for delete.';
            error_log("Prepare delete statement error: " . mysqli_error($link));
        }
    } else {
        $response['message'] = 'Invalid action specified.';
    }

    mysqli_close($link);
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>