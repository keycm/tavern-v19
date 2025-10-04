<?php
require_once 'db_connect.php'; // Your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['contactName'] ?? '');
    $email = trim($_POST['contactEmail'] ?? '');
    $subject = trim($_POST['contactSubject'] ?? '');
    $message = trim($_POST['contactMessage'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        // Handle error - redirect back to contact page with an error message
        header("Location: contact.php?status=error");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Handle invalid email format
        header("Location: contact.php?status=invalid_email");
        exit;
    }

    // Block emails with only numbers before the @
    $email_parts = explode('@', $email);
    $local_part = $email_parts[0];
    if (is_numeric($local_part)) {
        header("Location: contact.php?status=invalid_email_numeric");
        exit;
    }

    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);

        if (mysqli_stmt_execute($stmt)) {
            // Success
            header("Location: contact.php?status=success");
            exit;
        } else {
            // Database execution error
            header("Location: contact.php?status=db_error");
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        // Database prepare error
        header("Location: contact.php?status=db_error");
        exit;
    }

    mysqli_close($link);
} else {
    // Not a POST request
    header("Location: contact.php");
    exit;
}
?>