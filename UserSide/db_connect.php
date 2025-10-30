<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "information_management"; // change to your actual DB name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>