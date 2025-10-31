<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>

<header class="bg-[rgb(116,142,159)] text-white sticky top-0 z-50 shadow-md">
  <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">

    <!-- Left: Logo -->
    <a href="HomepageUser.php" class="text-2xl font-bold tracking-wide hover:opacity-90 transition">
      Electronic Device Market
    </a>

    <!-- Center: Navigation -->
        <nav class="hidden md:flex items-center space-x-8 text-[15px] font-medium">
        <a href="HomepageUser.php" class="hover:text-gray-200 transition">Home</a>
        <a href="mainproducts.php" class="hover:text-gray-200 transition">Products</a>
        <a href="about.php" class="hover:text-gray-200 transition">About Us</a>
        </nav>


    <!-- Right: Cart + Profile / Auth -->
    <div class="flex items-center space-x-4">
      <?php if ($isLoggedIn): ?>
        <!-- Logged in -->
        <a href="viewcart.php" class="flex items-center gap-2 bg-white/15 hover:bg-white/25 px-3 py-2 rounded-full transition">
          <span class="text-xl">🛒</span>
          <span class="hidden sm:inline text-sm font-semibold">View Cart</span>
        </a>
        <a href="profile.php" class="flex items-center gap-2 bg-white/15 hover:bg-white/25 px-3 py-2 rounded-full transition">
          <span class="text-xl">👤</span>
          <span class="hidden sm:inline text-sm font-semibold">Profile</span>
        </a>
      <?php else: ?>
        <!-- Not logged in -->
        <button class="openLoginPopup flex items-center gap-2 bg-white/15 hover:bg-white/25 px-3 py-2 rounded-full transition focus:outline-none">
          <span class="text-xl">🛒</span>
          <span class="hidden sm:inline text-sm font-semibold">View Cart</span>
        </button>
        <button class="openLoginPopup flex items-center gap-2 bg-white/15 hover:bg-white/25 px-3 py-2 rounded-full transition focus:outline-none">
          <span class="text-xl">👤</span>
          <span class="hidden sm:inline text-sm font-semibold">Login</span>
        </button>
      <?php endif; ?>

      <!-- Mobile Menu Button -->
      <button id="menuBtn" class="md:hidden text-2xl focus:outline-none hover:opacity-80">☰</button>
    </div>
  </div>

  <!-- Mobile Navigation -->
  <nav id="mobileNav" class="hidden flex-col bg-[rgb(106,132,149)] text-white md:hidden">
    <a href="HomepageUser.php" class="py-3 px-6 hover:bg-[rgb(96,122,139)] transition nav-protected" data-target="HomepageUser.php">Home</a>
    <a href="mainproducts.php" class="py-3 px-6 hover:bg-[rgb(96,122,139)] transition nav-protected" data-target="mainproducts.php">Products</a>
    <a href="about.php" class="py-3 px-6 hover:bg-[rgb(96,122,139)] transition">About Us</a>
  </nav>
</header>

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
const menuBtn = document.getElementById('menuBtn');
const mobileNav = document.getElementById('mobileNav');

if (menuBtn && mobileNav) {
  menuBtn.addEventListener('click', () => mobileNav.classList.toggle('hidden'));
}

// Handle nav-protected links (require login)
document.querySelectorAll('.nav-protected').forEach(link => {
  link.addEventListener('click', (e) => {
    if (!isLoggedIn) {
      e.preventDefault();
      openAuthModal(); // Function from login_modal.php
    }
  });
});

// Open login popup for buttons
document.querySelectorAll('.openLoginPopup').forEach(btn => {
  btn.addEventListener('click', openAuthModal);
});

// Define login modal open/close so it works on any page
const modal = document.getElementById("authModal");
const authBox = document.getElementById("authBox");

function openAuthModal() {
  if (!modal || !authBox) return;
  modal.classList.remove("hidden");
  setTimeout(() => {
    authBox.classList.remove("scale-95", "opacity-0");
    authBox.classList.add("scale-100", "opacity-100");
  }, 50);
}

function closeAuthModal() {
  if (!modal || !authBox) return;
  authBox.classList.add("scale-95", "opacity-0");
  authBox.classList.remove("scale-100", "opacity-100");
  setTimeout(() => modal.classList.add("hidden"), 200);
}

// Close modal button
const closeModal = document.getElementById("closeAuthModal");
if (closeModal) closeModal.addEventListener("click", closeAuthModal);
modal?.addEventListener("click", (e) => { if (e.target === modal) closeAuthModal(); });


</script>
