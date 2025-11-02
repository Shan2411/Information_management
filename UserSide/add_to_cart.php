<?php
include 'db_connect.php';
session_start();

// --- Check if logged in ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['HTTP_REFERER']));
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

if ($product_id <= 0 || $quantity <= 0) {
    header("Location: products.php?error=invalid");
    exit;
}

// --- Get product stock ---
$stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: products.php?error=notfound");
    exit;
}
$product = $result->fetch_assoc();
$available_stock = (int)$product['stock'];

// --- Check if already in cart ---
$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$cart_result = $stmt->get_result();

if ($cart_result->num_rows > 0) {
    $cart_item = $cart_result->fetch_assoc();
    $current_quantity = (int)$cart_item['quantity'];
    $new_total = $current_quantity + $quantity;

    if ($new_total > $available_stock) {
        // prevent exceeding stock
        $response = [
            "success" => false,
            "message" => "⚠️ Not enough stock. Only $available_stock items available."
        ];
    } else {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("iii", $new_total, $user_id, $product_id);
        $stmt->execute();
        $response = ["success" => true, "message" => "🛒 Cart updated successfully!"];
    }

} else {
    if ($quantity > $available_stock) {
        $response = [
            "success" => false,
            "message" => "⚠️ Not enough stock. Only $available_stock items available."
        ];
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
        $response = ["success" => true, "message" => "✅ Added to cart!"];
    }
}

// --- Handle AJAX vs normal redirect ---
if (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    $ref = $_SERVER['HTTP_REFERER'] ?? 'products.php';
    if ($response['success']) {
        $ref .= (str_contains($ref, '?') ? '&' : '?') . 'added=1';
    } else {
        $ref .= (str_contains($ref, '?') ? '&' : '?') . 'error=stock';
    }
    header("Location: $ref");
    exit;
}
?>
