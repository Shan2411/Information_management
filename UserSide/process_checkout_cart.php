<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: HomepageUser.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id'];
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $contact_num = $_POST['contact_num'];
  $address = $_POST['address'];
  $total_price = $_POST['total_price'];

  echo "<h2>Checkout Successful!</h2>";
  echo "<p>Total Amount: ₱" . number_format($total_price, 2) . "</p>";
  echo "<p>Thank you for your purchase, $first_name!</p>";
}
?>
