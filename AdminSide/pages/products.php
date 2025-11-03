<?php
session_start();
include 'includes/db_connect.php';

$pageTitle = 'Products Management';

// Handle delete
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE product_id = $product_id");
    header("Location: products.php?success=deleted");
    exit();
}

// Fetch all products
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM products";
if ($search) {
    $query .= " WHERE product_name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
$query .= " ORDER BY product_id DESC";
$products = $conn->query($query);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="lg:ml-64 pt-20 px-6 pb-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h2 class="text-3xl font-bold text-gray-800">Products Management</h2>
            <a href="add_product.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                <i class="fas fa-plus mr-2"></i>Add New Product
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>
            <?php 
            if ($_GET['success'] == 'added') echo 'Product added successfully!';
            if ($_GET['success'] == 'updated') echo 'Product updated successfully!';
            if ($_GET['success'] == 'deleted') echo 'Product deleted successfully!';
            ?>
        </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                    placeholder="Search products..." 
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if ($search): ?>
                <a href="products.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Image</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Product Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Price</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Stock</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Sold</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($product = $products->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm"><?php echo $product['product_id']; ?></td>
                            <td class="px-6 py-4">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                                    alt="Product" class="w-16 h-16 object-cover rounded-lg">
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($product['product_name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo substr(htmlspecialchars($product['description']), 0, 50); ?>...</p>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">₱<?php echo number_format($product['price'], 2); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-sm rounded-full 
                                    <?php echo $product['stock'] < 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm"><?php echo $product['sold_count']; ?></td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="add_product.php?edit=<?php echo $product['product_id']; ?>" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="products.php?delete=<?php echo $product['product_id']; ?>" 
                                        onclick="return confirm('Are you sure you want to delete this product?')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

</body>
</html>