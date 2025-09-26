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

    // ✅ Check if email is already used by another user
    $checkStmt = $conn->prepare("SELECT userID FROM users WHERE email = ? AND userID != ?");
    $checkStmt->bind_param("si", $email, $userID);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $errorMsg = "This email is already in use by another user.";
    } else {
        // === image upload handling ===
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
}

// Re-fetch updated data
$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, email, profile_image FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$defaultImg = 'images/user.png';
$profileImage = (!empty($user['profile_image']) && file_exists($user['profile_image'])) ? $user['profile_image'] : $defaultImg;
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CTU-Tuburan Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
/* ========== GLOBAL RESET & BASE STYLES ========== */
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #fff;
  color: #000;
  display: flex;
  flex-direction: column;
  height: 100vh;
}

/* ========== NAVBAR ========== */
.navbar {
  height: 60px;
  background-color: #f3f3f3ff;
  color: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  box-shadow: 0 2px 4px rgb(0 0 0 / 0.1);
  border-bottom: 1px solid #ff7a00;
}

.navbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.navbar-left img {
  height: 40px;
  width: 40px;
  border-radius: 50%;
}

.navbar-left .logo-text {
  font-weight: bold;
  font-size: 16px;
  white-space: nowrap;
  color: #ff7a00;
}

.navbar-right {
  display: flex;
  align-items: center;
  gap: 20px;
}

.navbar-right img.profile,
.navbar-right img.profile-icon {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  object-fit: cover;
}

.navbar-right .icon {
  width: 26px;
  height: 26px;
  fill: black;
  cursor: pointer;
  transition: fill 0.2s ease;
}

.navbar-right .icon:hover {
  fill: #ff7a00;
}

/* ========== PROFILE SECTION ========== */
section.profile-container {
  flex: 1;
  padding: 1rem;
  overflow-y: auto;
}

/* Cover photo */
.cover-photo {
  position: relative;
  width: 100%;
  height: 200px;
  background: url('images/cover.jpg') center/cover no-repeat;
  border-radius: 12px;
  overflow: hidden;
}

.cover-photo .overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  width: 100%;

}

/* Profile photo */
.profile-photo {
  position: relative;
  text-align: center;
  margin-top: -70px; /* pull photo up */
  z-index: 10;
}

