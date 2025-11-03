<?php
include 'db_connect.php';
session_start();
include 'header.php'; 
// Get product ID from URL
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($product_id <= 0) {
    die("Product not found.");
}

// Fetch product from DB
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();
$stock = (int)$product['stock'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['product_name']) ?> | Product Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">


    <section class="max-w-6xl mx-auto mt-4 p-4 bg-white rounded-xl shadow-md flex flex-col md:flex-row gap-6 min-h-[90vh]">

            
            <!-- FOR ADD TO CSRT-->
            <?php if (isset($_GET['added'])): ?>
                <div class="max-w-6xl mx-auto mt-4" style = "position:absolute">
                    <div class="bg-green-100 text-green-700 border border-green-300 p-3 rounded-md flex items-center justify-between">
                        <span>✅ Product added to cart!</span>
                        <a href="cart.php" class="text-blue-600 underline hover:text-blue-800">View Cart</a>
                    </div>
                </div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'outofstock'): ?>
                <div class="max-w-6xl mx-auto mt-4" style = "position:absolute">
                    <div class="bg-red-100 text-red-700 border border-red-300 p-3 rounded-md">
                        ⚠️ Sorry, not enough stock available.
                    </div>
                </div>
            <?php endif; ?>


        <!-- Left Panel: Image + Info -->
        <div class="md:w-1/3 flex flex-col gap-2 h-full">
            <img src="<?= htmlspecialchars($product['image_path'] ?? 'https://via.placeholder.com/300') ?>" 
                alt="<?= htmlspecialchars($product['product_name']) ?>" 
                class="w-full h-72 md:h-full object-cover rounded-lg shadow-sm">

            <div class="mt-2 text-sm text-gray-600 space-y-1">
                <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
                <p><strong>Stock:</strong> <?= $stock ?></p>
                <p><strong>Sold:</strong> <?= (int)$product['sold_count'] ?></p>
                <p><strong>Listed:</strong> <?= htmlspecialchars($product['date_listed'] ?? 'N/A') ?></p>
            </div>
        </div>

        <!-- Right Panel: Details -->
        <div class="flex-1 flex flex-col h-full">
            <!-- Header & Price -->
            <div class="mb-3">
                <h2 class="text-2xl md:text-3xl font-bold mb-2"><?= htmlspecialchars($product['product_name']) ?></h2>
                <p class="text-red-600 text-xl md:text-2xl font-semibold">₱<?= number_format($product['price'], 2) ?></p>
            </div>

            <!-- Description -->
            <div class="border border-dashed border-gray-300 p-3 rounded-lg overflow-y-auto h-80% text-sm md:text-base mb-3" style="padding-bottom: 10%; padding-top: 5%; margin-bottom: 10%; margin-top: 10%">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>

            <!-- Quantity Selector + Total -->
            <div class="mb-4">
            <?php if($stock > 0): ?>
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-3">
                <button id="decrement" class="bg-gray-300 text-gray-800 px-3 py-1 rounded hover:bg-gray-400">−</button>
                <input type="number" id="quantity" value="1" min="1" max="<?= $stock ?>" 
                class="w-16 text-center border border-gray-300 rounded bg-gray-100 cursor-default" 
                readonly>
                <button id="increment" class="bg-gray-300 text-gray-800 px-3 py-1 rounded hover:bg-gray-400">+</button>
                </div>

                <!-- Total Price -->
                <p class="text-lg font-semibold text-gray-700">
                Total: <span id="totalPrice" class="text-red-600">₱<?= number_format($product['price'], 2) ?></span>
                </p>
            </div>
            <?php else: ?>
            <p class="text-red-600 font-semibold text-lg">Out of Stock</p>
            <?php endif; ?>
            </div>


            <!-- Action Buttons -->
            <div class="mt-auto flex gap-3">
                <a href="<?= $stock>0 ? "add_to_cart.php?product_id={$product['product_id']}&quantity=1" : "#" ?>" 
                   id="addToCartBtn"
                   class="flex-1 bg-[rgb(116,142,159)] text-white py-2 rounded-md text-center hover:bg-[rgb(100,123,136)] transition action-btn <?= $stock===0 ? 'opacity-50 pointer-events-none' : '' ?>">
                    Add to Cart
                </a>
                <a href="<?= $stock>0 ? "buy_now.php?product_id={$product['product_id']}&quantity=1" : "#" ?>" 
                   id="buyNowBtn"
                   class="flex-1 bg-gray-800 text-white py-2 rounded-md text-center hover:bg-gray-700 transition action-btn <?= $stock===0 ? 'opacity-50 pointer-events-none' : '' ?>">
                    Buy Now
                </a>
            </div>
        </div>

    </section>


    <script>
document.addEventListener('DOMContentLoaded', () => {
    const loggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    const stock = <?= $stock ?>;

    const addBtn = document.getElementById('addToCartBtn');
    const buyBtn = document.getElementById('buyNowBtn');
    const quantityInput = document.getElementById('quantity');

    const incrementBtn = document.getElementById('increment');
    const decrementBtn = document.getElementById('decrement');
    const totalPrice = document.getElementById('totalPrice');
    const pricePerItem = <?= $product['price'] ?>;

    // Handle increment/decrement
    if (incrementBtn && decrementBtn && quantityInput) {
        incrementBtn.addEventListener('click', () => {
            let value = parseInt(quantityInput.value);
            if (value < stock) {
                value++;
                quantityInput.value = value;
                updateTotal();
            }
        });

        decrementBtn.addEventListener('click', () => {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                value--;
                quantityInput.value = value;
                updateTotal();
            }
        });
    }

    // Update total price
    function updateTotal() {
        const qty = parseInt(quantityInput.value) || 1;
        totalPrice.textContent = `₱${(pricePerItem * qty).toFixed(2)}`;
    }

    // Update total price when manually changing input
    quantityInput.addEventListener('input', updateTotal);


    // Update links dynamically
    function getQuantity() {
        return parseInt(quantityInput?.value || 1);
    }

    // Add to Cart (AJAX)
    if (addBtn) {
        addBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            if (!loggedIn) {
                openAuthModal();
                return;
            }

            if (stock <= 0) {
                alert("Sorry, this product is out of stock.");
                return;
            }

            const quantity = getQuantity();

            try {
                const response = await fetch(
                    `add_to_cart.php?product_id=<?= $product['product_id'] ?>&quantity=${quantity}`,
                    {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }
                );

                const data = await response.json();
                if (data.success) {
                    alert("✅ " + data.message);
                } else {
                    alert("❌ " + data.message);
                }
            } catch (error) {
                alert("⚠️ Failed to add to cart. Please try again.");
                console.error(error);
            }
        });
    }


    // Buy Now (check login and redirect)
if (buyBtn) {
    buyBtn.addEventListener('click', (e) => {
        e.preventDefault();

        // Check if user is logged in
        if (!loggedIn) {
            openAuthModal(); // show login modal
            return;
        }

        const quantity = parseInt(quantityInput.value) || 1;

        if (quantity > stock) {
            alert("⚠️ Quantity exceeds stock.");
            quantityInput.value = stock;
            return;
        }

        // Redirect to checkout.php with selected quantity
        window.location.href = `checkout.php?product_id=<?= $product['product_id'] ?>&quantity=${quantity}`;
    });
}

});
</script>



</body>
</html>
