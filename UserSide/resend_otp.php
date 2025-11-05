<?php
session_start();
require 'db_connect.php';

// Make sure we still have pending user data
if (!isset($_SESSION['pending_user'])) {
    header("Location: HomepageUser.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$user = $_SESSION['pending_user'];
$email = $user['email'];
$username = $user['username'];

// Generate a new OTP
$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_expires'] = time() + (60 * 5); // reset expiry
$_SESSION['otp_attempts'] = 0;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finaldefenseproject22@gmail.com';
    $mail->Password = 'imzr hgrg esdx qcty'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('finaldefenseproject22@gmail.com', 'Electronic Device Market');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'New Verification Code';

    $mail->Body = "
    <p>Hello <b>$username</b>,</p>
    <p>Your new verification code is:</p>
    <h2 style='color:#4A90E2;'>$otp</h2>
    <p>This code will expire in 5 minutes.</p>";

    $mail->send();
    $_SESSION['otp_error'] = "A new OTP has been sent!";
    
} catch (Exception $e) {
    $_SESSION['otp_error'] = "Failed to resend OTP. Try again later.";
}

header("Location: otp_verify.php");
exit();
