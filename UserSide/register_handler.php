<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    // Check if username already exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists.']);
        exit;
    }

    $check->close();

    // Insert new user
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password, created_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $username, $hashed);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed.']);
    }

    $stmt->close();
}
?>
