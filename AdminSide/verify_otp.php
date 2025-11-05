<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['otp'])) {
    header("Location: AdminStaff.php");
    exit;
}

$errors = [];
$success = false;

// Handle OTP verification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = $_POST['otp'];

    // Check if OTP is correct
    if ($input_otp == $_SESSION['otp']) {
        // OTP is correct, add staff to the database
        $name = $_SESSION['staff_name'];  // Store the name and email in session
        $email = $_SESSION['otp_email']; 
        $password = $_SESSION['staff_password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO staff (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $success = true;

            // Clear OTP data from session
            unset($_SESSION['otp']);
            unset($_SESSION['otp_email']);
            unset($_SESSION['staff_name']);
            unset($_SESSION['staff_password']);

            header("Location: AdminStaff.php?success=added");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    } else {
        $errors[] = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP | Device Market</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-xl mx-auto mt-12">
        <h2 class="text-3xl text-center text-[rgb(116,142,159)]">Verify OTP</h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc pl-5">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 shadow-md rounded-lg">
            <label for="otp" class="block text-gray-700">Enter OTP:</label>
            <input type="text" name="otp" class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-2 focus:ring-[rgb(116,142,159)]" required>

            <button type="submit" class="bg-[rgb(116,142,159)] text-white px-6 py-2.5 rounded-lg mt-4">Verify OTP</button>
        </form>
    </div>
</body>
</html>
