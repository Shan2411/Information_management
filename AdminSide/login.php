
<?php
session_start();
include 'includes/db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: AdminDashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Fetch admin/staff account
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Validate password (hashed or plain for legacy)
        if (password_verify($password, $admin['password']) || $password === $admin['password']) {
            // Store session data
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['role'] = $admin['role'] ?? 'admin';

            // Redirect based on role
            if ($admin['role'] === 'staff') {
                header("Location: StaffDashboard.php");
            } else {
                header("Location: AdminDashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Account not found.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Information Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-2xl shadow-2xl w-[420px] max-w-[90%] p-8">
        <div class="text-center mb-8">
            <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-shield text-blue-600 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800">Admin / Staff Login</h2>
            <p class="text-gray-500 mt-2">Sign in to manage the system</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Username</label>
                <input type="text" name="username" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your username">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your password">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                Login
            </button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-6">
            © 2025 Information Management System
        </p>
    </div>
</body>
</html>