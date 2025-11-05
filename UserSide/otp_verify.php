<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head><title>Verify OTP</title></head>
<body>
<h2>Enter OTP sent to your email</h2>
<form action="verify_otp.php" method="POST">
    <input type="text" name="otp" placeholder="6-digit OTP" required>
    <button type="submit">Verify</button>
</form>
</body>
</html>
