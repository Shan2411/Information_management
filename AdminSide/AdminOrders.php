<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE order_id=$order_id");
    header("Location: AdminOrders.php?success=updated");
    exit();
}

// Fetch orders with filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$query = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.user_id";
if ($filter) {
    $query .= " WHERE o.status = '" . $conn->real_escape_string($filter) . "'";
}
$query .= " ORDER BY o.order_date DESC";
$orders = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Device Market</title>
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
                <a href="AdminOrders.php" class="sidebar-link active flex items-center py-3 px-4 rounded-lg transition">
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
                <h1 class="text-3xl font-bold text-[rgb(116,142,159)]">Manage Orders</h1>
                <p class="text-gray-500 text-sm mt-1">Track and update order statuses</p>
            </header>

            <div class="p-8">
                <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i>Order status updated successfully!
                </div>
                <?php endif; ?>

                <!-- Filter Buttons -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <div class="flex flex-wrap gap-3">
                        <a href="AdminOrders.php" 
                            class="px-5 py-2.5 rounded-lg font-semibold transition <?= $filter == '' ? 'bg-[rgb(116,142,159)] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                            <i class="fas fa-list mr-2"></i>All Orders
                        </a>
                        <a href="AdminOrders.php?filter=pending" 
                            class="px-5 py-2.5 rounded-lg font-semibold transition <?= $filter == 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                            <i class="fas fa-clock mr-2"></i>Pending
                        </a>
                        <a href="AdminOrders.php?filter=processing" 
                            class="px-5 py-2.5 rounded-lg font-semibold transition <?= $filter == 'processing' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                            <i class="fas fa-cog mr-2"></i>Processing
                        </a>
                        <a href="AdminOrders.php?filter=completed" 
                            class="px-5 py-2.5 rounded-lg font-semibold transition <?= $filter == 'completed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                            <i class="fas fa-check mr-2"></i>Completed
                        </a>
                        <a href="AdminOrders.php?filter=cancelled" 
                            class="px-5 py-2.5 rounded-lg font-semibold transition <?= $filter == 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                            <i class="fas fa-times mr-2"></i>Cancelled
                        </a>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-[rgb(116,142,159)] text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Order ID</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Customer</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Date</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Total Amount</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-bold text-[rgb(116,142,159)]">#<?= $order['order_id'] ?></td>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($order['username']) ?></p>
                                        <p class="text-sm text-gray-500"><i class="fas fa-envelope mr-1"></i><?= htmlspecialchars($order['email']) ?></p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <i class="fas fa-calendar mr-1"></i><?= date('M d, Y', strtotime($order['order_date'])) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800">₱<?= number_format($order['total_amount'], 2) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                                            <?php 
                                            echo $order['status'] == 'completed' ? 'bg-green-100 text-green-700' : 
                                                ($order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                                ($order['status'] == 'processing' ? 'bg-blue-100 text-blue-700' : 
                                                'bg-red-100 text-red-700'));
                                            ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" class="flex gap-2 justify-center items-center">
                                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                            <select name="status" 
                                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status" 
                                                class="bg-[rgb(116,142,159)] hover:bg-[rgb(100,123,136)] text-white px-4 py-2 rounded-lg text-sm transition font-semibold">
                                                <i class="fas fa-sync-alt mr-1"></i>Update
                                            </button>
                                        </form>
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