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
$_SESSION['otp_expires'] = time() + (60 * 5); // OTP valid 5 minutes
$_SESSION['otp_attempts'] = 0;


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
    $mail->isHTML(true);
    $mail->Subject = 'Your Email Verification Code';

    $mail->Body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; line-height: 1.6;'>
        <h2 style='color:#4A90E2;'>Email Verification Required</h2>
        <p>Hi <b>$username</b>,</p>

        <p>Thank you for registering at <b>Electronic Device Market</b>! 
        To complete your account setup and keep your information secure, please verify your email address using the code below:</p>

        <div style='font-size: 24px; font-weight: bold; margin: 20px 0; padding: 10px 20px; border: 2px dashed #4A90E2; display: inline-block;'>
            $otp
        </div>

        <p>This code will expire in <b>5 minutes</b>. Do not share it with anyone.</p>

        <p>If you did not create this account, you can safely ignore this email.</p>

        <br>
        <p>Best regards,<br><b>Electronic Device Market Team</b></p>
    </div>
    ";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'otp_sent']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Email sending failed: '.$mail->ErrorInfo]);
}
