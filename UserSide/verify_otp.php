<?php
session_start();
require 'db_connect.php';

// If no pending OTP exists → redirect back
if (!isset($_SESSION['otp'], $_SESSION['otp_expires'], $_SESSION['pending_user'])) {
    header("Location: HomepageUser.php");
    exit();
}

// Sanitize input
$entered_otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';

if (!preg_match('/^[0-9]{6}$/', $entered_otp)) {
    $_SESSION['otp_error'] = "Invalid OTP format.";
    header("Location: otp_verify.php");
    exit();
}

// Check expiration: OTP valid for 5 minutes
if (time() > $_SESSION['otp_expires']) {
    unset($_SESSION['otp'], $_SESSION['pending_user'], $_SESSION['otp_expires']);
    $_SESSION['otp_error'] = "OTP expired. Please request a new one.";
    header("Location: otp_verify.php");
    exit();
}

// Initialize attempt tracking
if (!isset($_SESSION['otp_attempts'])) {
    $_SESSION['otp_attempts'] = 0;
}

$_SESSION['otp_attempts']++;

// Check attempt limit (max 5 tries)
if ($_SESSION['otp_attempts'] > 5) {
    unset($_SESSION['otp'], $_SESSION['pending_user'], $_SESSION['otp_expires'], $_SESSION['otp_attempts']);
    $_SESSION['otp_error'] = "Too many attempts. Please request a new OTP.";
    header("Location: otp_verify.php");
    exit();
}

// Compare OTP
if ($entered_otp == $_SESSION['otp']) {

    $data = $_SESSION['pending_user'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, birthdate, contact_num) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", 
        $data['username'], 
        $data['email'], 
        $data['password'], 
        $data['birthdate'], 
        $data['contact_num']
    );
    $stmt->execute();
    $stmt->close();

    // Cleanup everything
    unset($_SESSION['otp'], $_SESSION['pending_user'], $_SESSION['otp_expires'], $_SESSION['otp_attempts']);

    header("Location: HomepageUser.php?verified=1");
    exit();

} else {
    $_SESSION['otp_error'] = "Incorrect OTP. Please try again.";
    header("Location: otp_verify.php");
    exit();
}

?>
