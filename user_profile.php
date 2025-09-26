<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "ctunav");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMsg = '';
$errorMsg = '';

// Show success message once, then clear
if (isset($_SESSION['success'])) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $profileImagePath = null;

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageTmp = $_FILES['profile_image']['tmp_name'];
        $imageName = time() . '_' . basename($_FILES['profile_image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($imageTmp, $targetPath)) {
            $profileImagePath = $targetPath;
        } else {
            $errorMsg = "Image upload failed.";
        }
    }

    if (empty($errorMsg)) {
        if ($profileImagePath) {
            $stmt = $conn->prepare("UPDATE users SET first_name=?, middle_name=?, last_name=?, email=?, profile_image=? WHERE userID=?");
            $stmt->bind_param("sssssi", $first_name, $middle_name, $last_name, $email, $profileImagePath, $userID);
        } else {
            $stmt = $conn->prepare("UPDATE users SET first_name=?, middle_name=?, last_name=?, email=? WHERE userID=?");
            $stmt->bind_param("ssssi", $first_name, $middle_name, $last_name, $email, $userID);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $errorMsg = "Failed to update profile.";
        }
    }
}

// Re-fetch updated data
$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, email, profile_image FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if profile image exists
$defaultImg = 'images/user.png';
$profileImage = (!empty($user['profile_image']) && file_exists($user['profile_image'])) ? $user['profile_image'] : $defaultImg;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="icon" href="images/Logo.png" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
    body { background: #f4f6f8; }

    .profile-container {
      max-width: 600px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #7c0000;
      margin-bottom: 20px;
    }

    .alert {
      text-align: center;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 5px;
    }

    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }

    form { display: flex; flex-direction: column; gap: 15px; }

    label { font-weight: 500; margin-bottom: 5px; }

    input[type="text"], input[type="email"] {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #fff;
      width: 100%;
      font-size: 15px;
    }

    .button-group {
      margin-top: 20px;
      display: flex;
      gap: 10px;
    }

    .button-group button {
      flex: 1;
      background-color: #7c0000;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .profile-image-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
    }

    .profile-img {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #7c0000;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .profile-img:hover { opacity: 0.8; }
  </style>
</head>
<body>

<div class="profile-container">
  <h2>Edit My Profile</h2>

  <?php if ($successMsg): ?>
    <div class="alert success"><?= $successMsg ?></div>
  <?php elseif ($errorMsg): ?>
    <div class="alert error"><?= $errorMsg ?></div>
  <?php endif; ?>

  <div class="profile-image-wrapper">
    <label for="profileImageInput">
      <img id="previewImage" src="<?= htmlspecialchars($profileImage) ?>" alt="No Profile Picture" class="profile-img" />
    </label>
    <input type="file" name="profile_image" id="profileImageInput" accept="image/*" hidden form="profileForm">
  </div>

  <form id="profileForm" action="" method="post" enctype="multipart/form-data">
    <div>
      <label>First Name:</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
    </div>
    <div>
      <label>Middle Name:</label>
      <input type="text" name="middle_name" value="<?= htmlspecialchars($user['middle_name']) ?>">
    </div>
    <div>
      <label>Last Name:</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
    </div>
    <div>
      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <div class="button-group">
  <button type="submit">Save Changes</button>
  <a href="user_dashboard.php" style="flex: 1; text-align: center; background-color: #ccc; color: black; padding: 12px; border-radius: 6px; text-decoration: none; font-size: 15px;">Cancel</a>
</div>

  </form>
</div>

<script>
  const fileInput = document.getElementById('profileImageInput');
  const previewImage = document.getElementById('previewImage');

  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImage.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
</script>

</body>
</html>
