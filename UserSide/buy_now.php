<?php
include 'db_connect.php'; // Database connection

if (!isset($_GET['product_id'])) {
  die("Product not found.");
}

$product_id = intval($_GET['product_id']);
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

// Fetch product details
$query = "SELECT * FROM products WHERE product_id = '$product_id'";
$result = $conn->query($query);

if (!$result || $result->num_rows == 0) {
  die("Product not found.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($product['product_name']); ?> | Buy Now</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- HEADER -->
   <!-- Header -->
  <?php include 'header.php'; ?>

  <!-- PRODUCT DETAILS -->
  <main class="max-w-6xl mx-auto mt-16 bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col md:flex-row">
    
    <!-- Product Image -->
    <div class="md:w-1/2 bg-gray-100 flex items-center justify-center p-8">
      <img 
        src="<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://via.placeholder.com/400'; ?>" 
        alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
        class="rounded-xl w-full max-w-sm object-cover shadow-md hover:scale-105 transition-transform duration-300"
      >
    </div>

    <!-- Product Info -->
    <div class="md:w-1/2 p-10 flex flex-col justify-between">
      <div>
        <h1 class="text-4xl font-bold mb-4 text-[rgb(116,142,159)]">
          <?php echo htmlspecialchars($product['product_name']); ?>
        </h1>
        <p class="text-gray-600 mb-6 leading-relaxed">
          <?php echo htmlspecialchars($product['description']); ?>
        </p>

        <!-- Extra info (category, stock, etc.) -->
        <div class="space-y-2 mb-6 text-sm text-gray-700">
          <?php if (!empty($product['category'])): ?>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
          <?php endif; ?>
          <?php if (isset($product['stock'])): ?>
            <p><strong>Stock Available:</strong> 
              <span class="<?php echo $product['stock'] > 0 ? 'text-green-600' : 'text-red-500'; ?>">
                <?php echo $product['stock'] > 0 ? $product['stock'] . ' units' : 'Out of stock'; ?>
              </span>
            </p>
          <?php endif; ?>
        </div>

        <p class="text-3xl font-semibold text-[rgb(116,142,159)] mb-8">
          ₱<?php echo number_format($product['price']); ?>
        </p>

        <!-- Quantity Selector -->
        <div class="flex items-center mb-6">
          <button id="decrement" class="bg-gray-200 text-gray-800 px-3 py-1 rounded-l hover:bg-gray-300">−</button>
          <input 
            type="number" 
            id="quantity" 
            value="<?php echo $quantity; ?>" 
            min="1" 
            class="w-16 text-center border-t border-b border-gray-300 focus:outline-none"
          >
          <button id="increment" class="bg-gray-200 text-gray-800 px-3 py-1 rounded-r hover:bg-gray-300">+</button>
        </div>

        <!-- Total -->
        <p class="text-lg font-medium mb-1">Total:</p>
        <p id="totalPrice" class="text-2xl font-semibold text-[rgb(116,142,159)] mb-8">
          ₱<?php echo number_format($product['price'] * $quantity); ?>
        </p>
      </div>

      <!-- Buttons -->
      <div class="flex flex-col sm:flex-row gap-4">
        <button 
          class="bg-[rgb(116,142,159)] text-white py-3 rounded-lg hover:bg-[rgb(100,123,136)] flex-1 transition"
        >
          Confirm Purchase
        </button>
        <a 
          href="mainproducts.php" 
          class="text-center bg-gray-200 text-gray-800 py-3 rounded-lg hover:bg-gray-300 flex-1 transition"
        >
          Cancel
        </a>
      </div>
    </div>
  </main>

  <!-- JS for Quantity & Total -->
  <script>
    const price = <?php echo $product['price']; ?>;
    const quantityInput = document.getElementById('quantity');
    const totalPrice = document.getElementById('totalPrice');

    document.getElementById('increment').addEventListener('click', () => {
      quantityInput.value = parseInt(quantityInput.value) + 1;
      updateTotal();
    });

    document.getElementById('decrement').addEventListener('click', () => {
      if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
        updateTotal();
      }
    });

    quantityInput.addEventListener('input', updateTotal);

    function updateTotal() {
      const total = price * parseInt(quantityInput.value);
      totalPrice.textContent = "₱" + total.toLocaleString();
    }
  </script>

</body>
</html>
