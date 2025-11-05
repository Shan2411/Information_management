<?php
session_start();
require 'includes/db_connect.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Get form data from AddStaff.php
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Validate
if (empty($name) || empty($email) || empty($password)) {
    header("Location: verify_staff_otp.php");
    exit;
}

// Check Email Exists
$check = $conn->prepare("SELECT * FROM staff WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    header("Location: AddStaff.php?error=email_exists");
    exit;
}

// Hash password now (but do not insert yet)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Generate OTP
$otp = rand(100000, 999999);

// Store data + OTP temporarily
$_SESSION['pending_staff'] = [
    'name' => $name,
    'email' => $email,
    'password' => $hashed_password
];
$_SESSION['otp'] = $otp;
$_SESSION['otp_expires'] = time() + (60 * 5); // OTP lasts 5 mins
$_SESSION['otp_attempts'] = 0;


$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'finaldefenseproject22@gmail.com';
    $mail->Password = 'imzr hgrg esdx qcty'; // APP Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('finaldefenseproject22@gmail.com', 'Device Market');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Staff Account Email Verification Code';

    $mail->Body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; line-height: 1.6;'>
        <h2 style='color:#4A90E2;'>Verify Staff Account</h2>
        <p>Hello <b>$name</b>,</p>

        <p>You have been added as a staff member at <b>Electronic Device Market</b>. 
        To activate your account, please verify your email using the code below:</p>

        <div style='font-size: 24px; font-weight: bold; margin: 20px 0; padding: 10px 20px; border: 2px dashed #4A90E2; display: inline-block;'>
            $otp
        </div>

        <p>This code expires in <b>5 minutes</b>. Do not share it with anyone.</p>

        <p>If this was not you, contact an administrator immediately.</p>

        <br>
        <p>Best regards,<br><b>Device Market Admin Team</b></p>
    </div>
    ";

    $mail->send();
    header("Location: verify_staff_otp.php");
    exit;

} catch (Exception $e) {
    header("Location: AddStaff.php?error=mail_failed");
    exit;
}
