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
// Get selected cart IDs from URL (passed from viewcart.php)
$cart_ids = isset($_GET['cart_ids']) ? explode(',', $_GET['cart_ids']) : [];

if (empty($cart_ids)) {
    echo "<p class='text-center mt-10 text-gray-500'>No items selected for checkout.</p>";
    exit;
}

// --- Fetch only selected cart items ---
$placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
$types = str_repeat('i', count($cart_ids) + 1); // +1 for user_id

$query = "
  SELECT c.cart_id, c.quantity, p.product_name, p.price, p.image_path, p.stock
  FROM cart c
  JOIN products p ON c.product_id = p.product_id
  WHERE c.user_id = ? AND c.cart_id IN ($placeholders)
";

$stmt = $conn->prepare($query);

// Bind parameters dynamically
$params = array_merge([$user_id], $cart_ids);
$stmt->bind_param($types, ...$params);

$stmt->execute();
$cartItems = $stmt->get_result();

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
  <?php $total_price = 0; ?>


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
        <div class="flex-1 border border-gray-200 rounded-lg p-4 overflow-y-auto max-h-[800px]">
        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

        <?php $total_price = 0; // initialize to prevent warnings ?>

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
<div class="flex-1 border border-gray-200 rounded-md p-3">
  <h2 class="text-lg font-semibold mb-3">Shipping Details</h2>

  <form id="checkoutForm" action="process_checkout_cart.php" method="POST" class="space-y-3">
    <input type="hidden" name="total_price" value="<?= $total_price ?>">

    <div>
      <label class="block text-gray-700 text-sm font-medium mb-1">First Name</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required
             class="w-full border border-gray-300 rounded-md px-2.5 py-1.5 text-sm focus:ring focus:ring-blue-100">
    </div>

    <div>
      <label class="block text-gray-700 text-sm font-medium mb-1">Last Name</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required
             class="w-full border border-gray-300 rounded-md px-2.5 py-1.5 text-sm focus:ring focus:ring-blue-100">
    </div>

    <div>
      <label class="block text-gray-700 text-sm font-medium mb-1">Contact Number</label>
      <input type="text" name="contact_num" value="<?= htmlspecialchars($user['contact_num'] ?? '') ?>" required
             class="w-full border border-gray-300 rounded-md px-2.5 py-1.5 text-sm focus:ring focus:ring-blue-100">
    </div>

<!-- ADDRESS PART-->
    <div class="space-y-3">
      <div class="flex flex-col">
        <label class="block text-gray-700 font-medium text-sm">Address:</label>
        <input type="text" name="address" 
               value="<?= htmlspecialchars($user['address'] ?? '') ?>" 
               class="w-full border rounded-md px-2.5 py-1.5 text-sm">
      </div>

      <div class="flex flex-col">
        <label class="block text-gray-700 font-medium text-sm">City:</label>
        <input type="text" name="city" 
               value="<?= htmlspecialchars($user['city'] ?? '') ?>" 
               class="w-full border rounded-md px-2.5 py-1.5 text-sm">
      </div>

      <div class="flex flex-col">
        <label class="block text-gray-700 font-medium text-sm">Province:</label>
        <input type="text" name="province" 
               value="<?= htmlspecialchars($user['province'] ?? '') ?>" 
               class="w-full border rounded-md px-2.5 py-1.5 text-sm">
      </div>

      <div class="flex flex-col">
        <label class="block text-gray-700 font-medium text-sm">Postal Code:</label>
        <input type="text" name="postal_code" 
               value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" 
               class="w-full border rounded-md px-2.5 py-1.5 text-sm">
      </div>
    </div>

    <!-- Payment Option Dropdown -->
    <div>
      <label class="block text-gray-700 text-sm font-medium mb-1">Payment Method</label>
      <select name="payment_method" required
              class="w-full border border-gray-300 rounded-md px-2.5 py-1.5 text-sm bg-white focus:ring focus:ring-blue-100">
        <option value="Cash on Delivery" selected>Cash on Delivery</option>
      </select>
    </div>

    <!-- Confirmation Checkbox -->
    <div class="flex items-center mt-3">
      <input type="checkbox" id="confirmDetails" class="mr-2 accent-green-600 scale-90" checked>
      <label for="confirmDetails" class="text-sm text-gray-700">
        I confirm my address and details are correct.
      </label>
    </div>

    <!-- VALIDATION MESSAGE -->
    <p id="shippingWarning" class="text-red-600 text-sm mt-2 hidden">
    Please fill out all shipping details before confirming your purchase.
    </p>
    <!-- Confirm Button -->
    <div class="flex justify-end mt-4">
      <button id="submitBtn" type="submit"
              class="px-4 py-1.5 rounded-md font-semibold text-sm transition 
                     disabled:bg-gray-400 disabled:cursor-not-allowed 
                     bg-green-600 text-white hover:bg-green-700">
        Confirm Purchase
      </button>
    </div>
  </form>


</div>

    </div>
  </div>

<script>
const form = document.getElementById('checkoutForm');
const submitBtn = document.getElementById('submitBtn');
const confirmBox = document.getElementById('confirmDetails');
const shippingWarning = document.getElementById('shippingWarning');

function toggleFormFields() {
  const fields = form.querySelectorAll('input[type="text"], textarea');

  // Make fields uneditable if checkbox is checked and change background color
  fields.forEach(field => {
    field.readOnly = confirmBox.checked;
    field.classList.toggle('bg-gray-100', confirmBox.checked);
    field.classList.toggle('bg-white', !confirmBox.checked);
  });

  // Check if all fields are filled
  const allFilled = [...fields].every(field => field.value.trim() !== '');

  // Enable/disable button and change color dynamically
  if (confirmBox.checked && allFilled) {
    submitBtn.disabled = false;
    submitBtn.classList.remove('bg-gray-400', 'text-gray-200');
    submitBtn.classList.add('bg-green-600', 'text-white');
    shippingWarning.classList.add('hidden'); // hide warning
  } else {
    submitBtn.disabled = true;
    submitBtn.classList.remove('bg-green-600', 'text-white');
    submitBtn.classList.add('bg-gray-400', 'text-gray-200');
    
    // Show warning if any field is empty
    if (!allFilled) {
      shippingWarning.classList.remove('hidden');
    } else {
      shippingWarning.classList.add('hidden');
    }
  }
}

// Run on page load
toggleFormFields();

// Listen to checkbox changes
confirmBox.addEventListener('change', toggleFormFields);

// Monitor input changes
form.addEventListener('input', toggleFormFields);

</script>



</body>
</html>
