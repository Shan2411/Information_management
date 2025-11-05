<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit;
}

// Date filter
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Sales statistics
$salesQuery = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as avg_order_value,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
    FROM orders 
    WHERE order_date BETWEEN '$startDate' AND '$endDate'";
$salesStats = $conn->query($salesQuery)->fetch_assoc();

// Top selling products
$topProducts = $conn->query("SELECT product_name, sold_count, price, (sold_count * price) as revenue 
    FROM products ORDER BY sold_count DESC LIMIT 5");

// Recent transactions
$transactions = $conn->query("SELECT o.*, u.username FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    WHERE o.order_date BETWEEN '$startDate' AND '$endDate'
    ORDER BY o.order_date DESC LIMIT 10");

// Daily sales chart data
$dailySales = $conn->query("SELECT DATE(order_date) as sale_date, SUM(total_amount) as daily_total 
    FROM orders 
    WHERE order_date BETWEEN '$startDate' AND '$endDate' AND status = 'completed'
    GROUP BY DATE(order_date) 
    ORDER BY sale_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Report | Device Market</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="StaffOrders.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
            <i class="fas fa-shopping-cart w-6"></i><span class="ml-3">Orders</span>
        </a>
        <a href="StaffProducts.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
            <i class="fas fa-box w-6"></i><span class="ml-3">Products</span>
        </a>
        <a href="StaffReports.php" class="sidebar-link active flex items-center py-3 px-4 rounded-lg transition">
            <i class="fas fa-chart-bar w-6"></i><span class="ml-3">Reports</span>
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
        <h1 class="text-3xl font-bold text-[rgb(116,142,159)]">Sales Report</h1>
        <p class="text-gray-500 text-sm mt-1">Analytics and insights for your business</p>
    </header>

    <div class="p-8">
        <!-- Date Filter -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="<?= $startDate ?>" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="<?= $endDate ?>" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
                </div>
                <button type="submit" class="bg-[rgb(116,142,159)] hover:bg-[rgb(100,123,136)] text-white px-6 py-2.5 rounded-lg transition font-semibold">
                    <i class="fas fa-filter mr-2"></i>Apply Filter
                </button>
            </form>
        </div>

        <!-- Statistics Cards -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                <h3 class="text-4xl font-bold mt-2">₱<?= number_format($salesStats['total_revenue'] ?? 0, 2) ?></h3>
                <p class="text-green-100 text-xs mt-2">Completed orders</p>
            </div>
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                <p class="text-blue-100 text-sm font-medium">Total Orders</p>
                <h3 class="text-4xl font-bold mt-2"><?= $salesStats['total_orders'] ?></h3>
                <p class="text-blue-100 text-xs mt-2">All orders in period</p>
            </div>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
                <p class="text-purple-100 text-sm font-medium">Avg Order Value</p>
                <h3 class="text-4xl font-bold mt-2">₱<?= number_format($salesStats['avg_order_value'] ?? 0, 2) ?></h3>
                <p class="text-purple-100 text-xs mt-2">Per transaction</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-xl p-6 shadow-lg">
                <p class="text-yellow-100 text-sm font-medium">Completed Orders</p>
                <h3 class="text-4xl font-bold mt-2"><?= $salesStats['completed_orders'] ?></h3>
                <p class="text-yellow-100 text-xs mt-2"><?= $salesStats['pending_orders'] ?> pending, <?= $salesStats['cancelled_orders'] ?> cancelled</p>
            </div>
        </section>

        <!-- Charts and Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Daily Sales Chart -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-[rgb(116,142,159)] mb-4">Daily Sales Trend</h3>
                <canvas id="salesChart"></canvas>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-[rgb(116,142,159)] mb-4">Top Selling Products</h3>
                <div class="space-y-3">
                    <?php while ($product = $topProducts->fetch_assoc()): ?>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($product['product_name']) ?></p>
                            <p class="text-sm text-gray-500"><?= $product['sold_count'] ?> units sold</p>
                        </div>
                        <p class="font-bold text-[rgb(116,142,159)]">₱<?= number_format($product['revenue'], 2) ?></p>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-[rgb(116,142,159)] mb-4">Recent Transactions</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Order ID</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Amount</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($txn = $transactions->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-semibold">#<?= $txn['order_id'] ?></td>
                            <td class="px-4 py-3 text-sm"><?= htmlspecialchars($txn['username']) ?></td>
                            <td class="px-4 py-3 text-sm"><?= date('M d, Y', strtotime($txn['order_date'])) ?></td>
                            <td class="px-4 py-3 text-sm font-semibold">₱<?= number_format($txn['total_amount'], 2) ?></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    <?= $txn['status'] == 'completed' ? 'bg-green-100 text-green-700' : 
                                        ($txn['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
                                    <?= ucfirst($txn['status']) ?>
                                </span>
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

<script>
// Prepare chart data
const salesData = <?php 
    $dates = [];
    $amounts = [];
    $dailySales->data_seek(0);
    while($row = $dailySales->fetch_assoc()) {
        $dates[] = date('M d', strtotime($row['sale_date']));
        $amounts[] = $row['daily_total'];
    }
    echo json_encode(['dates' => $dates, 'amounts' => $amounts]);
?>;

const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: salesData.dates,
        datasets: [{
            label: 'Daily Sales (₱)',
            data: salesData.amounts,
            borderColor: 'rgb(116,142,159)',
            backgroundColor: 'rgba(116,142,159,0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
</body>
</html>
