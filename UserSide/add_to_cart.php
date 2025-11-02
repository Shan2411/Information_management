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

// --- Validate input ---
if ($product_id <= 0 || $quantity <= 0) {
    header("Location: products.php?error=invalid");
    exit;
}

// --- Check product stock ---
$stmt = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: products.php?error=notfound");
    exit;
}
$product = $result->fetch_assoc();
if ($product['stock'] < $quantity) {
    header("Location: products.php?error=outofstock");
    exit;
}

// --- Check if item already in cart ---
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$cart_result = $stmt->get_result();

if ($cart_result->num_rows > 0) {
    // Update quantity
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt->execute();
} else {
    // Insert new item
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
    $stmt->execute();
}

// --- Redirect back to product page ---
$ref = $_SERVER['HTTP_REFERER'] ?? 'products.php';
$ref .= (str_contains($ref, '?') ? '&' : '?') . 'added=1';
header("Location: $ref");
exit;