.profile-photo .avatar {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background: url('<?= htmlspecialchars($profileImage) ?>') center/cover no-repeat;
  border: 4px solid #fff;
  margin: 0 auto;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.profile-photo .name {
  margin-top: 20px;
  margin-bottom: 10px;
  font-size: 18px;
  font-weight: bold;
  color: #000;
}

/* Cover content (below photo) */
.cover-content {
  position: relative;
  text-align: center;
  margin-top: 10px;
}

/* ========== FORM (EDIT PROFILE) ========== */
form.edit-profile {
  max-width: 400px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

form.edit-profile input {
  width: 100%;
  padding: 0.7rem 1.2rem;
  border-radius: 1rem;
  border: none;
  background: #d3d3d3;
}

form.edit-profile button {
  margin-top: 1rem;
  background-color: #ff7a00;
  border: none;
  border-radius: 1.5rem;
  padding: 0.75rem 1.5rem;
  font-weight: 500;
  cursor: pointer;
}

/* ========== ALERT MESSAGES ========== */
.alert {
  text-align: center;
  margin: 10px 0;
  padding: 10px;
  border-radius: 5px;
}

.success {
  background: #d4edda;
  color: #155724;
}

.error {
  background: #f8d7da;
  color: #721c24;
}

/* ========== SIDEBAR ========== */
.sidebar {
  width: 250px;
  background-color: rgba(255, 255, 255, 0.9);
  padding: 20px 0;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidebar a {
  display: block;
  padding: 15px 25px;
  color: #333;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s;
  border-left: 4px solid transparent;
}

.sidebar a:hover {
  background-color: #f0f5ff;
  border-left: 4px solid #ff7300;
  color: #ff7300;
}

.sidebar a.active {
    background-color: #f0f5ff;
    border-left: 4px solid #ff7300;
    color: #ff7300;
}

/* Layout for sidebar + content */
.main-container {
  display: flex;
  flex: 1;
  min-height: calc(100vh - 60px); /* full height minus navbar */
}

/* Sidebar */
.sidebar {
  flex-shrink: 0;
  width: 250px;
  background-color: rgba(255, 255, 255, 0.9);
  padding: 20px 0;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  height: auto;
}

/* Content area */
.profile-container {
  flex: 1;
  padding: 1rem;
  overflow-y: auto;
}


/* ========== MEDIA QUERIES ========== */

/* Tablets and small laptops */
@media (max-width: 1024px) {
  .navbar-left .logo-text {
    font-size: 14px;
  }
  .navbar-right {
    gap: 12px;
  }
}

/* Mobile landscape & portrait */
@media (max-width: 768px) {
  .navbar {
    flex-wrap: wrap;
    height: auto;
    padding: 10px 15px;
  }

  .navbar-left {
    width: 100%;
    justify-content: center;
    margin-bottom: 8px;
  }

  .navbar-right {
    width: 100%;
    justify-content: center;
    gap: 15px;
  }



  .sidebar {
    width: 100%;
    display: flex;
    overflow-x: auto;
    padding: 10px 0;
  }

  .sidebar a {
    padding: 10px 15px;
    white-space: nowrap;
    border-left: none;
    border-bottom: 3px solid transparent;
  }

  .sidebar a:hover {
    border-left: none;
    border-bottom: 3px solid #ff7300;
  }

  .content {
    padding: 20px;
  }
}

/*Form labels, katong naas left sa input box*/
form.edit-profile {
  max-width: 500px; 
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.form-group label {
  width: 120px;       /* fixed width so labels align neatly */
  font-size: 14px;
  font-weight: 500;
  color: #333;
  text-align: right;  /* align text to the right side */
}

.form-group input {
  flex: 1;  /* input takes remaining space */
  padding: 0.7rem 1rem;
  border-radius: 1rem;
  border: none;
  background: #d3d3d3;
}

form.edit-profile button {
  align-self: flex-end; /* button aligns with inputs */
  background-color: #ff7a00;
  border: none;
  border-radius: 1.5rem;
  padding: 0.75rem 1.5rem;
  font-weight: 500;
  cursor: pointer;
}

/*Error Message kung naay kapareha na email*/
.error {
  color: red;
  font-size: 13px;
  margin-top: 4px;
}

/*Pencil/Edit Icon para sa profile*/
.avatar-wrapper {
  position: relative;
  display: inline-block;
}

.avatar {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  border: 4px solid #fff;
  background: center/cover no-repeat;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* Pencil icon overlay */
.edit-icon {
  position: absolute;
  bottom: 8px;
  right: 8px;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 16px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  transition: background-color 0.2s ease;
  color: #e56c00;
  background-color: #fff;
}

.edit-icon:hover {
  background-color: #e56c00;
  color: #fff;
}

.edit-icon i {
  pointer-events: none; /* make sure clicking the icon still triggers the file input */
}


</style>
</head>
<body>
   <!-- Navbar -->
<nav class="navbar">
    <div class="navbar-left">
        <a href="index.php">
            <img src="images/Logo.png" alt="Logo" />
        </a>
        <span class="logo-text">CTU-TUBURAN</span>
    </div>
    <div class="navbar-right">
        <a href="user_dashboard.php">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
        </a>
        <a href="messages.php">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M20 2H4a2 2 0 0 0-2 2v16l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
            </svg>
        </a>
        <a href="profile.php">
            <img class="profile" src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" />
        </a>
    </div>
</nav>
  
  <main class="main-container">
    <nav class="sidebar">
      <a href="profile.php" class="active" >Manage Account</a>
      <a href="user_manage_pass.php">Manage Password</a>
      <a href="logout.php">Log Out</a>
    </nav>
    <section class="profile-container">

      
      <!-- Cover Photo -->
<div class="cover-photo">
  <div class="overlay"></div>
</div>

<!-- Profile Section (below cover) -->
<div class="profile-photo">
  <div class="avatar-wrapper">
    <div class="avatar" style="background-image: url('<?= htmlspecialchars($profileImage) ?>');"></div>

    <!-- Pencil icon button -->
<label for="profileImageInput" class="edit-icon">
  <i class="fa-solid fa-pencil"></i>
</label>

  </div>

  <input type="file" name="profile_image" id="profileImageInput" accept="image/*" hidden form="profileForm">

  <div class="name">
    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
  </div>
</div>



      <?php if ($successMsg): ?>
        <div class="alert success"><?= $successMsg ?></div>
      <?php elseif ($errorMsg): ?>
        <div class="alert error"><?= $errorMsg ?></div>
      <?php endif; ?>

<!-- Edit Profile Form -->
<form id="profileForm" class="edit-profile" action="" method="post" enctype="multipart/form-data">
  
  <div class="form-group">
    <label for="first_name">First Name: </label>
    <input type="text" id="first_name" name="first_name" 
           value="<?= htmlspecialchars($user['first_name']) ?>" required>
  </div>

  <div class="form-group">
    <label for="middle_name">Middle Name: </label>
    <input type="text" id="middle_name" name="middle_name" 
           value="<?= htmlspecialchars($user['middle_name']) ?>">
  </div>

  <div class="form-group">
    <label for="last_name">Last Name: </label>
    <input type="text" id="last_name" name="last_name" 
           value="<?= htmlspecialchars($user['last_name']) ?>" required>
  </div>

  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" 
           value="<?= htmlspecialchars($user['email']) ?>" required>
    <?php if (!empty($errorMsg) && strpos($errorMsg, 'email') !== false): ?>
    <?php endif; ?>
  </div>


  <button type="submit">Save Changes</button>
</form>


    </section>
  </main>

<script>
  const fileInput = document.getElementById('profileImageInput');
  const avatarDiv = document.querySelector('.avatar');
  fileInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        avatarDiv.style.backgroundImage = `url('${e.target.result}')`;
      };
      reader.readAsDataURL(file);
    }
  });
</script>

</body>
</html>
