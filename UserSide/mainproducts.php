<?php
include 'db_connect.php';
session_start();

// Handle filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;

// Build SQL query dynamically
$query = "SELECT * FROM products WHERE 1";
$params = [];
$types = "";

if (!empty($search)) {
  $query .= " AND product_name LIKE ?";
  $params[] = "%$search%";
  $types .= "s";
}

if (!empty($category)) {
  $query .= " AND category = ?";
  $params[] = $category;
  $types .= "s";
}

$query .= " AND price BETWEEN ? AND ?";
$params[] = $min_price;
$params[] = $max_price;
$types .= "dd";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Products | Electronic Device Market</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <!-- HEADER (Matches Homepage) -->
  <header class="bg-[rgb(116,142,159)] text-white sticky top-0 z-50 shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">

      <!-- Logo -->
      <a href="HomepageUser.php" class="text-2xl font-bold tracking-wide hover:opacity-90 transition">
        Electronic Device Market
      </a>

      <!-- Nav Links -->
      <nav class="hidden md:flex items-center space-x-8 text-[15px] font-medium">
        <a href="HomepageUser.php" class="hover:text-gray-200 transition">Home</a>
        <a href="products.php" class="font-semibold underline">Products</a>
        <a href="#contact" class="hover:text-gray-200 transition">Contact</a>
      </nav>

      <!-- Profile / Login -->
      <div class="flex items-center space-x-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="profile.php" 
             class="flex items-center gap-2 bg-white/15 hover:bg-white/25 px-3 py-2 rounded-full transition">
            <span class="text-xl">👤</span>
            <span class="hidden sm:inline text-sm font-semibold">Profile</span>
          </a>
        <?php else: ?>
          <button 
            id="openLoginPopup" 
            class="flex items-center gap-2 bg-white/15 hover:bg-white/25 px-3 py-2 rounded-full transition focus:outline-none">
            <span class="text-xl">👤</span>
            <span class="hidden sm:inline text-sm font-semibold">Login</span>
          </button>
        <?php endif; ?>

        <!-- Mobile Menu Button -->
        <button id="menuBtn" class="md:hidden text-2xl focus:outline-none hover:opacity-80">☰</button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <nav id="mobileNav" class="hidden flex-col bg-[rgb(106,132,149)] text-white md:hidden">
      <a href="HomepageUser.php" class="py-3 px-6 hover:bg-[rgb(96,122,139)] transition">Home</a>
      <a href="products.php" class="py-3 px-6 bg-[rgb(96,122,139)] transition">Products</a>
      <a href="#contact" class="py-3 px-6 hover:bg-[rgb(96,122,139)] transition">Contact</a>
    </nav>

    <script>
      const menuBtn = document.getElementById('menuBtn');
      const mobileNav = document.getElementById('mobileNav');
      if (menuBtn && mobileNav) {
        menuBtn.addEventListener('click', () => {
          mobileNav.classList.toggle('hidden');
        });
      }
    </script>
  </header>

  <!-- FILTER SECTION -->
  <section class="max-w-7xl mx-auto mt-10 bg-white p-6 rounded-2xl shadow-lg">
    <form method="GET" class="flex flex-col md:flex-row md:items-center md:space-x-6 space-y-4 md:space-y-0">

      <!-- Search -->
      <div class="flex-1">
        <input 
          type="text" 
          name="search" 
          value="<?= htmlspecialchars($search) ?>" 
          placeholder="Search products..."
          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-[rgb(116,142,159)] focus:outline-none"
        >
      </div>

      <!-- Category -->
      <select 
        name="category" 
        class="w-full md:w-1/4 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[rgb(116,142,159)] focus:outline-none"
      >
        <option value="">All Categories</option>
        <?php
        $cat_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
        while ($cat = $cat_result->fetch_assoc()) {
          $selected = ($cat['category'] === $category) ? 'selected' : '';
          echo "<option value='{$cat['category']}' $selected>{$cat['category']}</option>";
        }
        ?>
      </select>

      <!-- Price Range -->
      <div class="flex items-center space-x-2">
        <input 
          type="number" 
          name="min_price" 
          value="<?= htmlspecialchars($min_price) ?>" 
          placeholder="Min ₱" 
          class="w-24 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-[rgb(116,142,159)] focus:outline-none"
        >
        <span>–</span>
        <input 
          type="number" 
          name="max_price" 
          value="<?= htmlspecialchars($max_price) ?>" 
          placeholder="Max ₱" 
          class="w-24 border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-[rgb(116,142,159)] focus:outline-none"
        >
      </div>

      <!-- Apply Button -->
      <button 
        type="submit" 
        class="bg-[rgb(116,142,159)] text-white px-5 py-2 rounded-lg hover:bg-[rgb(100,123,136)] transition">
        Apply Filters
      </button>
    </form>
  </section>

  <!-- PRODUCT GRID -->
  <section class="max-w-7xl mx-auto mt-10 px-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="bg-white shadow-md rounded-xl overflow-hidden hover:shadow-xl transition duration-300">
          <img 
            src="<?= htmlspecialchars($row['image_path'] ?? 'https://i1.sndcdn.com/artworks-YKzQGzw6kpjz4xoL-b6nyFw-t1080x1080.jpg') ?>" 
            alt="<?= htmlspecialchars($row['product_name']) ?>" 
            class="w-full h-48 object-cover"
          >
          <div class="p-5 flex flex-col justify-between h-[250px]">
            <div>
              <h3 class="font-semibold text-lg mb-1"><?= htmlspecialchars($row['product_name']) ?></h3>
              <p class="text-gray-600 text-sm line-clamp-2 mb-2"><?= htmlspecialchars($row['description']) ?></p>
              <p class="font-bold text-[rgb(116,142,159)] mb-3">₱<?= number_format($row['price'], 2) ?></p>
            </div>
            <div class="flex gap-2">
              <button 
                class="addToCart bg-[rgb(116,142,159)] text-white py-2 px-3 rounded-lg hover:bg-[rgb(100,123,136)] flex-1 transition"
                data-product_id="<?= $row['product_id'] ?>"
                data-name="<?= htmlspecialchars($row['product_name']) ?>"
                data-price="<?= $row['price'] ?>"
              >
                Add to Cart
              </button>
              <button 
                class="buyNow bg-gray-800 text-white py-2 px-3 rounded-lg hover:bg-gray-700 flex-1 transition"
                data-product_id="<?= $row['product_id'] ?>"
                data-name="<?= htmlspecialchars($row['product_name']) ?>"
                data-price="<?= $row['price'] ?>"
              >
                Buy Now
              </button>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="col-span-full text-center text-gray-600 text-lg mt-10">No products found.</p>
    <?php endif; ?>
  </section>

  <script>
    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
    document.querySelectorAll(".addToCart, .buyNow").forEach(btn => {
      btn.addEventListener("click", (e) => {
        const product_id = btn.dataset.product_id;
        const name = btn.dataset.name;
        const price = btn.dataset.price;
        const quantity = 1;

        if (!isLoggedIn) {
          e.preventDefault();
          openAuthModal(); // same popup as homepage
          return;
        }

        if (btn.classList.contains("buyNow")) {
          window.location.href = `buy_now.php?product_id=${product_id}&name=${encodeURIComponent(name)}&price=${price}&quantity=${quantity}`;
        } else {
          window.location.href = `add_to_cart.php?product_id=${product_id}&name=${encodeURIComponent(name)}&price=${price}&quantity=${quantity}`;
        }
      });
    });
  </script>
</body>
</html>
