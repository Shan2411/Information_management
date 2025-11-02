<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$pageTitle = 'Add Product';
$isEdit = false;
$product = null;

// Check if editing
if (isset($_GET['edit'])) {
    $isEdit = true;
    $product_id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM products WHERE product_id = $product_id");
    $product = $result->fetch_assoc();
    $pageTitle = 'Edit Product';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image_path = $conn->real_escape_string($_POST['image_path']);

    if ($isEdit && isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        $query = "UPDATE products SET 
                  product_name='$product_name', 
                  description='$description', 
                  price=$price, 
                  stock=$stock, 
                  image_path='$image_path' 
                  WHERE product_id=$product_id";
        $conn->query($query);
        header("Location: AdminProducts.php?success=updated");
    } else {
        $query = "INSERT INTO products (product_name, description, price, stock, image_path, sold_count) 
                  VALUES ('$product_name', '$description', $price, $stock, '$image_path', 0)";
        $conn->query($query);
        header("Location: AdminProducts.php?success=added");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | Device Market</title>
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
                <a href="AdminProducts.php" class="sidebar-link active flex items-center py-3 px-4 rounded-lg transition">
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
                <h1 class="text-3xl font-bold text-[rgb(116,142,159)]"><?= $pageTitle ?></h1>
                <p class="text-gray-500 text-sm mt-1">
                    <a href="AdminProducts.php" class="hover:underline"><i class="fas fa-arrow-left mr-1"></i>Back to Products</a>
                </p>
            </header>

            <div class="p-8">
                <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
                    <form method="POST" class="space-y-6">
                        <?php if ($isEdit): ?>
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <?php endif; ?>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-tag text-[rgb(116,142,159)] mr-2"></i>Product Name
                            </label>
                            <input type="text" name="product_name" required 
                                value="<?= $isEdit ? htmlspecialchars($product['product_name']) : '' ?>"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]"
                                placeholder="Enter product name">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-align-left text-[rgb(116,142,159)] mr-2"></i>Description
                            </label>
                            <textarea name="description" rows="4" required 
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]"
                                placeholder="Enter product description"><?= $isEdit ? htmlspecialchars($product['description']) : '' ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    <i class="fas fa-peso-sign text-[rgb(116,142,159)] mr-2"></i>Price (₱)
                                </label>
                                <input type="number" name="price" step="0.01" required 
                                    value="<?= $isEdit ? $product['price'] : '' ?>"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]"
                                    placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    <i class="fas fa-boxes text-[rgb(116,142,159)] mr-2"></i>Stock Quantity
                                </label>
                                <input type="number" name="stock" required 
                                    value="<?= $isEdit ? $product['stock'] : '' ?>"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]"
                                    placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-image text-[rgb(116,142,159)] mr-2"></i>Image URL
                            </label>
                            <input type="text" name="image_path" required 
                                value="<?= $isEdit ? htmlspecialchars($product['image_path']) : '' ?>"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]"
                                placeholder="https://example.com/image.jpg">
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>Enter the full URL of the product image
                            </p>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" 
                                class="flex-1 bg-[rgb(116,142,159)] hover:bg-[rgb(100,123,136)] text-white font-semibold py-3 rounded-lg transition shadow-md">
                                <i class="fas fa-save mr-2"></i><?= $isEdit ? 'Update Product' : 'Add Product' ?>
                            </button>
                            <a href="AdminProducts.php" 
                                class="px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition text-center">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>