<?php
include 'db_connect.php';
session_start();
$removed = isset($_GET['removed']) && $_GET['removed'] === '1';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: HomepageUser.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items by joining products table
$query = "
  SELECT 
          c.cart_id,
          c.user_id,
          c.product_id,
          c.quantity,
          p.product_name,
          p.description,
          p.price,
          p.stock,
          p.image_path,
          (p.price * c.quantity) AS subtotal
      FROM cart c
      JOIN products p ON c.product_id = p.product_id
      WHERE c.user_id = ?
";

$stmt = $conn->prepare($query);
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
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/line-clamp@0.4.4"></script>
  <script>
    tailwind.config = {
      theme: { extend: {} },
      plugins: [tailwindcssLineClamp],
    }
  </script>
  <style>
    /* Keep row height consistent even for long text */
    td {
      vertical-align: middle;
    }
  </style>
</head>
<body class="bg-gray-50">

  <!-- HEADER -->
  <?php include 'header.php'; ?>

  <!-- MAIN CART SECTION -->
  <main class="max-w-7xl mx-auto p-6 mt-10">

    <?php if (!empty($removed)): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
        ✅ Item removed successfully!
      </div>
    <?php endif; ?>

    <h1 class="text-3xl font-bold mb-6 text-[rgb(116,142,159)]">Your Shopping Cart</h1>

    <div class="bg-white shadow-md rounded-xl">
      <!-- Scrollable Table -->
      <div class="overflow-y-auto overflow-x-auto max-h-[500px] rounded-t-xl">
        <!-- SELECT ALL BUTTON-->
        <div class="flex justify-end items-center p-4 bg-gray-100 border-b">
          <button id="select-all-btn" 
            class="bg-[rgb(116,142,159)] text-white px-4 py-2 rounded hover:bg-[rgb(100,123,136)] transition">
            Select All
          </button>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100 sticky top-0 z-10">
            <tr>
              <th class="px-6 py-3 text-left font-medium text-gray-700 bg-gray-100">Product</th>
              <th class="px-6 py-3 text-left font-medium text-gray-700 bg-gray-100">Description</th>
              <th class="px-6 py-3 text-center font-medium text-gray-700 bg-gray-100">Price</th>
              <th class="px-6 py-3 text-center font-medium text-gray-700 bg-gray-100">Quantity</th>
              <th class="px-6 py-3 text-center font-medium text-gray-700 bg-gray-100">Subtotal</th>
              <th class="px-6 py-3 text-center font-medium text-gray-700 bg-gray-100">Select</th>
              <th class="px-6 py-3 text-center font-medium text-gray-700 bg-gray-100">Remove</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200" id="cart-body">
            <?php if (count($cartItems) > 0): ?>
              <?php foreach ($cartItems as $item): ?>
                <tr data-cart-id="<?= $item['cart_id'] ?>" data-price="<?= $item['price'] ?>" data-stock="<?= $item['stock'] ?>">
                  <td class="px-6 py-4 font-semibold flex items-center gap-3">
                    <?php if (!empty($item['image_path'])): ?>
                      <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-16 h-16 object-cover rounded">
                    <?php else: ?>
                      <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded text-gray-500">No Image</div>
                    <?php endif; ?>
                    <?= htmlspecialchars($item['product_name']) ?>
                  </td>

                  <!-- Description (Clamped to 2 lines with tooltip) -->
                  <td class="px-6 py-4 text-gray-600 max-w-xs" title="<?= htmlspecialchars($item['description'] ?? 'No description available.') ?>">
                    <p class="line-clamp-2">
                      <?= htmlspecialchars($item['description'] ?? 'No description available.') ?>
                    </p>
                  </td>

                  <td class="px-6 py-4 text-center">₱<?= number_format($item['price'], 2) ?></td>

                  <!-- Quantity with + and - -->
                  <td class="px-6 py-4 text-center">
                    <div class="flex justify-center items-center gap-2">
                      <button type="button" class="decrement bg-gray-300 text-gray-700 px-2 py-1 rounded hover:bg-gray-400 transition">-</button>
                      <input type="number" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" class="quantity w-16 text-center border rounded" readonly>
                      <button type="button" class="increment bg-gray-300 text-gray-700 px-2 py-1 rounded hover:bg-gray-400 transition">+</button>
                    </div>
                  </td>

                  <!-- Subtotal -->
                  <td class="px-6 py-4 text-center font-semibold subtotal">₱<?= number_format($item['subtotal'], 2) ?></td>

                  <!-- Select checkbox -->
                  <td class="px-6 py-4 text-center">
                    <input type="checkbox" class="select-item w-5 h-5 text-[rgb(116,142,159)] accent-[rgb(116,142,159)] cursor-pointer" checked>
                  </td>

                  <!-- Remove -->
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
                <td colspan="7" class="px-6 py-16 text-center text-gray-400 text-lg">
                  Your cart is empty.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Selected Total -->
      <?php if (count($cartItems) > 0): ?>
        <div class="bg-gray-100 border-t px-6 py-3 flex justify-between items-center rounded-b-xl">
          <span class="font-bold text-gray-700">Selected Total:</span>
          <span id="cart-total" class="font-bold text-[rgb(116,142,159)] text-lg">₱<?= number_format($total, 2) ?></span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Checkout / Continue Shopping -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
      <a href="mainproducts.php" 
        class="bg-gray-300 text-gray-800 py-3 px-6 rounded hover:bg-gray-400 transition">
        Continue Shopping
      </a>

      <?php if (count($cartItems) > 0): ?>
        <button id="checkout-btn"
                class="bg-green-600 text-white py-3 px-6 rounded hover:bg-green-700 transition">
          Proceed to Checkout
        </button>

        <script>
        const checkoutBtn = document.getElementById('checkout-btn');

        checkoutBtn.addEventListener('click', () => {
          const selectedItems = [];
          document.querySelectorAll('.select-item:checked').forEach(cb => {
            const row = cb.closest('tr');
            selectedItems.push(row.dataset.cartId);
          });

          if (selectedItems.length === 0) {
            alert('Please select at least one item to proceed.');
            return;
          }

          // Redirect to checkout page with selected cart IDs
          const params = new URLSearchParams();
          params.append('cart_ids', selectedItems.join(','));
          window.location.href = `checkout_cart.php?${params.toString()}`;
        });
        </script>

      <?php endif; ?>
    </div>

  </main>

  <!-- Script -->
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const cartBody = document.getElementById('cart-body');
    const totalDisplay = document.getElementById('cart-total');

    const updateTotal = () => {
      let total = 0;
      document.querySelectorAll('.select-item:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const subtotal = parseFloat(row.querySelector('.subtotal').textContent.replace(/[₱,]/g, '')) || 0;
        total += subtotal;
      });
      totalDisplay.textContent = '₱' + total.toFixed(2);
    };

    // --- Select / Deselect All functionality ---
    const selectAllBtn = document.getElementById('select-all-btn');
    let allSelected = true; // initial state

    selectAllBtn.addEventListener('click', () => {
      const checkboxes = document.querySelectorAll('.select-item');
      allSelected = !allSelected; // toggle

      checkboxes.forEach(cb => cb.checked = allSelected);

      selectAllBtn.textContent = allSelected ? 'Deselect All' : 'Select All';
      updateTotal();
    });

    cartBody.addEventListener('click', (e) => {
      const btn = e.target;
      const row = btn.closest('tr');
      if (!row) return;

      const quantityInput = row.querySelector('.quantity');
      const price = parseFloat(row.dataset.price);
      const stock = parseInt(row.dataset.stock);
      let quantity = parseInt(quantityInput.value);

      if (btn.classList.contains('increment')) {
        if (quantity < stock) {
          quantity++;
          quantityInput.value = quantity;
        }
      } else if (btn.classList.contains('decrement')) {
        if (quantity > 1) {
          quantity--;
          quantityInput.value = quantity;
        }
      }

      const subtotalCell = row.querySelector('.subtotal');
      const newSubtotal = price * quantity;
      subtotalCell.textContent = '₱' + newSubtotal.toFixed(2);

      updateTotal();
    });

    document.querySelectorAll('.select-item').forEach(cb => {
      cb.addEventListener('change', updateTotal);
    });

    updateTotal();
  });
  </script>
</body>
</html>
