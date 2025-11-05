<?php
session_start();

// Optional: Redirect if no OTP was sent
if (!isset($_SESSION['otp'])) {
    header("Location: HomepageUser.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-2xl shadow-lg w-[380px] text-center border border-gray-200">
    
    <h2 class="text-2xl font-bold text-[rgb(116,142,159)] mb-3">
        Email Verification
    </h2>

    <p class="text-gray-600 text-sm mb-6">
        We sent a 6-digit verification code to your email.<br>
        Please enter it below to activate your account.
    </p>

    <form action="verify_otp.php" method="POST" class="space-y-4">
        <input type="text" maxlength="6" name="otp" placeholder="Enter OTP Code"
            class="w-full text-center tracking-widest text-lg font-semibold border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]"
            required>

        <button type="submit"
            class="w-full bg-[rgb(116,142,159)] text-white py-2.5 rounded-lg font-medium hover:bg-[rgb(96,122,139)] transition">
            Verify Code
        </button>
    </form>

    <form action="send_otp.php" method="POST" class="mt-3">
        <!-- Re-send hidden values -->
        <button class="text-sm text-[rgb(116,142,159)] hover:underline" type="submit">Resend Code</button>
    </form>

    <p class="text-xs text-gray-500 mt-4">
        Having trouble? Check your spam folder.
    </p>

</div>

</body>
</html>
