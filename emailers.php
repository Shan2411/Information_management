<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'finaldefenseproject22@gmail.com'; // your Gmail address
    $mail->Password   = 'imzr hgrg esdx qcty';    // your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Email info
    $mail->setFrom('your_email@gmail.com', 'test');
    $mail->addAddress('recipient@example.com', 'User');
    $mail->Subject = 'test';
    $mail->Body    = 'test';

    $mail->send();
    echo '✅ Message has been sent';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
