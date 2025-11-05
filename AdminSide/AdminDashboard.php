<?php
session_start();

// --- DATABASE CONNECTION ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "information_management";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- ADMIN LOGIN CHECK ---
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// --- FETCH STATISTICS ---
$productCount = $conn->query("SELECT COUNT(product_id) AS total_products FROM products")->fetch_assoc()['total_products'];
$userCount = $conn->query("SELECT COUNT(user_id) AS total_users FROM users")->fetch_assoc()['total_users'];
$staffCount = $conn->query("SELECT COUNT(staff_id) AS total_staff FROM staff")->fetch_assoc()['total_staff'];
$orderCount = $conn->query("SELECT COUNT(order_id) AS total_orders FROM orders")->fetch_assoc()['total_orders'];
$totalTransactions = $conn->query("SELECT COUNT(transaction_id) AS total_transactions FROM transactions")->fetch_assoc()['total_transactions'] ?? 0;
$totalSales = $conn->query("SELECT SUM(total_amount) AS total_sales FROM orders WHERE status = 'completed'")->fetch_assoc()['total_sales'] ?? 0;

// --- FETCH RECENT PRODUCTS ---
$recentProducts = $conn->query("SELECT * FROM products ORDER BY product_id DESC LIMIT 5");

// --- FETCH RECENT ORDERS ---
$recentOrders = $conn->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    ORDER BY o.order_date DESC LIMIT 5
");

// --- FETCH LOW STOCK PRODUCTS ---
$lowStockProducts = $conn->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Device Market</title>
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
            <a href="AdminDashboard.php" class="sidebar-link active flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-chart-line w-6"></i><span class="ml-3">Dashboard</span>
            </a>
            <a href="AdminProducts.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-box w-6"></i><span class="ml-3">Manage Products</span>
            </a>
            <a href="AdminUsers.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-users w-6"></i><span class="ml-3">Manage Users</span>
            </a>
            <a href="AdminStaff.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
                <i class="fas fa-user-tie w-6"></i><span class="ml-3">Manage Staff</span>
            </a>
            <a href="AdminOrders.php" class="sidebar-link flex items-center py-3 px-4 rounded-lg transition">
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
        <header class="bg-white shadow-sm border-b border-gray-200 px-8 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-[rgb(116,142,159)]">Dashboard Overview</h1>
                <p class="text-gray-500 text-sm mt-1">Welcome back, <?= htmlspecialchars($_SESSION['admin_username']) ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500"><?= date('l, F j, Y') ?></p>
                <p class="text-xs text-gray-400"><?= date('h:i A') ?></p>
            </div>
        </header>

        <div class="p-8">
            <!-- STATISTICS CARDS -->
            <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Products</p>
                            <h3 class="text-4xl font-bold mt-2"><?= $productCount ?></h3>
                            <p class="text-blue-100 text-xs mt-2">Active in inventory</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg"><i class="fas fa-box text-2xl"></i></div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Registered Users</p>
                            <h3 class="text-4xl font-bold mt-2"><?= $userCount ?></h3>
                            <p class="text-green-100 text-xs mt-2">Total customers</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg"><i class="fas fa-users text-2xl"></i></div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-teal-100 text-sm font-medium">Staff Members</p>
                            <h3 class="text-4xl font-bold mt-2"><?= $staffCount ?></h3>
                            <p class="text-teal-100 text-xs mt-2">Active staff accounts</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg"><i class="fas fa-user-tie text-2xl"></i></div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Total Orders</p>
                            <h3 class="text-4xl font-bold mt-2"><?= $orderCount ?></h3>
                            <p class="text-purple-100 text-xs mt-2">All time orders</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg"><i class="fas fa-shopping-cart text-2xl"></i></div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-indigo-100 text-sm font-medium">Total Transactions</p>
                            <h3 class="text-4xl font-bold mt-2"><?= $totalTransactions ?></h3>
                            <p class="text-indigo-100 text-xs mt-2">All transaction records</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg"><i class="fas fa-exchange-alt text-2xl"></i></div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-6 shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Total Revenue</p>
                            <h3 class="text-4xl font-bold mt-2">₱<?= number_format($totalSales, 0) ?></h3>
                            <p class="text-orange-100 text-xs mt-2">Completed sales</p>
                        </div>
                        <div class="bg-white/20 p-3 rounded-lg"><i class="fas fa-peso-sign text-2xl"></i></div>
                    </div>
                </div>
            </section>

            <!-- RECENT SECTIONS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- RECENT ORDERS -->
                <section class="bg-white shadow-lg rounded-xl p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-[rgb(116,142,159)]">Recent Orders</h2>
                        <a href="AdminOrders.php" class="text-sm text-[rgb(116,142,159)] hover:underline">View All</a>
                    </div>
                    <div class="space-y-3">
                        <?php while ($order = $recentOrders->fetch_assoc()): ?>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-semibold text-gray-800">#<?= $order['order_id'] ?> - <?= htmlspecialchars($order['username']) ?></p>
                                <p class="text-sm text-gray-500">₱<?= number_format($order['total_amount'], 2) ?></p>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full font-semibold 
                                <?= $order['status'] == 'completed' ? 'bg-green-100 text-green-700' : 
                                    ($order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>

                <!-- LOW STOCK ALERT -->
                <section class="bg-white shadow-lg rounded-xl p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-red-600">Low Stock Alert</h2>
                        <a href="AdminProducts.php" class="text-sm text-red-600 hover:underline">Manage Stock</a>
                    </div>
                    <div class="space-y-3">
                        <?php while ($product = $lowStockProducts->fetch_assoc()): ?>
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($product['product_name']) ?></p>
                                <p class="text-sm text-gray-500">Only <?= $product['stock'] ?> units left</p>
                            </div>
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Low
                            </span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            </div>

            <!-- RECENT PRODUCTS TABLE -->
            <section class="bg-white shadow-lg rounded-xl p-6 mt-6">
                <h2 class="text-xl font-bold mb-4 text-[rgb(116,142,159)]">Recent Products</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">#</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Product Name</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Price</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Stock</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Sold</th>
                                <th class="py-3 px-4 text-right text-sm font-semibold text-gray-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($row = $recentProducts->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm"><?= $row['product_id'] ?></td>
                                <td class="py-3 px-4 text-sm font-medium"><?= htmlspecialchars($row['product_name']) ?></td>
                                <td class="py-3 px-4 text-sm">₱<?= number_format($row['price'], 2) ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded-full <?= $row['stock'] < 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                                        <?= (int)$row['stock'] ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm"><?= (int)$row['sold_count'] ?></td>
                                <td class="py-3 px-4 text-right">
                                    <a href="add_product.php?edit=<?= $row['product_id'] ?>" class="text-blue-600 hover:text-blue-800 mr-3"><i class="fas fa-edit"></i></a>
                                    <a href="AdminProducts.php?delete=<?= $row['product_id'] ?>" onclick="return confirm('Delete this product?')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="text-center text-gray-500 text-sm mt-8 pb-4">
                © <?= date('Y') ?> Device Market Admin Panel | All Rights Reserved
            </footer>
        </div>
    </main>
</div>
</body>
</html>
