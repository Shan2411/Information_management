<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: HomepageUser.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Validate required POST fields ---
$required_fields = ['first_name', 'last_name', 'contact_num', 'address', 'city', 'province', 'postal_code', 'payment_method', 'total_price', 'product_id', 'quantity'];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        die("Missing required field: $field");
    }
}

// --- Sanitize user inputs ---
$first_name   = htmlspecialchars($_POST['first_name']);
$last_name    = htmlspecialchars($_POST['last_name']);
$contact_num  = htmlspecialchars($_POST['contact_num']);
$address      = htmlspecialchars($_POST['address']);
$city         = htmlspecialchars($_POST['city']);
$province     = htmlspecialchars($_POST['province']);
$postal_code  = htmlspecialchars($_POST['postal_code']);
$payment_method = htmlspecialchars($_POST['payment_method']);
$total_price  = floatval($_POST['total_price']);
$product_id   = intval($_POST['product_id']);
$quantity     = intval($_POST['quantity']);

// --- Validate contact number and postal code ---
if (!preg_match('/^09\d{9}$/', $contact_num)) {
    die("Invalid Philippine contact number format.");
}
if (!preg_match('/^\d{4}$/', $postal_code)) {
    die("Invalid Philippine postal code format.");
}

// --- Fetch product info ---
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

// --- Insert order into database ---
$order_stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, product_name, price, quantity, total_amount, order_date, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')");
$product_name = $product['product_name'];
$price = $product['price'];
$total_amount = $price * $quantity;
$order_stmt->bind_param("iisddi", $user_id, $product_id, $product_name, $price, $quantity, $total_amount);
$order_stmt->execute();

// ✅ Decrease stock from products table
$update_stock = $conn->prepare("UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE product_id = ?");
$update_stock->bind_param("ii", $quantity, $product_id);
$update_stock->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout Successful</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Soft gradient background for the whole page */
    body {
      background: linear-gradient(135deg, rgba(116,142,159,0.25), rgba(200,210,220,0.6));
    }

    /* Card styling */
    .success-card {
      background-color: #ffffff;
      border-radius: 1rem;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
      padding: 2rem;
      max-width: 480px;
      text-align: center;
      animation: fadeIn 0.8s ease;
    }

    /* Gradient button */
    .gradient-btn {
      background: linear-gradient(90deg, rgba(116,142,159,1), rgba(180,200,215,1));
      transition: all 0.3s ease;
    }
    .gradient-btn:hover {
      background: linear-gradient(90deg, rgba(180,200,215,1), rgba(116,142,159,1));
      transform: scale(1.05);
    }

    /* Fade-in animation */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center">

  <div class="success-card">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">✅ Checkout Successful!</h1>
    <p class="text-gray-700 mb-2">Thank you, <span class="font-semibold"><?= $first_name ?></span>, for your purchase.</p>
    <p class="text-gray-700 mb-2">Product: <span class="font-semibold"><?= htmlspecialchars($product_name) ?></span></p>
    <p class="text-gray-700 mb-2">Quantity: <span class="font-semibold"><?= $quantity ?></span></p>
    <p class="text-gray-800 mb-4 font-semibold">Total Amount: 
      <span class="text-green-600">₱<?= number_format($total_amount, 2) ?></span>
    </p>
    <p class="text-gray-500 mb-6">Your order is now pending and will be processed shortly.</p>

    <div class="flex justify-center gap-4">
      <a href="mainproducts.php" 
         class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Continue Shopping</a>
      <a href="transactions.php" 
         class="gradient-btn px-4 py-2 text-white rounded-lg">View Orders</a>
    </div>
  </div>

</body>
</html>
