<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: HomepageUser.php");
    exit;
}

// Check if cart_id is provided
if (!isset($_POST['cart_id'])) {
    header("Location: view_cart.php");
    exit;
}

$cart_id = (int)$_POST['cart_id'];
$user_id = $_SESSION['user_id'];

// Delete item from cart, but only if it belongs to the logged-in user
$stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

// Redirect back to cart page
header("Location: view_cart.php");
exit;
?>
