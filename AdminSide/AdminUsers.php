<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id = $user_id");
    header("Location: AdminUsers.php?success=deleted");
    exit();
}

// Fetch users with search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM users";
if ($search) {
    $query .= " WHERE username LIKE '%" . $conn->real_escape_string($search) . "%' OR email LIKE '%" . $conn->real_escape_string($search) . "%'";
}
$query .= " ORDER BY user_id DESC";
$users = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Device Market</title>
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
        <a href="AdminUsers.php" class="sidebar-link active flex items-center py-3 px-4 rounded-lg transition">
            <i class="fas fa-users w-6"></i>
            <span class="ml-3">Manage Users</span>
        </a>
        <a href="AdminStaff.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
            <i class="fas fa-user-tie w-6"></i>
            <span class="ml-3">Manage Staff</span>
        </a>
        <a href="AdminOrders.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
            <i class="fas fa-shopping-cart w-6"></i>
            <span class="ml-3">Orders</span>
        </a>
        <a href="AdminReports.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
            <i class="fas fa-chart-bar w-6"></i>
            <span class="ml-3">Sales Report</span>
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
                <h1 class="text-3xl font-bold text-[rgb(116,142,159)]">Manage Users</h1>
                <p class="text-gray-500 text-sm mt-1">View and manage registered customers</p>
            </header>

            <div class="p-8">
                <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>User deleted successfully!
                </div>
                <?php endif; ?>

                <!-- Search Bar -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <form method="GET" class="flex gap-3">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                            placeholder="Search by username or email..." 
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                        <button type="submit" class="bg-[rgb(116,142,159)] hover:bg-[rgb(100,123,136)] text-white px-6 py-2.5 rounded-lg transition">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <?php if ($search): ?>
                        <a href="AdminUsers.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-lg transition">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-[rgb(116,142,159)] text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">ID</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">User</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Registered</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php while ($user = $users->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-semibold"><?= $user['user_id'] ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mr-3 shadow">
                                                <span class="text-white font-bold text-lg"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($user['username']) ?></p>
                                                <p class="text-xs text-gray-500">User ID: <?= $user['user_id'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <i class="fas fa-envelope text-gray-400 mr-2"></i><?= htmlspecialchars($user['email']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                        <?= isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A' ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="AdminUsers.php?delete=<?= $user['user_id'] ?>" 
                                            onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition inline-flex items-center">
                                            <i class="fas fa-trash mr-2"></i>Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>