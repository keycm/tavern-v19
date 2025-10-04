<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the database connection file and the new mail config
require_once 'db_connect.php';
require_once 'mail_config.php';

// Manually include the PHPMailer files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- Validation (No changes here) ---
    if (empty($username) || empty($email) || empty($password)) {
        $response['message'] = 'Please fill in all fields.';
        echo json_encode($response);
        exit;
    }
     if (!filter_var($email, FILTER_VALIDATE_EMAIL) || substr($email, -10) !== '@gmail.com') {
        $response['message'] = 'Invalid email format or not a Gmail address.';
        echo json_encode($response);
        exit;
    }
    if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $response['message'] = 'Password does not meet the requirements.';
        echo json_encode($response);
        exit;
    }


    // Check if username or email already exists among active users
    $sql_check = "SELECT user_id FROM users WHERE (username = ? OR email = ?) AND deleted_at IS NULL";
    if ($stmt_check = mysqli_prepare($link, $sql_check)) {
        mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
        if (mysqli_stmt_execute($stmt_check)) {
            mysqli_stmt_store_result($stmt_check);
            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $response['message'] = 'Username or Email already taken.';
                echo json_encode($response);
                exit;
            }
        }
        mysqli_stmt_close($stmt_check);
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $verification_token = bin2hex(random_bytes(50));

    // Insert user into database with verification token
    $sql_insert = "INSERT INTO users (username, email, password_hash, is_verified, verification_token) VALUES (?, ?, ?, 0, ?)";
    if ($stmt_insert = mysqli_prepare($link, $sql_insert)) {
        mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $email, $password_hash, $verification_token);

        if (mysqli_stmt_execute($stmt_insert)) {
            $user_id = mysqli_insert_id($link);

            // --- Send Verification Email using PHPMailer ---
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_SECURE;
                $mail->Port       = SMTP_PORT;

                //Recipients
                $mail->setFrom(SMTP_USERNAME, 'Tavern Publico');
                $mail->addAddress($email, $username); // Add a recipient

                // Content
                $verification_link = "http://localhost/Original%20Tavern%20Publico/verify.php?token=" . $verification_token;
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Account - Tavern Publico';
                $mail->Body    = "<h1>Welcome to Tavern Publico!</h1><p>Thank you for registering. Please click the link below to verify your account:</p><a href='$verification_link'>Verify My Account</a>";
                $mail->AltBody = "Thank you for registering. Please copy and paste this link to verify your account: " . $verification_link;

                $mail->send();

                $response['success'] = true;
                $response['message'] = 'Registration successful! A verification link has been sent to your Gmail address. Please check your inbox.';

            } catch (Exception $e) {
                $response['message'] = "Registration was successful, but the verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            $response['message'] = 'Registration failed. Please try again.';
        }
        mysqli_stmt_close($stmt_insert);
    } else {
        $response['message'] = 'Database error: Could not prepare statement.';
    }
    mysqli_close($link);
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>