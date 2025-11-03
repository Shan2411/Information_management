<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: HomepageUser.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// --- Validate required POST fields ---
$required_fields = ['first_name', 'last_name', 'contact_num', 'address', 'city', 'province', 'postal_code', 'payment_method', 'total_price', 'cart_ids', 'product_ids', 'quantities', 'prices'];
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

// --- Validate contact number and postal code ---
if (!preg_match('/^09\d{9}$/', $contact_num)) {
    die("Invalid Philippine contact number format.");
}
if (!preg_match('/^\d{4}$/', $postal_code)) {
    die("Invalid Philippine postal code format.");
}

// --- Fetch arrays of cart info ---
$cart_ids    = $_POST['cart_ids'];
$product_ids = $_POST['product_ids'];
$quantities  = $_POST['quantities'];
$prices      = $_POST['prices'];

if (count($cart_ids) !== count($product_ids) || count($cart_ids) !== count($quantities)) {
    die("Cart data mismatch.");
}

// --- Insert each item into orders table ---
$order_stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, product_name, price, quantity, total_amount, order_date, status)
                              VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')");

for ($i = 0; $i < count($cart_ids); $i++) {
    $product_id   = intval($product_ids[$i]);
    $quantity     = intval($quantities[$i]);
    $price        = floatval($prices[$i]);

    // Fetch product name for this ID
    $pstmt = $conn->prepare("SELECT product_name FROM products WHERE product_id = ?");
    $pstmt->bind_param("i", $product_id);
    $pstmt->execute();
    $presult = $pstmt->get_result();
    $product = $presult->fetch_assoc();
    $product_name = $product['product_name'] ?? 'Unknown';

    $total_amount = $price * $quantity;

    $order_stmt->bind_param("iisddi", $user_id, $product_id, $product_name, $price, $quantity, $total_amount);
    $order_stmt->execute();

    // Remove item from cart
    $del_stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
    $del_stmt->bind_param("i", $cart_ids[$i]);
    $del_stmt->execute();

    // ✅ Decrease stock
    $update_stock = $conn->prepare("UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE product_id = ?");
    $update_stock->bind_param("ii", $quantity, $product_id);
    $update_stock->execute();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout Successful</title>
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, rgb(116,142,159), rgb(189,203,212));
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 420px;
    }

    h2 {
        color: rgb(70, 88, 99);
        margin-bottom: 10px;
    }

    p {
        color: #555;
        line-height: 1.6;
    }

    .amount {
        font-weight: bold;
        color: rgb(60, 80, 90);
        font-size: 1.2em;
        margin: 10px 0 20px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        margin: 10px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s ease;
        text-decoration: none;
    }

    .btn-shop {
        background-color: rgb(116,142,159);
        color: #fff;
    }

    .btn-orders {
        background-color: #e5e8eb;
        color: rgb(70, 88, 99);
    }

    .btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
    }
</style>
</head>
<body>

<div class="card">
    <h2>Checkout Successful!</h2>
    <p>Thank you, <strong><?php echo $first_name; ?></strong>, for your purchase.</p>
    <p class="amount">Total Amount: ₱<?php echo number_format($total_price, 2); ?></p>
    <p>Your orders are now <strong>pending</strong> and will be processed shortly.</p>
    <a href="mainproducts.php" class="btn btn-shop">Continue Shopping</a>
    <a href="transactions.php" class="btn btn-orders">View Orders</a>
</div>

</body>
</html>
