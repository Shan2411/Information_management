<?php
session_start();
require 'includes/db_connect.php';

if (!isset($_SESSION['pending_staff'])) {
    header("Location: AddStaff.php");
    exit;
}

$entered = $_POST['otp_input'];
$correct = $_SESSION['otp'] ?? null;

if ($entered == $correct) {
    $data = $_SESSION['pending_staff'];

    $stmt = $conn->prepare("INSERT INTO staff (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $data['name'], $data['email'], $data['password']);
    $stmt->execute();

    // clear otp session data
    unset($_SESSION['pending_staff'], $_SESSION['otp'], $_SESSION['otp_expires'], $_SESSION['otp_attempts']);

    header("Location: AdminStaff.php?success=added");
    exit;
} else {
    header("Location: verify_staff_otp.php?error=invalid_otp");
    exit;
}