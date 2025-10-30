<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $db_pass = $row['password'];

        // If password is hashed (new users)
        if (password_verify($password, $db_pass)) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            echo json_encode(['success' => true]);
            header("Location: homepageUser.php");
            exit();
        }
        // If password is still plain (old users)
        elseif ($password === $db_pass) {
            // Auto-upgrade password to hashed for future logins
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $newHash, $row['user_id']);
            $update->execute();

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            echo json_encode(['success' => true]);
            header("Location: homepageUser.php");
            exit();
        }
        else {
            echo json_encode(['success' => false, 'message' => 'Invalid password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }

    $stmt->close();
}
?>
