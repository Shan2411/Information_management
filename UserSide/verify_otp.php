<?php
session_start();
require 'db_connect.php';

if ($_POST['otp'] == $_SESSION['otp']) {
    $data = $_SESSION['pending_user'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, birthdate, contact_num) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $data['username'], $data['email'], $data['password'], $data['birthdate'], $data['contact_num']);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['otp'], $_SESSION['pending_user']);

    echo "✅ Account Verified! You can now log in.";
    echo '<br><a href="HomepageUser.php">Return to Login</a>';
} else {
    echo "❌ Incorrect OTP. Try Again.";
}
