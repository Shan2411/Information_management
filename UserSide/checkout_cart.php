<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: HomepageUser.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// --- Fetch user info ---
$userStmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

// --- Fetch all cart items for the logged-in user ---
$cartQuery = "
  SELECT c.cart_id, c.quantity, p.product_name, p.price, p.image_path, p.stock
  FROM cart c
  JOIN products p ON c.product_id = p.product_id
  WHERE c.user_id = ?
";
$stmt = $conn->prepare($cartQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartItems = $stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout | Your Cart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <?php include 'header.php'; ?>

  <div class="max-w-5xl mx-auto mt-8 mb-4 px-2 flex items-center justify-between">
    <a href="viewcart.php" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-gray-200 transition">
      <span class="text-lg">←</span>
      <span>Back to Cart</span>
    </a>

    <h1 class="text-2xl font-bold text-gray-800 absolute left-1/2 transform -translate-x-1/2">
      Checkout
    </h1>
  </div>

  <div class="max-w-5xl mx-auto bg-white shadow-md rounded-xl p-6 mb-10 mt-4">
    <div class="flex flex-col md:flex-row gap-6">

      <!-- Order Summary -->
      <div class="flex-1 border border-gray-200 rounded-lg p-4 overflow-y-auto max-h-[400px]">
        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

        <?php if ($cartItems->num_rows > 0): ?>
          <?php while ($item = $cartItems->fetch_assoc()): 
              $item_total = $item['price'] * $item['quantity'];
              $total_price += $item_total;
          ?>
            <div class="flex items-center gap-4 mb-4 border-b pb-3">
              <img src="<?= htmlspecialchars($item['image_path'] ?? 'https://via.placeholder.com/100') ?>" 
                   alt="<?= htmlspecialchars($item['product_name']) ?>" 
                   class="w-20 h-20 object-cover rounded-md">
              <div class="flex-1">
                <p class="font-medium text-lg"><?= htmlspecialchars($item['product_name']) ?></p>
                <p class="text-gray-600">₱<?= number_format($item['price'], 2) ?> × <?= $item['quantity'] ?></p>
                <p class="text-gray-800 font-semibold">₱<?= number_format($item_total, 2) ?></p>
              </div>
            </div>
          <?php endwhile; ?>
          <hr class="my-3">
          <p class="text-lg font-semibold text-right">
            Total: <span class="text-red-600">₱<?= number_format($total_price, 2) ?></span>
          </p>
        <?php else: ?>
          <p class="text-gray-500 text-center">Your cart is empty.</p>
        <?php endif; ?>
      </div>

      <!-- Shipping Form -->
      <div class="flex-1 border border-gray-200 rounded-lg p-4">
        <h2 class="text-xl font-semibold mb-4">Shipping Details</h2>

        <form action="process_checkout_cart.php" method="POST" class="space-y-4">
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

          </button>
        </form>
      </div>

    </div>
  </div>

</body>
</html>
