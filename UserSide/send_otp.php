<?php
session_start();
require 'db_connect.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$birthdate = $_POST['birthdate'];
$contact_num = $_POST['contact_num'];

if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

// Hash password now (but don't insert yet!)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Generate OTP
$otp = rand(100000, 999999);

// Store data + OTP temporarily
$_SESSION['pending_user'] = [
    'username' => $username,
    'email' => $email,
    'password' => $hashedPassword,
    'birthdate' => $birthdate,
    'contact_num' => $contact_num
];
$_SESSION['otp'] = $otp;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finaldefenseproject22@gmail.com';
    $mail->Password = 'imzr hgrg esdx qcty'; // app password only
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('finaldefenseproject22@gmail.com', 'Electronic Device Market');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body = "Your 6-digit OTP is: <b>$otp</b>";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'otp_sent']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Email sending failed: '.$mail->ErrorInfo]);
}
