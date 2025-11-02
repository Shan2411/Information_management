<?php
include 'db_connect.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: HomepageUser.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Get product + quantity ---
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

if ($product_id <= 0 || $quantity <= 0) {
    die("Invalid checkout details.");
}

// --- Fetch product ---
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// --- Fetch user info ---
$userStmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

$total_price = $product['price'] * $quantity;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout | <?= htmlspecialchars($product['product_name']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <?php include 'header.php'; ?> 
  <!-- ✅ Top Row: Back Button (left) + Checkout Title (center) -->
  <div class="max-w-5xl mx-auto mt-8 mb-4 px-2 flex items-center justify-between">
    <a href="mainproducts.php" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-gray-200 transition">
      <span class="text-lg">←</span>
      <span>Back to Products</span>
    </a>

    <h1 class="text-2xl font-bold text-gray-800 absolute left-1/2 transform -translate-x-1/2">
      Checkout
    </h1>
  </div>

  <!-- Checkout Container -->
  <div class="max-w-5xl mx-auto bg-white shadow-md rounded-xl p-6 mb-10 mt-4">
    <div class="flex flex-col md:flex-row gap-6">
      <!-- Product Summary -->
      <div class="flex-1 border border-gray-200 rounded-lg p-4">
        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
        <div class="flex items-center gap-4 mb-4">
          <img src="<?= htmlspecialchars($product['image_path'] ?? 'https://via.placeholder.com/120') ?>" 
               alt="<?= htmlspecialchars($product['product_name']) ?>" 
               class="w-24 h-24 object-cover rounded-md">
          <div>
            <p class="font-medium text-lg"><?= htmlspecialchars($product['product_name']) ?></p>
            <p class="text-gray-600">₱<?= number_format($product['price'], 2) ?></p>
            <p class="text-gray-600">Quantity: <?= $quantity ?></p>
          </div>
        </div>
        <hr class="my-3">
        <p class="text-lg font-semibold text-right">Total: 
          <span class="text-red-600">₱<?= number_format($total_price, 2) ?></span>
        </p>
      </div>

      <!-- User Info -->
      <div class="flex-1 border border-gray-200 rounded-lg p-4">
        <h2 class="text-xl font-semibold mb-4">Shipping Details</h2>

        <form action="process_checkout.php" method="POST" class="space-y-4">
          <input type="hidden" name="product_id" value="<?= $product_id ?>">
          <input type="hidden" name="quantity" value="<?= $quantity ?>">
          <input type="hidden" name="total_price" value="<?= $total_price ?>">

          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">First Name</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100">
          </div>

          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Last Name</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100">
          </div>

          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100">
          </div>

          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Contact Number</label>
            <input type="text" name="contact_num" value="<?= htmlspecialchars($user['contact_num'] ?? '') ?>" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100">
          </div>

          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Address</label>
            <textarea name="address" required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100"></textarea>
          </div>

          <!-- ✅ Payment Option Dropdown -->
          <div>
            <label class="block text-gray-700 text-sm font-medium mb-1">Payment Method</label>
            <select name="payment_method" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring focus:ring-blue-100">
              <option value="Cash on Delivery" selected>Cash on Delivery</option>
            </select>
          </div>

          <button type="submit" 
                  class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
            Confirm Purchase
          </button>

        </form>
      </div>
    </div>
  </div>

</body>
</html>
