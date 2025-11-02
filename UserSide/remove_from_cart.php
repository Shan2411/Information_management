<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: HomepageUser.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
  $cart_id = intval($_POST['cart_id']);
  $user_id = $_SESSION['user_id'];

  $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $cart_id, $user_id);
  $stmt->execute();

  // ✅ Redirect back with success flag
  header("Location: viewcart.php?removed=1");
  exit;
} else {
  header("Location: viewcart.php");
  exit;
}
?>
