<?php
include 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: HomepageUser.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for this user
$stmt = $conn->prepare("SELECT *, price * quantity AS subtotal FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $total += $row['subtotal'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Cart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

  <!-- HEADER -->
  <?php include 'header.php'; ?>

  <!-- MAIN CART SECTION -->
  <main class="max-w-7xl mx-auto p-6 mt-10">
    <h1 class="text-3xl font-bold mb-6 text-[rgb(116,142,159)]">Your Shopping Cart</h1>

    <div class="overflow-x-auto bg-white shadow-md rounded-xl">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left font-medium text-gray-700">Product</th>
            <th class="px-6 py-3 text-left font-medium text-gray-700">Description</th>
            <th class="px-6 py-3 text-center font-medium text-gray-700">Price</th>
            <th class="px-6 py-3 text-center font-medium text-gray-700">Quantity</th>
            <th class="px-6 py-3 text-center font-medium text-gray-700">Subtotal</th>
            <th class="px-6 py-3 text-center font-medium text-gray-700">Remove</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (count($cartItems) > 0): ?>
            <?php foreach ($cartItems as $item): ?>
              <tr>
                <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($item['description']) ?></td>
                <td class="px-6 py-4 text-center">₱<?= number_format($item['price'], 2) ?></td>
                <td class="px-6 py-4 text-center">
                  <form method="POST" action="update_cart.php" class="flex items-center justify-center gap-2">
                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-16 text-center border rounded">
                    <button type="submit" class="bg-[rgb(116,142,159)] text-white px-3 py-1 rounded hover:bg-[rgb(100,123,136)] transition">Update</button>
                  </form>
                </td>
                <td class="px-6 py-4 text-center font-semibold">₱<?= number_format($item['subtotal'], 2) ?></td>
                <td class="px-6 py-4 text-center">
                  <form method="POST" action="remove_from_cart.php">
                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr class="bg-gray-50">
              <td colspan="6" class="px-6 py-16 text-center text-gray-400 text-lg">
                Your cart is empty.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
        <?php if (count($cartItems) > 0): ?>
        <tfoot class="bg-gray-100">
          <tr>
            <td colspan="4" class="px-6 py-3 text-right font-bold text-gray-700">Total:</td>
            <td class="px-6 py-3 text-center font-bold text-[rgb(116,142,159)]">₱<?= number_format($total, 2) ?></td>
            <td></td>
          </tr>
        </tfoot>
        <?php endif; ?>
      </table>
    </div>

    <!-- Checkout / Continue Shopping -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
      <a href="mainproducts.php" class="bg-gray-300 text-gray-800 py-3 px-6 rounded hover:bg-gray-400 transition">Continue Shopping</a>
      <?php if (count($cartItems) > 0): ?>
      <a href="checkout.php" class="bg-[rgb(116,142,159)] text-white py-3 px-6 rounded hover:bg-[rgb(100,123,136)] transition">Proceed to Checkout</a>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
