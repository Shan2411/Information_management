
<!--DO NOT REMOVE URL: http://localhost/HTML-IN-IM/UserSide/HomepageUser.php
reference puede gamitin: https://www.ram-koenig.de/Registrieren

-->

<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
include 'db_connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ElectroHub | Products</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="Homestyle.css">
  <style>
    .scroll-container {
      scroll-behavior: smooth;
    }
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
  </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <?php include 'header.php'; ?>



  <!-- HERO -->
  <section class="bg-[rgb(116,142,159)] text-white py-20 text-center" id = "home">
    <h2 class="text-4xl font-bold mb-4">Shop the Latest Electronics</h2>
    <p class="text-lg mb-6">Smart devices, powerful laptops, and next-gen gadgets.</p>
    <a href="mainproducts.php" class="bg-white text-[rgb(116,142,159)] px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">Shop Now</a>
  </section>


<!-- FEATURED PRODUCTS -->
<section id="products" class="max-w-6xl mx-auto py-16 px-6 relative">
  <h3 class="text-3xl font-bold text-center mb-10">Our Top Selling Products</h3>

  <?php
  $result = $conn->query("SELECT * FROM products");
  $product_count = $result ? $result->num_rows : 0;
  ?>

  <?php if ($product_count > 3): ?>
    <!-- Arrows -->
    <button id="prevBtn"
      class="absolute left-[-25px] top-1/2 -translate-y-1/2 bg-[rgb(116,142,159)] text-white p-3 rounded-full shadow-lg hover:bg-[rgb(100,123,136)] z-50">
      &#10094;
    </button>
    <button id="nextBtn"
      class="absolute right-[-25px] top-1/2 -translate-y-1/2 bg-[rgb(116,142,159)] text-white p-3 rounded-full shadow-lg hover:bg-[rgb(100,123,136)] z-50">
      &#10095;
    </button>
  <?php endif; ?>

  <!-- Scrollable container -->
  <div id="productSlider" class="flex space-x-6 overflow-x-auto no-scrollbar scroll-container px-2 scroll-smooth">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="w-[300px] bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition flex-shrink-0 flex flex-col">
        <img src="<?= htmlspecialchars($row['image_path'] ?? 'https://i1.sndcdn.com/artworks-YKzQGzw6kpjz4xoL-b6nyFw-t1080x1080.jpg') ?>" class="w-full h-48 object-cover" alt="<?= htmlspecialchars($row['product_name']) ?>">
        <div class="p-5 flex flex-col justify-between flex-1">
          <div>
            <h4 class="font-semibold text-xl mb-1"><?= htmlspecialchars($row['product_name']) ?></h4>
            <p class="text-gray-600 mb-2 text-sm line-clamp-2"><?= htmlspecialchars($row['description']) ?></p>
            <p class="text-gray-500 text-sm mb-1">Stock available: <?= (int)$row['stock'] ?></p>
            <p class="text-gray-500 text-sm mb-2">Items sold: <?= (int)$row['sold_count'] ?></p>
            <p class="font-bold text-[rgb(116,142,159)] mb-3">₱<?= number_format((float)$row['price'], 2) ?></p>
          </div>

          <!-- View Details Button -->
          <a href="product_details.php?product_id=<?= (int)$row['product_id'] ?>" 
             class="text-center bg-[rgb(116,142,159)] text-white py-2 px-3 rounded-lg hover:bg-[rgb(100,123,136)] transition mt-3">
            View Details
          </a>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>



<!-- Login/Register Popup -->
<div id="authModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[999]">
  <div class="bg-white rounded-2xl shadow-lg w-[400px] max-w-[90%] p-8 relative transition-all duration-300 scale-95 opacity-0" id="authBox">
    <button id="closeAuthModal" class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
    
    <!-- LOGIN FORM -->
    <div id="loginForm">
      <h2 class="text-2xl font-bold text-center mb-6 text-[rgb(116,142,159)]">Welcome Back</h2>
      <form method="POST" action="login_handler.php" class="space-y-4">
        <input type="text" name="username" placeholder="Username" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
        <input type="password" name="password" placeholder="Password" required class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">
        <button type="submit" class="w-full bg-[rgb(116,142,159)] text-white py-2 rounded-lg hover:bg-[rgb(100,123,136)] transition">Login</button>
      </form>
      <p class="text-center mt-4 text-sm">Don’t have an account? 
        <a href="#" id="showRegister" class="text-[rgb(116,142,159)] font-semibold hover:underline">Register here</a>
      </p>
    </div>

      <!-- REGISTER FORM -->
    <div id="registerForm" class="hidden">
      <h2 class="text-2xl font-bold text-center mb-6 text-[rgb(116,142,159)]">Create Account</h2>
      <form method="POST" action="register_handler.php" class="space-y-4">
        <input type="text" name="username" placeholder="Username" required 
          class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">

        <input type="email" name="email" placeholder="Email" required 
          class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">

        <input type="password" name="password" placeholder="Password" required 
          class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">

        <!-- ✅ Added confirm password -->
        <input type="password" name="confirm_password" placeholder="Confirm Password" required 
          class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[rgb(116,142,159)]">

        <button type="submit" 
          class="w-full bg-[rgb(116,142,159)] text-white py-2 rounded-lg hover:bg-[rgb(100,123,136)] transition">
          Register
        </button>
      </form>

      <p class="text-center mt-4 text-sm">
        Already have an account? 
        <a href="#" id="showLogin" class="text-[rgb(116,142,159)] font-semibold hover:underline">Login here</a>
      </p>
    </div>

  </div>
