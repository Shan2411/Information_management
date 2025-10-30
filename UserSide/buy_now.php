<?php
include 'db_connect.php'; // change to your connection file

// Validate input
if (!isset($_GET['product_id'])) {
  die("Product not found.");
}

$product_id = $_GET['product_id'];
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

// Fetch product details from database
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
  <title>Buy Now | <?php echo htmlspecialchars($product['product_name']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- HEADER -->
  <header class="bg-[rgb(116,142,159)] text-white sticky top-0 z-50 shadow-md py-1">
    <div class="max-w-6xl mx-auto flex items-center justify-between py-4 px-6">
      <a href="HomepageUser.php" class="text-2xl font-bold">Electronic Device Market</a>
      <a href="#" class="text-2xl hover:text-gray-200">👤</a>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="max-w-5xl mx-auto mt-16 p-6 bg-white shadow-lg rounded-xl flex flex-col md:flex-row gap-10">
    
    <!-- Left: Product Image -->
    <div class="flex-1 flex justify-center items-center">
      <img 
        src="https://i1.sndcdn.com/artworks-YKzQGzw6kpjz4xoL-b6nyFw-t1080x1080.jpg" 
        alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
        class="w-[300px] h-[300px] object-cover rounded-lg shadow-md"
      >
    </div>

    <!-- Right: Product Info -->
    <div class="flex-1 flex flex-col justify-between">
      <div>
        <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h1>
        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
        <p class="text-[rgb(116,142,159)] text-2xl font-bold mb-6">₱<?php echo number_format($product['price']); ?></p>

        <!-- Quantity Selector -->
        <div class="flex items-center mb-6">
          <button id="decrement" class="bg-gray-300 text-gray-800 px-3 py-1 rounded hover:bg-gray-400">−</button>
          <input 
            type="number" 
            id="quantity" 
            value="<?php echo $quantity; ?>" 
            min="1" 
            class="w-16 text-center mx-2 border border-gray-300 rounded"
          >
          <button id="increment" class="bg-gray-300 text-gray-800 px-3 py-1 rounded hover:bg-gray-400">+</button>
        </div>

        <!-- Total -->
        <p class="text-lg font-medium mb-2">Total:</p>
        <p id="totalPrice" class="text-2xl font-semibold text-[rgb(116,142,159)] mb-8">
          ₱<?php echo number_format($product['price'] * $quantity); ?>
        </p>
      </div>

      <!-- Buttons -->
      <div class="flex flex-col sm:flex-row gap-4">
        <button class="bg-gray-800 text-white py-3 rounded hover:bg-gray-700 flex-1">Confirm Purchase</button>
        <a href="HomepageUser.php" class="text-center bg-gray-300 text-gray-800 py-3 rounded hover:bg-gray-400 flex-1">Cancel</a>
      </div>
    </div>
  </main>

  <!-- JS for Quantity -->
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
