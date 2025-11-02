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
  $address = trim($_POST['address']);
  $city = trim($_POST['city']);
  $province = trim($_POST['province']);
  $postal_code = trim($_POST['postal_code']);

  $update = $conn->prepare("UPDATE users SET address=?, city=?, province=?, postal_code=? WHERE user_id=?");
  $update->bind_param("ssssi", $address, $city, $province, $postal_code, $user_id);

  if ($update->execute()) {
    header("Location: profile.php?update=success");
    exit;
  } else {
    echo "<p style='color:red;'>Error updating billing info. Please try again.</p>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Billing - Step 2</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-lg">
    <h2 class="text-2xl font-bold text-center text-[rgb(116,142,159)] mb-6">Billing Information</h2>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700 font-semibold">Address</label>
        <input type="text" name="address" 
               value="<?= htmlspecialchars($user['address'] ?? '') ?>" 
               class="w-full border rounded-lg p-2" required>
      </div>

      <div>
        <label class="block text-gray-700 font-semibold">City</label>
        <input type="text" name="city" 
               value="<?= htmlspecialchars($user['city'] ?? '') ?>" 
               class="w-full border rounded-lg p-2" required>
      </div>

      <div>
        <label class="block text-gray-700 font-semibold">Province</label>
        <input type="text" name="province" 
               value="<?= htmlspecialchars($user['province'] ?? '') ?>" 
               class="w-full border rounded-lg p-2" required>
      </div>

      <div>
        <label class="block text-gray-700 font-semibold">Postal Code</label>
        <input type="text" name="postal_code" 
               value="<?= htmlspecialchars($user['postal_code'] ?? '') ?>" 
               class="w-full border rounded-lg p-2" required>
      </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4 mt-6">
        <!-- Previous Button -->
        <button type="button" 
                onclick="goBack()"
                class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500 transition duration-200">
            Previous
        </button>

        <!-- Save / Next Button -->
        <button type="submit" 
                class="bg-[rgb(116,142,159)] text-white px-4 py-2 rounded-lg font-semibold hover:bg-[rgb(100,123,136)] transition duration-200">
            Save & Continue
        </button>
        </div>
    </form>
  </div>

<script>
function goBack() {
  window.location.href = 'edit_billing.php';
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  form.addEventListener('submit', (e) => {
    const requiredFields = form.querySelectorAll('input[required]');
    let allFilled = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        allFilled = false;
        field.classList.add('border-red-500');
      } else {
        field.classList.remove('border-red-500');
      }
    });

    // --- Postal code validation ---
    const postalInput = form.querySelector('input[name="postal_code"]');
    const postalVal = postalInput.value.trim();
    const postalPattern = /^\d{4}$/; // 4 digits only
    if (!postalPattern.test(postalVal)) {
      postalInput.classList.add('border-red-500');
      alert('Please enter a valid 4-digit Philippine postal code.');
      e.preventDefault();
      return; // stop form submission here
    } else {
      postalInput.classList.remove('border-red-500');
    }

    if (!allFilled) {
      e.preventDefault();
      alert('Please fill in all required fields before saving.');
    }
  });
});
</script>

</body>
</html>
