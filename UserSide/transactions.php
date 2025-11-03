<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: HomepageUser.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// --- Get selected filter ---
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// --- Query based on filter ---
if ($status_filter === 'all') {
  $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
  $stmt->bind_param("i", $user_id);
} else {
  $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND status = ? ORDER BY order_date DESC");
  $stmt->bind_param("is", $user_id, $status_filter);
}

$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Transactions</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Gradient background */
    body {
      background: linear-gradient(135deg, #d7e0e7, #b7c7d3, #748e9f);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    /* Scrollable table area */
    .table-container {
      max-height: 500px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #748e9f #f0f0f0;
    }

    .table-container::-webkit-scrollbar {
      width: 8px;
    }
    .table-container::-webkit-scrollbar-thumb {
      background-color: #748e9f;
      border-radius: 10px;
    }
    .table-container::-webkit-scrollbar-track {
      background: #f0f0f0;
    }
  </style>
</head>
<body class="text-gray-800">

<!-- ✅ Make header full width -->
<div class="w-full">
  <?php include 'header.php'; ?>
</div>

<!-- Main Content -->
<div class="w-full max-w-6xl p-6 mt-8">
  <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">My Transactions</h1>

  <!-- Filter Buttons -->
  <div class="flex flex-wrap justify-center gap-3 mb-6">
    <?php
      $filters = ['all' => 'All', 'pending' => 'Pending', 'shipped' => 'Shipped', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
      foreach ($filters as $value => $label):
        $isActive = ($status_filter === $value);
    ?>
      <a href="?status=<?= $value ?>"
         class="px-4 py-1.5 rounded-full text-sm font-medium border transition
                <?= $isActive ? 'bg-[#748e9f] text-white border-[#748e9f]' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100' ?>">
        <?= $label ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- Table Card -->
  <div class="bg-white shadow-lg rounded-xl p-5">
    <?php if ($orders->num_rows > 0): ?>
      <div class="table-container rounded-lg border border-gray-200">
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="bg-gray-100 text-gray-800 uppercase text-xs sticky top-0 z-10">
            <tr>
              <th class="px-4 py-3">Product</th>
              <th class="px-4 py-3 text-center">Quantity</th>
              <th class="px-4 py-3 text-right">Price</th>
              <th class="px-4 py-3 text-right">Total</th>
              <th class="px-4 py-3 text-center">Status</th>
              <th class="px-4 py-3 text-center">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
              <tr class="border-t hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($order['product_name']) ?></td>
                <td class="px-4 py-3 text-center"><?= htmlspecialchars($order['quantity']) ?></td>
                <td class="px-4 py-3 text-right">₱<?= number_format($order['price'], 2) ?></td>
                <td class="px-4 py-3 text-right text-green-700 font-semibold">₱<?= number_format($order['total_amount'], 2) ?></td>
                <td class="px-4 py-3 text-center">
                  <?php
                    $status = htmlspecialchars($order['status']);
                    $statusColor = match ($status) {
                      'pending' => 'bg-yellow-200 text-yellow-800',
                      'shipped' => 'bg-blue-200 text-blue-800',
                      'completed' => 'bg-green-200 text-green-800',
                      'cancelled' => 'bg-red-200 text-red-800',
                      default => 'bg-gray-200 text-gray-700',
                    };
                  ?>
                  <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $statusColor ?>">
                    <?= ucfirst($status) ?>
                  </span>
                </td>
                <td class="px-4 py-3 text-center text-gray-600">
                  <?= date("M d, Y h:i A", strtotime($order['order_date'])) ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-600 py-10">
        No <?= $status_filter === 'all' ? '' : ucfirst($status_filter) ?> transactions found.
      </p>
    <?php endif; ?>
  </div>

  <!-- Bottom Buttons -->
  <div class="flex justify-center mt-8 gap-4">
    <a href="mainproducts.php"
       class="px-5 py-2 rounded-lg bg-[#748e9f] text-white font-medium hover:bg-[#5f788a] transition">
      Continue Shopping
    </a>
  </div>
</div>

</body>
</html>
