<?php
session_start();
$error_message = "";
if (isset($_GET['error']) && $_GET['error'] === 'invalid_otp') {
    $error_message = "Invalid OTP. Please try again.";
}

if (!isset($_SESSION['pending_staff'])) {
    header("Location: AddStaff.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Staff OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
<?php if ($error_message): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded">
        <?= $error_message ?>
    </div>
<?php endif; ?>
    <form action="verify_staff_otp_process.php" method="POST" class="bg-white p-8 rounded shadow w-96">
        <h2 class="text-2xl font-bold text-center mb-4 text-gray-700">Enter Verification Code</h2>

        <p class="text-gray-600 text-sm text-center mb-4">
            A verification code was sent to <b><?= htmlspecialchars($_SESSION['pending_staff']['email']) ?></b>
        </p>

        <input type="text" name="otp_input" required class="w-full border p-2 rounded text-center tracking-widest text-xl">

        <button class="w-full bg-[rgb(116,142,159)] hover:bg-[rgb(96,122,138)] text-white py-2 mt-4 rounded">
            Verify
        </button>
    </form>
</body>
</html>