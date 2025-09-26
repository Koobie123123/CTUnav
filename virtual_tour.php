<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ctunav");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT profile_image FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$defaultImage = 'images/user.png';
$profileImage = (!empty($user['profile_image']) && file_exists($user['profile_image'])) ? $user['profile_image'] : $defaultImage;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CTU Tuburan Virtual Tour</title>
  <link rel="stylesheet" href="CSS/virtual_tour.css" />

</head>
<style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
    }

    body {
      min-height: 100vh;
    }

    /* NAVBAR */
    .navbar {
      background-color: #7c0000;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      flex-wrap: wrap;
    }

    .navbar .logo {
      display: flex;
      align-items: center;
    }

    .navbar .logo img {
      height: 60px;
      margin-right: 15px;
    }

    .navbar .logo h2 {
      font-size: 1.5rem;
    }

    .navbar .nav-links {
      display: flex;
      align-items: center;
      gap: 15px;
      position: relative;
    }

    .navbar .nav-links a {
      color: white;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 6px;
      font-weight: 500;
      transition: background-color 0.3s ease;
    }

    .navbar .nav-links a:hover {
      background-color: #5e0000;
    }

    .main {
      padding: 20px;
      background-position: center;
      background-repeat: no-repeat;
      position: relative;
    }

    .overlay {
      background-color: rgba(255, 255, 255, 0.85);
      padding: 20px;
      border-radius: 8px;
      max-width: 90%;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    .overlay h1 {
      margin-bottom: 15px;
      color: #7c0000;
      text-align: center;
    }

    /* Profile Dropdown */
    .profile-menu {
      position: relative;
      display: flex;
      align-items: center;
    }

    .profile-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      cursor: pointer;
      border: 2px solid white;
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 50px;
      right: 0;
      background-color: #7c0000;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
      min-width: 120px;
      z-index: 100;
    }

    .dropdown a {
      color: #fff;
      padding: 10px;
      text-decoration: none;
      display: block;
      text-align: center;
      font-weight: 500;
    }

    .dropdown a:hover {
      background-color: #5e0000;
    }

    .dropdown.show {
      display: block;
    }
</style>
<body>

  <!-- NAVBAR -->
  <div class="navbar">
    <div class="logo">
      <img src="images/Logo.png" alt="CTU Logo">
      <h2>CTU Tuburan</h2>
    </div>

    <div class="nav-links">
      <a href="index.php">Campus Map</a>
      <a href="virtual_tour.php">Virtual Tour</a>
      <a href="messages.php">Messages</a>
      
      <!-- Profile Dropdown -->
      <div class="profile-menu" id="profileMenu">
        <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="profile-icon" onclick="toggleDropdown()" />
        <div class="dropdown" id="dropdownMenu">
          <a href="user_profile.php">Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

<div class="main">
  <div class="overlay">
    <!-- IFRAME ADDED HERE -->
    <iframe width="100%" height="640" frameborder="0" allow="xr-spatial-tracking; gyroscope; accelerometer" allowfullscreen scrolling="no" src="https://kuula.co/share/collection/7DbC3?logo=1&info=0&logosize=105&fs=1&vr=0&zoom=1&sd=1&gyro=0&thumbs=-1&margin=7"></iframe>
</div>

  <script>
    function toggleDropdown() {
      document.getElementById("dropdownMenu").classList.toggle("show");
    }

    // Close dropdown if clicked outside
    window.addEventListener("click", function (e) {
      const menu = document.getElementById("profileMenu");
      const dropdown = document.getElementById("dropdownMenu");
      if (!menu.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });
  </script>

</body>
</html>
