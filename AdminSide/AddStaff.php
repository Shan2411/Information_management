<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (!$name) $errors[] = "Name is required.";
    if (!$email) $errors[] = "Email is required.";
    if (!$role) $errors[] = "Role is required.";
    if (!$password) $errors[] = "Password is required.";

    // Check email uniqueness
    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO staff (name, email, role, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $role, $hashed_password);
        if ($stmt->execute()) {
            $success = "Staff added successfully!";
            $name = $email = $role = $password = '';
        } else {
            $errors[] = "Failed to add staff. Try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Staff | Device Market</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800">
<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-4 text-[rgb(116,142,159)]">Add New Staff</h1>

    <?php if ($errors): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-4">
            <ul class="list-disc pl-5">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-semibold">Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
        </div>
        <div>
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
        </div>
        <div>
            <label class="block mb-1 font-semibold">Role</label>
            <select name="role" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                <option value="">--Select Role--</option>
                <option value="Admin" <?= (isset($role) && $role=='Admin')?'selected':'' ?>>Admin</option>
                <option value="Staff" <?= (isset($role) && $role=='Staff')?'selected':'' ?>>Staff</option>
            </select>
        </div>
        <div>
            <label class="block mb-1 font-semibold">Password</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
        </div>
        <div class="flex justify-between">
            <a href="AdminStaff.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">Back</a>
            <button type="submit" class="bg-[rgb(116,142,159)] hover:bg-[rgb(100,123,136)] text-white px-6 py-2 rounded-lg transition">Add Staff</button>
        </div>
    </form>
</div>
</body>
</html>

