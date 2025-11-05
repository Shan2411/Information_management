<?php
session_start();
include 'db_connect.php'; // <-- make sure this file connects to your database

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Add staff account
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO staff (username, email, password, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $created_at);
    
    if ($stmt->execute()) {
        $message = "✅ Staff account added successfully!";
    } else {
        $message = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}

// Delete staff account
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: staff_accounts.php");
    exit();
}

// Fetch staff accounts
$result = $conn->query("SELECT id, username, email, created_at FROM staff ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Accounts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<?php include 'sidebar.php'; ?>

<div class="ml-0 lg:ml-64 p-8">
    <h1 class="text-2xl font-bold mb-6">Staff Accounts</h1>

    <?php if (isset($message)): ?>
        <div class="mb-4 p-3 rounded-lg 
            <?php echo str_contains($message, '✅') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Add Staff Button -->
    <button onclick="document.getElementById('addModal').classList.remove('hidden')"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
        <i class="fas fa-user-plus mr-2"></i>Add Staff
    </button>

    <!-- Staff Table -->
    <div class="mt-6 overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Username</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Created At</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-3"><?php echo $row['id']; ?></td>
                        <td class="px-4 py-3 font-medium"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="px-4 py-3"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="px-4 py-3 text-gray-500"><?php echo $row['created_at']; ?></td>
                        <td class="px-4 py-3 text-center">
                            <a href="?delete=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this staff?')"
                               class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows === 0): ?>
                    <tr><td colspan="5" class="text-center py-4 text-gray-500">No staff found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Staff Modal -->
<div id="addModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4">Add New Staff</h2>
        <form method="POST">
            <input type="hidden" name="add_staff" value="1">

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Username</label>
                <input type="text" name="username" required class="w-full border px-3 py-2 rounded-lg">
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" required class="w-full border px-3 py-2 rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full border px-3 py-2 rounded-lg">
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
