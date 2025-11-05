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

    // Get current status
    $current_status_result = $conn->query("SELECT status FROM orders WHERE order_id = $order_id");
    $current_status = $current_status_result->fetch_assoc()['status'];

    // Allowable transitions
    $allowed_transitions = [
        'pending' => ['processing', 'completed', 'cancelled'],
        'processing' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => []
    ];

if (in_array($status, $allowed_transitions[$current_status])) {
    // Update the order status
    $conn->query("UPDATE orders SET status='$status' WHERE order_id=$order_id");

    // Increment sold_count if status changed to completed
    if ($status === 'completed' && $current_status !== 'completed') {
        // Get the product_id and quantity from this order
        $order_result = $conn->query("SELECT product_id, quantity FROM orders WHERE order_id=$order_id");
        if ($order_result->num_rows > 0) {
            $order_data = $order_result->fetch_assoc();
            $product_id = (int)$order_data['product_id'];
            $quantity = (int)$order_data['quantity'];

            // Update sold_count in products table
            $conn->query("UPDATE products SET sold_count = sold_count + $quantity WHERE product_id = $product_id");
        }
    }

    header("Location: AdminOrders.php?success=updated");
    exit();
} else {
    header("Location: AdminOrders.php?error=invalid");
    exit();
}


}

// Filters
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$query = "SELECT o.*, u.username, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.user_id
          WHERE 1=1";

if ($filter) {
    $query .= " AND o.status = '" . $conn->real_escape_string($filter) . "'";
}

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND DATE(o.order_date) BETWEEN '$from_date' AND '$to_date'";
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
        select:disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }
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
                    <i class="fas fa-chart-line w-6"></i><span class="ml-3">Dashboard</span>
                </a>
                <a href="AdminProducts.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                    <i class="fas fa-box w-6"></i><span class="ml-3">Manage Products</span>
                </a>
                <a href="AdminUsers.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                    <i class="fas fa-users w-6"></i><span class="ml-3">Manage Users</span>
                </a>
                <a href="AdminOrders.php" class="sidebar-link active flex items-center py-3 px-4 rounded-lg transition">
                    <i class="fas fa-shopping-cart w-6"></i><span class="ml-3">Orders</span>
                </a>
                <a href="AdminReports.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                    <i class="fas fa-chart-bar w-6"></i><span class="ml-3">Sales Report</span>
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
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Invalid status transition!
                    </div>
                <?php endif; ?>

                <!-- Filter Buttons -->
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <form method="GET" class="flex flex-wrap items-center gap-4">
                        <div class="flex flex-wrap gap-3">
                            <?php
                            $filters = [
                                '' => ['All Orders', 'fa-list', 'gray'],
                                'pending' => ['Pending', 'fa-clock', 'yellow'],
                                'processing' => ['Processing', 'fa-cog', 'blue'],
                                'completed' => ['Completed', 'fa-check', 'green'],
                                'cancelled' => ['Cancelled', 'fa-times', 'red']
                            ];
                            foreach ($filters as $key => $data):
                                [$label, $icon, $color] = $data;
                                $isActive = ($filter == $key);
                                $bgClass = $isActive ? "bg-$color-600 text-white" : "bg-gray-200 text-gray-700 hover:bg-gray-300";
                            ?>
                                <a href="AdminOrders.php<?= $key ? '?filter=' . $key : '' ?>" 
                                   class="px-5 py-2.5 rounded-lg font-semibold transition <?= $bgClass ?>">
                                    <i class="fas <?= $icon ?> mr-2"></i><?= $label ?>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <!-- DATE FILTER -->
                        <div class="flex items-center gap-3 ml-auto">
                            <label class="font-semibold text-gray-700">From:</label>
                            <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>"
                                   class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[rgb(116,142,159)]">

                            <label class="font-semibold text-gray-700">To:</label>
                            <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>"
                                   class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[rgb(116,142,159)]">

                            <button type="submit"
                                    class="bg-[rgb(116,142,159)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[rgb(100,123,136)] transition">
                                <i class="fas fa-filter mr-2"></i>Apply
                            </button>
                            <a href="AdminOrders.php"
                               class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-400 transition">
                                <i class="fas fa-undo mr-1"></i>Reset
                            </a>
                        </div>
                    </form>
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
                                <?php if ($orders->num_rows > 0): ?>
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
                                                <?= $order['status'] == 'completed' ? 'bg-green-100 text-green-700' : 
                                                   ($order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                                   ($order['status'] == 'processing' ? 'bg-blue-100 text-blue-700' : 
                                                   'bg-red-100 text-red-700')) ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <form method="POST" class="flex gap-2 justify-center items-center">
                                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">

                                                <select name="status"
                                                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]"
                                                    <?= ($order['status'] == 'completed' || $order['status'] == 'cancelled') ? 'disabled' : '' ?>>
                                                    <?php if ($order['status'] == 'pending'): ?>
                                                        <option value="pending" selected>Pending</option>
                                                        <option value="processing">Processing</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    <?php elseif ($order['status'] == 'processing'): ?>
                                                        <option value="processing" selected>Processing</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="cancelled">Cancelled</option>
                                                    <?php elseif ($order['status'] == 'completed'): ?>
                                                        <option value="completed" selected>Completed</option>
                                                    <?php elseif ($order['status'] == 'cancelled'): ?>
                                                        <option value="cancelled" selected>Cancelled</option>
                                                    <?php endif; ?>
                                                </select>

                                                <button type="submit" name="update_status"
                                                    class="bg-[rgb(116,142,159)] hover:bg-[rgb(100,123,136)] text-white px-4 py-2 rounded-lg text-sm transition font-semibold"
                                                    <?= ($order['status'] == 'completed' || $order['status'] == 'cancelled') ? 'disabled' : '' ?>>
                                                    <i class="fas fa-sync-alt mr-1"></i>Update
                                                </button>

                                                <?php if ($order['status'] == 'completed' || $order['status'] == 'cancelled'): ?>
                                                    <i class="fas fa-lock text-gray-400 ml-2"></i>
                                                <?php endif; ?>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-gray-500 py-6">
                                            <i class="fas fa-info-circle mr-2"></i>No orders found for this filter.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
