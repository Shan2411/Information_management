<?php
session_start();
include 'includes/db_connect.php';

$pageTitle = 'Orders Management';

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $conn->real_escape_string($_POST['status']);

    // Fetch current status
    $result = $conn->query("SELECT status FROM orders WHERE order_id = $order_id");
    if ($result && $row = $result->fetch_assoc()) {
        $current_status = $row['status'];

        $allowed_transitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => []
        ];

        // Check if transition is allowed
        if (in_array($new_status, $allowed_transitions[$current_status])) {
            $conn->query("UPDATE orders SET status='$new_status' WHERE order_id=$order_id");
            header("Location: orders.php?success=updated");
            exit();
        } else {
            header("Location: orders.php?error=invalid_transition");
            exit();
        }
    }
}

// Fetch all orders
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$query = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.user_id";
if ($filter) {
    $query .= " WHERE o.status = '" . $conn->real_escape_string($filter) . "'";
}
$query .= " ORDER BY o.order_date DESC";
$orders = $conn->query($query);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="lg:ml-64 pt-20 px-6 pb-6">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Orders Management</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>Order status updated successfully!
            </div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'invalid_transition'): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6">
                <i class="fas fa-times-circle mr-2"></i>Invalid status change! You cannot move backward in the process.
            </div>
        <?php endif; ?>

        <!-- Filter Buttons -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex flex-wrap gap-3">
                <?php
                $statuses = [
                    '' => 'All Orders',
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled'
                ];
                foreach ($statuses as $value => $label):
                    $active = ($filter == $value) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300';
                ?>
                    <a href="orders.php?filter=<?php echo $value; ?>" class="px-4 py-2 rounded-lg <?php echo $active; ?> transition">
                        <?php echo $label; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Order ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Customer</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Total</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-semibold">#<?php echo $order['order_id']; ?></td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($order['username']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($order['email']); ?></p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    ₱<?php echo number_format($order['total_amount'], 2); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs rounded-full font-semibold
                                        <?php 
                                        echo $order['status'] == 'completed' ? 'bg-green-100 text-green-700' : 
                                            ($order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                            ($order['status'] == 'processing' ? 'bg-blue-100 text-blue-700' : 
                                            'bg-red-100 text-red-700'));
                                        ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    // Determine allowed transitions dynamically
                                    $allowed_transitions = [
                                        'pending' => ['processing', 'cancelled'],
                                        'processing' => ['completed', 'cancelled'],
                                        'completed' => [],
                                        'cancelled' => []
                                    ];
                                    $allowed = $allowed_transitions[$order['status']];
                                    ?>

                                    <?php if (!empty($allowed)): ?>
                                        <form method="POST" class="flex gap-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <select name="status"
                                                class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <?php foreach ($allowed as $status_option): ?>
                                                    <option value="<?php echo $status_option; ?>">
                                                        <?php echo ucfirst($status_option); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="update_status"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition">
                                                Update
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm italic">No further actions</span>
                                    <?php endif; ?>
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
