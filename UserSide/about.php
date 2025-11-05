<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | Electronic Device Market</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Header -->
  <?php include 'header.php'; ?>

  <!-- About Section -->
  <section class="bg-[rgb(116,142,159)] text-white py-20 text-center px-6">
    <h2 class="text-4xl font-bold mb-4">About Us</h2>
    <p class="max-w-3xl mx-auto text-lg leading-relaxed">
      Welcome to <span class="font-semibold">Electronic Device Market</span> — a prototype e-commerce platform developed by University of Caloocan City students.
      Our goal is to demonstrate how an online electronics store can help users explore, compare, and shop for modern devices efficiently.
    </p>
    <p class="max-w-3xl mx-auto text-lg leading-relaxed mt-6">
      This system includes core features such as product listings, a shopping cart, and user authentication — all designed to simulate
      a real-world web store experience. It was built using <span class="font-semibold">HTML, Tailwind CSS, PHP, Javascript and MySQL</span> for academic purposes.
    </p>
    <p class="mt-8 text-sm opacity-80">
      <i>Developed by BS Computer Science students</i>
    </p>
  </section>

  
<!-- Team Section -->
<section class="py-16 bg-gray-100 text-center">
  <h2 class="text-3xl font-bold mb-10 text-[rgb(116,142,159)]">Meet the Developers</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 max-w-6xl mx-auto px-6">
    
    <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-lg transition">
      <img src="https://scontent.fcrk3-3.fna.fbcdn.net/v/t39.30808-1/458685973_2078332299234916_2855669685257001315_n.jpg?stp=cp6_dst-jpg_s200x200_tt6&_nc_cat=111&ccb=1-7&_nc_sid=e99d92&_nc_eui2=AeHAaNC-a81HR60yAJBanO27IXdQHC0YeMchd1AcLRh4x5PBxt1kzV1qihhwIjpxhhRa5evQDReKitTN6Ct6UtOM&_nc_ohc=Big_XoA5tRQQ7kNvwGfK9Jn&_nc_oc=AdnQSO6-2tp1vDNJjX0RvXFHm8-MN2H1e6_Pi05P6gJb3T5aGNZ45PkBU1_bzMa0TDo5WP0w0KAEON2BhI1It0er&_nc_zt=24&_nc_ht=scontent.fcrk3-3.fna&_nc_gid=G9jJ7J5fGbFe7LsoualScw&oh=00_Afhz_QqEww7PNHUY8PiPlo7iZ5yLFQx-EmKE3bbYns53VQ&oe=6910CBFD" alt="Shan" class="w-24 h-24 mx-auto rounded-full mb-4 object-cover">
      <h3 class="font-semibold text-lg">Shan</h3>
      <p class="text-sm text-gray-600">User Page Developer</p>
    </div>

    <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-lg transition">
      <img src="https://scontent.fcrk3-1.fna.fbcdn.net/v/t1.30497-1/453178253_471506465671661_2781666950760530985_n.png?stp=dst-png_s200x200&_nc_cat=1&ccb=1-7&_nc_sid=136b72&_nc_eui2=AeG3TWMLDMHbcigMj2yA7-SFWt9TLzuBU1Ba31MvO4FTUHf1TAu_KgzgBsUHOuP_5mDXqzxB1iBUTN75H-e5KciX&_nc_ohc=sf_Q6oMt5UwQ7kNvwFvNLXG&_nc_oc=AdnbcuqT-3SdwWQcuyvolmg0v4Q4TzUUZcUkFvjv85paNOHB2jvJ9uyLTwGqH3XWVpYjZOD-XIG-v0q4HxnPK61c&_nc_zt=24&_nc_ht=scontent.fcrk3-1.fna&oh=00_AfjdVwbe1vx4vu_QV5MblQzQEwCASDGxqEIBdduo3KPuXg&oe=6932657A" alt="James Jhared Juanitez" class="w-24 h-24 mx-auto rounded-full mb-4 object-cover">
      <h3 class="font-semibold text-lg">James Jhared Juanitez</h3>
      <p class="text-sm text-gray-600">Backend Developer</p>
    </div>

    <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-lg transition">
      <img src="https://scontent.fcrk3-1.fna.fbcdn.net/v/t39.30808-1/558011686_3224526744390031_6355039193299709816_n.jpg?stp=dst-jpg_s200x200_tt6&_nc_cat=105&ccb=1-7&_nc_sid=e99d92&_nc_eui2=AeGD4J-qLrd_4XirumbrjokEFGyle0Czy0AUbKV7QLPLQFT0HivXiUq7r_l9ozOTJJ3l0B731CoQ9u7HUym2jtFf&_nc_ohc=hvdUTaJmTHUQ7kNvwHyzDRP&_nc_oc=AdnSZSQ94mahaO2eGUll83M5qAaQGCr1LUhtFS9qnCU7Q08NpSwlGZOsF-gPe4r5uYzXPjUecWTkOPe3YD6WcV1m&_nc_zt=24&_nc_ht=scontent.fcrk3-1.fna&_nc_gid=ts2aVZ8c-gw0PuEyIW5-lw&oh=00_Afg9U5JBSjUtFZNEC5ptCEP4EEWf8OLA555xSg1jQkqQHg&oe=6910B03E" alt="Jael P. Gonzal" class="w-24 h-24 mx-auto rounded-full mb-4 object-cover">
      <h3 class="font-semibold text-lg">Jael P. Gonzal</h3>
      <p class="text-sm text-gray-600">Database Manager</p>
    </div>

    <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-lg transition">
      <img src="https://scontent.fcrk3-3.fna.fbcdn.net/v/t39.30808-1/548195874_3014884908694417_3464814004857849116_n.jpg?stp=cp6_dst-jpg_s200x200_tt6&_nc_cat=100&ccb=1-7&_nc_sid=1d2534&_nc_eui2=AeFc2KqtzKrlER_OFGobganOBekUAenRPMgF6RQB6dE8yMu08a4B3nLNEPa9ZQwQ6htMB9dwExFdtHMSbLRnO2SV&_nc_ohc=R1JNjNYmLowQ7kNvwEaXgcv&_nc_oc=Adny2hBqFFbItKLnBU5zIjpzOlM0fBt9VhwockXqqJciqwt18wP329IGoUPxtElJ1gp7bcEQhY9aIZ-2w7f6HM2b&_nc_zt=24&_nc_ht=scontent.fcrk3-3.fna&_nc_gid=GAfwLRe1sYfpGjxrNDZ39w&oh=00_AfhTctf88PA9L6gdeNYHzVPWW8xIM3yjyhTR-svAY-WFmg&oe=6910B105" alt="Johnziljan Severo" class="w-24 h-24 mx-auto rounded-full mb-4 object-cover">
      <h3 class="font-semibold text-lg">Johnziljan Severo</h3>
      <p class="text-sm text-gray-600">Admin Page Developer</p>
    </div>

  </div>
</section>


  <!-- Footer -->
  <footer class="bg-[rgb(116,142,159)] text-white py-6 text-center text-sm">
    © 2025 Electronic Device Market.
  </footer>

</body>
</html>
