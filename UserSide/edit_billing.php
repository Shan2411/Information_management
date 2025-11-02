<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: HomepageUser.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// --- Fetch user info ---
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// --- Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = trim($_POST['first_name']);
  $last_name = trim($_POST['last_name']);
  $middle_initial = trim($_POST['middle_initial']);
  $birthdate = $_POST['birthdate'];
  $email = trim($_POST['email']);
  $contact_num = trim($_POST['contact_num']);
  $profile_image_path = $user['profile_image_path']; // keep old one by default

  // --- Handle file upload if a new image is provided ---
  if (!empty($_FILES['profile_image']['name'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES['profile_image']['name']);
    $target_file = $target_dir . time() . "_" . $file_name;

    // Only allow image types
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (in_array($file_type, $allowed_types)) {
      if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
        $profile_image_path = $target_file;
      }
    }
  }

  // --- Update info in database ---
  $update = $conn->prepare("UPDATE users SET first_name=?, middle_initial=?, last_name=?, birthdate=?, email=?, contact_num=?, profile_image_path=? WHERE user_id=?");
  $update->bind_param("sssssssi", $first_name, $middle_initial, $last_name, $birthdate, $email, $contact_num, $profile_image_path, $user_id);

    if ($update->execute()) {
        header("Location: edit_billing_step2.php");
        exit;
    } else {
        echo "<p style='color:red;'>Error updating info. Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Billing & Info</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-lg">
    <h2 class="text-2xl font-bold text-center text-[rgb(116,142,159)] mb-6">Edit Billing & Information</h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <!-- Profile Image -->
      <div class="flex flex-col items-center">
        <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-[rgb(116,142,159)] mb-3">
          <img src="<?= htmlspecialchars($user['profile_image_path'] ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png') ?>" alt="Profile" class="w-full h-full object-cover">
        </div>
        <input type="file" name="profile_image" accept="image/*" class="text-sm text-gray-600">
      </div>

      <!-- Name fields -->
        <div class="grid grid-cols-3 gap-2">
        <input type="text" name="first_name" placeholder="First Name"
                value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                class="col-span-1 border rounded-lg p-2">

        <input type="text" name="middle_initial" placeholder="M.I." maxlength="1"
                value="<?= htmlspecialchars($user['middle_initial'] ?? '') ?>"
                class="col-span-1 border rounded-lg p-2 text-center">

        <input type="text" name="last_name" placeholder="Last Name"
                value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                class="col-span-1 border rounded-lg p-2">
        </div>

        <div>
        <label class="block text-gray-700 font-semibold">Birthdate</label>
        <input type="date" name="birthdate"
                value="<?= htmlspecialchars($user['birthdate'] ?? '') ?>"
                class="w-full border rounded-lg p-2">
        </div>

        <div>
        <label class="block text-gray-700 font-semibold">Email</label>
        <input type="email" name="email"
                value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                class="w-full border rounded-lg p-2" required>
        </div>

        <div>
        <label class="block text-gray-700 font-semibold">Contact Number</label>
        <input type="text" name="contact_num"
                value="<?= htmlspecialchars($user['contact_num'] ?? '') ?>"
                class="w-full border rounded-lg p-2">
        </div>


    <!-- Buttons -->
        <div class="flex justify-end gap-4 mt-6">
            <a href="profile.php" 
                class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-[rgb(116,142,159)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[rgb(100,123,136)] transition duration-200">
                Next
            </button>
        </div>
    </form>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  form.addEventListener('submit', (e) => {
    const requiredFields = form.querySelectorAll('input[required], textarea[required]');
    let allFilled = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        allFilled = false;
        field.classList.add('border-red-500'); // highlight empty field
      } else {
        field.classList.remove('border-red-500');
      }
    });

    if (!allFilled) {
      e.preventDefault();
      alert('Please fill in all required fields before proceeding.');
    }
  });
});
</script>


</body>
</html>
