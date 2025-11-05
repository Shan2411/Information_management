<?php
include 'includes/db_connect.php';

$order_id = (int)$_GET['order_id'];

$query = "
    SELECT p.product_name, p.price, oi.quantity, oi.subtotal 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = $order_id
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo '<table class="w-full text-sm">';
    echo '<thead><tr class="border-b">
            <th class="py-2 text-left">Product</th>
            <th class="py-2 text-left">Qty</th>
            <th class="py-2 text-left">Price</th>
            <th class="py-2 text-left">Subtotal</th>
          </tr></thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr class="border-b hover:bg-gray-50">
                <td class="py-2">'.htmlspecialchars($row['product_name']).'</td>
                <td class="py-2">'.$row['quantity'].'</td>
                <td class="py-2">₱'.number_format($row['price'], 2).'</td>
                <td class="py-2 font-semibold">₱'.number_format($row['subtotal'], 2).'</td>
              </tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p class="text-gray-500">No items found for this order.</p>';
}
?>

