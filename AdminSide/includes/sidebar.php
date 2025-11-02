<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-16 h-full w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40">
    <nav class="mt-6">
        <a href="dashboard.php" class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 transition <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home w-6"></i>
            <span class="ml-3 font-medium">Dashboard</span>
        </a>
        
        <a href="products.php" class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 transition <?php echo $currentPage == 'products.php' || $currentPage == 'add_product.php' ? 'active' : ''; ?>">
            <i class="fas fa-box w-6"></i>
            <span class="ml-3 font-medium">Products</span>
        </a>
        
        <a href="users.php" class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 transition <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users w-6"></i>
            <span class="ml-3 font-medium">Users</span>
        </a>
        
        <a href="orders.php" class="sidebar-link flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 transition <?php echo $currentPage == 'orders.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart w-6"></i>
            <span class="ml-3 font-medium">Orders</span>
        </a>
    </nav>
</aside>

<!-- Overlay for mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

<script>
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');

sidebarToggle?.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
    sidebarOverlay.classList.toggle('hidden');
});

sidebarOverlay?.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    sidebarOverlay.classList.add('hidden');
});
</script>