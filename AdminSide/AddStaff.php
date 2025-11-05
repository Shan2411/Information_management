<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$name = $email = "";

// Keep input values after error redirect using session
if (isset($_SESSION['form_data'])) {
    $name = $_SESSION['form_data']['name'] ?? "";
    $email = $_SESSION['form_data']['email'] ?? "";
    unset($_SESSION['form_data']);
}

// Check if errors were sent back
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    unset($_SESSION['errors']);
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
<style>
    .sidebar-link:hover { background-color: rgba(255, 255, 255, 0.1); }
    .sidebar-link.active { background-color: rgba(255, 255, 255, 0.2); border-left: 4px solid white; }
</style>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="flex h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-[rgb(116,142,159)] text-white flex flex-col shadow-xl">
        <div class="p-6 text-center text-2xl font-bold border-b border-white/20">
            <i class="fas fa-microchip mr-2"></i>Device Market
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="AdminDashboard.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-chart-line w-6"></i>
                <span class="ml-3">Dashboard</span>
            </a>
            <a href="AdminProducts.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-box w-6"></i>
                <span class="ml-3">Manage Products</span>
            </a>
            <a href="AdminUsers.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-users w-6"></i>
                <span class="ml-3">Manage Users</span>
            </a>
            <a href="AdminOrders.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-shopping-cart w-6"></i>
                <span class="ml-3">Orders</span>
            </a>
            <a href="AdminReports.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-chart-bar w-6"></i>
                <span class="ml-3">Sales Report</span>
            </a>
            <a href="AdminStaff.php" class="sidebar-link active flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-user-tie w-6"></i>
                <span class="ml-3">Staff Accounts</span>
            </a>
        </nav>
        <div class="p-4 border-t border-white/20">
            <a href="logout.php" class="block w-full text-center bg-white text-[rgb(116,142,159)] py-2.5 rounded-lg font-semibold hover:bg-gray-100 transition">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
            <h1 class="text-3xl font-bold text-[rgb(116,142,159)]">Add New Staff</h1>
            <p class="text-gray-500 text-sm mt-1">Create a new staff account</p>
        </header>

        <div class="p-8 max-w-3xl mx-auto">

            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                    <ul class="list-disc pl-5">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-md p-6">
                <form method="POST" action="send_staff_otp.php" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Full Name</label>
                        <input type="text" name="name" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Email</label>
                        <input type="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">Password</label>
                        <input type="password" name="password" placeholder="Minimum 6 characters" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                    </div>
                    <div class="flex gap-3 mt-4">
                        <button type="submit" class="bg-[rgb(116,142,159)] hover:bg-[rgb(100,123,136)] text-white px-6 py-2.5 rounded-lg transition inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>Add Staff
                        </button>
                        <a href="AdminStaff.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg transition inline-flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>

