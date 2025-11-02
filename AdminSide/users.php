<?php
session_start();
include 'includes/db_connect.php';

$pageTitle = 'Users Management';

// Handle delete
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id = $user_id");
    header("Location: users.php?success=deleted");
    exit();
}

// Fetch all users
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM users";
if ($search) {
    $query .= " WHERE username LIKE '%" . $conn->real_escape_string($search) . "%' OR email LIKE '%" . $conn->real_escape_string($search) . "%'";
}
$query .= " ORDER BY user_id DESC";
$users = $conn->query($query);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="lg:ml-64 pt-20 px-6 pb-6">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Users Management</h2>

        <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>User deleted successfully!
        </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                    placeholder="Search by username or email..." 
                    class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if ($search): ?>
                <a href="users.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Username</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Registered</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium"><?php echo $user['user_id']; ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-semibold"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                                    </div>
                                    <span class="font-medium text-gray-800"><?php echo htmlspecialchars($user['username']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?>
                            </td>
                            <td class="px-6 py-4">
                                <a href="users.php?delete=<?php echo $user['user_id']; ?>" 
                                    onclick="return confirm('Are you sure you want to delete this user?')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

</body>
</html>