<?php
session_start();
include 'db_connect.php'; // make sure this connects to your DB

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Get user info from database
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'");

if ($result && $result->num_rows > 0) {
  $user = $result->fetch_assoc();
} else {
  echo "User not found.";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>`
  <meta charset="UTF-8">
  <title>User Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col items-center py-10">

  <!-- Profile Card -->
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md">
    <!-- Profile Image -->
    <div class="flex flex-col items-center">
      <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-[rgb(116,142,159)] mb-4">
        <?php if (!empty($user['profile_image_path'])): ?>
           <img 
                src="<?= !empty($user['profile_image_path']) ? htmlspecialchars($user['profile_image_path']) : 'https://braverplayers.org/wp-content/uploads/2022/09/braver-blank-pfp.jpg' ?>" 
                alt="Profile Picture" 
                class="w-full h-full object-cover"
                onerror="this.onerror=null; this.src='https://braverplayers.org/wp-content/uploads/2022/09/braver-blank-pfp.jpg';"
            >
        <?php else: ?>
          <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Default Profile" class="w-full h-full object-cover">
        <?php endif; ?>
      </div>

      <h2 class="text-2xl font-bold text-[rgb(116,142,159)] mb-1">
        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
      </h2>
      <p class="text-gray-600 mb-4">@<?= htmlspecialchars($user['username']) ?></p>
    </div>

    <!-- User Details -->
    <div class="space-y-3 border-t pt-4" style = "padding-top: 9%">
      <div>
        <span class="font-semibold text-gray-700">Email:</span>
        <p class="text-gray-600"><?= htmlspecialchars($user['email'] ?? 'Not provided') ?></p>
      </div>

      <div>
        <span class="font-semibold text-gray-700">Contact Number:</span>
        <p class="text-gray-600"><?= htmlspecialchars($user['contact_num'] ?? 'Not provided') ?></p>
      </div>

      <div style = "padding-bottom: 9%">
        <span class="font-semibold text-gray-700" >Birthdate:</span>
        <p class="text-gray-600"><?= htmlspecialchars($user['birthdate'] ?? 'Not provided') ?></p>
      </div>
    </div>

        
            <!-- Buttons -->
        <div class="mt-6 flex flex-col space-y-3">
        <a href="homepageUser.php"
            class="w-full bg-gray-500 text-white py-2 rounded-lg text-center hover:bg-gray-400 transition duration-200 font-semibold">
            Back to Homepage
        </a>
        <a href="edit_profile.php"
            class="w-full bg-[rgb(116,142,159)] text-white py-2 rounded-lg text-center hover:bg-[rgb(100,123,136)] transition duration-200 font-semibold">
            Edit Profile
        </a>
        <a href="logout.php"
            class="w-full bg-gray-800 text-white py-2 rounded-lg text-center hover:bg-gray-700 transition duration-200 font-semibold">
            Log Out
        </a>
        </div>

  </div>

</body>
</html>
