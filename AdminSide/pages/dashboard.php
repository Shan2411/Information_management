<?php
session_start();
include 'includes/db_connect.php';

$pageTitle = 'Dashboard';

// Fetch statistics
$totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$totalRevenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;

// Recent orders
$recentOrders = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 5");

// Low stock products
$lowStockProducts = $conn->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="lg:ml-64 pt-20 px-6 pb-6">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Products</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $totalProducts; ?></h3>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full">
                        <i class="fas fa-box text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Users</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $totalUsers; ?></h3>
                    </div>
                    <div class="bg-green-100 p-4 rounded-full">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Orders</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo $totalOrders; ?></h3>
                    </div>
                    <div class="bg-purple-100 p-4 rounded-full">
                        <i class="fas fa-shopping-cart text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1">₱<?php echo number_format($totalRevenue, 2); ?></h3>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-full">
                        <i class="fas fa-peso-sign text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders & Low Stock -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Orders</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Order ID</th>
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Customer</th>
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Total</th>
                                <th class="text-left py-3 text-sm font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recentOrders->fetch_assoc()): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 text-sm">#<?php echo $order['order_id']; ?></td>
                                <td class="py-3 text-sm"><?php echo htmlspecialchars($order['username']); ?></td>
                                <td class="py-3 text-sm">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php echo $order['status'] == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Low Stock Alert</h3>
                <div class="space-y-3">
                    <?php while ($product = $lowStockProducts->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($product['product_name']); ?></p>
                            <p class="text-sm text-gray-500">Stock: <?php echo $product['stock']; ?> units</p>
                        </div>
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Low</span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>