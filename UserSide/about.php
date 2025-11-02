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
        <div class="w-24 h-24 mx-auto bg-gray-300 rounded-full mb-4"></div>
        <h3 class="font-semibold text-lg">Shan</h3>
        <p class="text-sm text-gray-600">Frontend Developer</p>
      </div>

      <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-lg transition">
        <div class="w-24 h-24 mx-auto bg-gray-300 rounded-full mb-4"></div>
        <h3 class="font-semibold text-lg">[Teammate 2]</h3>
        <p class="text-sm text-gray-600">Backend Developer</p>
      </div>

      <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-lg transition">
        <div class="w-24 h-24 mx-auto bg-gray-300 rounded-full mb-4"></div>
        <h3 class="font-semibold text-lg">[Teammate 3]</h3>
        <p class="text-sm text-gray-600">UI/UX Designer</p>
      </div>

      <div class="bg-white shadow-md rounded-2xl p-6 hover:shadow-lg transition">
        <div class="w-24 h-24 mx-auto bg-gray-300 rounded-full mb-4"></div>
        <h3 class="font-semibold text-lg">[Teammate 4]</h3>
        <p class="text-sm text-gray-600">Project Manager</p>
      </div>

    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-[rgb(116,142,159)] text-white py-6 text-center text-sm">
    © 2025 Electronic Device Market.
  </footer>

</body>
</html>