</div>

<script>
const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
const modal = document.getElementById("authModal");
const authBox = document.getElementById("authBox");
const closeModal = document.getElementById("closeAuthModal");
const showRegister = document.getElementById("showRegister");
const showLogin = document.getElementById("showLogin");
const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");
const loginBtn = document.getElementById('openLoginPopup');

// Open modal with animation
function openAuthModal() {
  modal.classList.remove("hidden");
  setTimeout(() => {
    authBox.classList.remove("scale-95", "opacity-0");
    authBox.classList.add("scale-100", "opacity-100");
  }, 50);
}

// Close modal
function closeAuthModal() {
  authBox.classList.add("scale-95", "opacity-0");
  authBox.classList.remove("scale-100", "opacity-100");
  setTimeout(() => modal.classList.add("hidden"), 200);
}

closeModal.addEventListener("click", closeAuthModal);
modal.addEventListener("click", (e) => { if (e.target === modal) closeAuthModal(); });

// Toggle forms
showRegister.addEventListener("click", (e) => {
  e.preventDefault();
  loginForm.classList.add("hidden");
  registerForm.classList.remove("hidden");
});
showLogin.addEventListener("click", (e) => {
  e.preventDefault();
  registerForm.classList.add("hidden");
  loginForm.classList.remove("hidden");
});

// Trigger popup when user not logged in
document.querySelectorAll(".addToCart, .buyNow").forEach(btn => {
  btn.addEventListener("click", (e) => {
    const product_id = btn.dataset.product_id;
    const name = btn.dataset.name;
    const price = btn.dataset.price;
    const quantity = btn.closest('.flex.flex-col').querySelector('.quantity').value;

    if (!isLoggedIn) {
      e.preventDefault();
      openAuthModal(); // Show login/register popup
      return;
    }

    // If user is logged in, redirect to proper page
    if (btn.classList.contains('addToCart')) {
      window.location.href = `add_to_cart.php?product_id=${product_id}&name=${encodeURIComponent(name)}&price=${price}&quantity=${quantity}`;
    } else if (btn.classList.contains('buyNow')) {
      window.location.href = `buy_now.php?product_id=${product_id}&name=${encodeURIComponent(name)}&price=${price}&quantity=${quantity}`;
    }
  });
});


// FOR USER PROFILE IF NOT LOGGED IN
// FOR PROFILE AND CART BUTTONS IF NOT LOGGED IN
document.querySelectorAll('.openLoginPopup').forEach(btn => {
  btn.addEventListener('click', openAuthModal);
});


// FOR SLIDER
const slider = document.getElementById('productSlider');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
if (slider && prevBtn && nextBtn) {
  prevBtn.addEventListener('click', () => {
    slider.scrollBy({
      left: -300,
      behavior: 'smooth'
    });
  });

  nextBtn.addEventListener('click', () => {
    slider.scrollBy({
      left: 300,
      behavior: 'smooth'
    });
  });
}

// Quantity increment/decrement
document.addEventListener('DOMContentLoaded', () => {
  document.body.addEventListener('click', (e) => {
    if (e.target.classList.contains('increment')) {
      const input = e.target.previousElementSibling;
      const max = parseInt(input.max);
      let val = parseInt(input.value);
      if (val < max) input.value = val + 1;
    }

    if (e.target.classList.contains('decrement')) {
      const input = e.target.nextElementSibling;
      let val = parseInt(input.value);
      if (val > 1) input.value = val - 1;
    }
  });
});

</script>



  <!-- FOOTER -->
  <footer class="bg-[rgb(116,142,159)] text-white text-center py-6">
    <ul class="text-sm space-y-2">
      <a href="#" class="hover:underline">Home</a>
      <a href="#products" class="hover:underline">Products</a>
      <a href="#about" class="hover:underline">About</a>
      <a href="#contact" class="hover:underline">Contact</a>
    </ul>

  </footer>
</body>
</html>