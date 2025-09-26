<?php
session_start();

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load messages and clear them
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// REGISTER
if (isset($_POST['register'])) {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = 'student';

    $check = $conn->prepare("SELECT userID FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $middle_name, $last_name, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// LOGIN
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // fetch departmentID also
$stmt = $conn->prepare("
    SELECT u.userID, u.first_name, u.last_name, u.email, u.password, u.role, s.departmentID
    FROM users u
    LEFT JOIN staff s ON u.userID = s.userID
    WHERE u.email = ?
");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userID, $first_name, $last_name, $emailDb, $hashedPassword, $role, $departmentID);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userID;
            $_SESSION['user_name'] = "$first_name $last_name";
            $_SESSION['user_email'] = $emailDb;
            $_SESSION['role'] = $role;

            // ✅ Save staff departmentID
            if ($role === 'staff') {
                $_SESSION['departmentID'] = $departmentID;
                header("Location: staff_dashboard.php");
            } elseif ($role === 'admin') {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Invalid password!";
        }
    } else {
        $_SESSION['error'] = "Account not found!";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ADMIN CREATION
if (isset($_GET['create_admin'])) {
    $admin_email = 'admin@gmail.com';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);

    $conn->query("ALTER TABLE users MODIFY role ENUM('student', 'staff', 'admin') NOT NULL DEFAULT 'student'");

    $check = $conn->prepare("SELECT userID FROM users WHERE email = ?");
    $check->bind_param("s", $admin_email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, role) VALUES ('Admin', '', 'User', ?, ?, 'admin')");
        $stmt->bind_param("ss", $admin_email, $admin_password);
        $stmt->execute();
        echo "✅ Admin account created. You can now log in using admin@gmail.com / admin123";
    } else {
        echo "ℹ️ Admin account already exists.";
    }
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CTU-Tuburan Virtual Tour</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Roboto', sans-serif;
}

body {
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* Background Video */
#bg-video {
  position: fixed;
  right: 0;
  bottom: 0;
  min-width: 100%;
  min-height: 100%;
  object-fit: cover;
  z-index: -2;
}

/* Dark Overlay */
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  z-index: -1;
}

/* Navbar */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 40px;
  background: white;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  position: relative;
  z-index: 2;
}

.navbar .logo {
  font-size: 20px;
  font-weight: 700;
  color: #ff7a00;
  display: flex;
  align-items: center;
  gap: 8px;
}

.navbar ul {
  display: flex;
  list-style: none;
  gap: 25px;
}

.navbar ul li a {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  transition: 0.3s;
}

.navbar ul li a:hover {
  color: #ff7a00;
}

/* Main Section */
.main {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 40px 130px;
  position: relative;
  z-index: 1;
}

.content {
  max-width: 60%;
  color: white;
}

.content h1 {
  font-size: 50px;
  font-weight: 700;
  margin-bottom: 20px;
}

.content p {
  font-size: 18px;
  margin-bottom: 25px;
}

.tour-btn {
  padding: 12px 25px;
  background: #ff7a00;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
}

.tour-btn:hover {
  background: #e56a00;
}

/* Auth Box */
.auth-box {
  width: 370px;
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.auth-box h2 {
  font-size: 22px;
  margin-bottom: 10px;
  text-align: center;
}

.auth-box p {
  margin-bottom: 20px;
  font-size: 13px;
  color: #666;
  text-align: center;
}

.auth-box form label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

.auth-box input {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  outline: none;
  font-size: 12px;
}

.password-container {
  position: relative;
}

.password-container i {
  position: absolute;
  right: 10px;
  top: 14px;
  color: #888;
  cursor: pointer;
}

.auth-btn {
  width: 100%;
  padding: 12px;
  background: #ff7a00;
  color: white;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-bottom: 0px;
}

.auth-btn:hover {
  background: #e56a00;
}

.extra-links {
  text-align: center;
  margin-top: 10px;
}

.extra-links a {
  font-size: 14px;
  color: #ff7a00;
  text-decoration: none;
}

.extra-links a:hover {
  text-decoration: underline;
}

.hidden {
  display: none;
}

.highlight {
  color: #ff7a00;
  font-weight: 700;
}
</style>
</head>
<body>
  <!-- Video Background -->
  <video autoplay muted loop id="bg-video">
    <source src="images/CTU promotional Video- Bsit.mp4" type="video/mp4">
    Your browser does not support HTML5 video.
  </video>
  <div class="overlay"></div>

  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">
     <img src="images/Logo.png" style="height:60px; width:auto;"> 
     CTU-Tuburan
    </div>
    <nav>
      <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- Main Section -->
  <section class="main">
    <div class="content">
      <h1>Welcome to the <span class="highlight">CTU-Tuburan</span> Virtual Navigation Tour</h1>
      <p>Explore our beautiful campus from anywhere through our interactive and immersive virtual experience.</p>
      <button class="tour-btn">Take the Tour Now</button>
    </div>

   <!-- Login Box -->
<div class="auth-box" id="login-box">
  <h2>Welcome Back!</h2>
  <p>Sign in to access your account</p>

  <!-- ✅ Added method POST -->
  <form method="POST" action="">
    <label>Email</label>
    <!-- ✅ Added name -->
    <input type="email" name="email" placeholder="Enter your email" required>

    <label>Password</label>
    <div class="password-container">
      <!-- ✅ Added name -->
      <input type="password" name="password" placeholder="Enter your password" required>
      <i class="fas fa-eye"></i>
    </div>

    <!-- ✅ Added name="login" -->
    <button type="submit" name="login" class="auth-btn">Sign in</button>
  </form>

  <?php if (!empty($error)): ?>
    <p style="color:red; text-align:center; margin-top:10px;"><?= $error ?></p>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
    <p style="color:green; text-align:center; margin-top:10px;"><?= $success ?></p>
  <?php endif; ?>

  <div class="extra-links">
    <a href="#">Forgot Password?</a><br>
    <a href="#" onclick="toggleForms()">Don't have an account? Sign up</a>
  </div>
</div>

<!-- Sign Up Box -->
<div class="auth-box hidden" id="signup-box">
  <h2>Create Account</h2>
  <p>Fill in the details to register</p>

  <!-- ✅ Added method POST -->
  <form method="POST" action="">
    <label>First Name</label>
    <input type="text" name="first_name" placeholder="Enter your first name" required>

    <label>Middle Name</label>
    <input type="text" name="middle_name" placeholder="Enter your middle name">

    <label>Last Name</label>
    <input type="text" name="last_name" placeholder="Enter your last name" required>

    <label>Email</label>
    <input type="email" name="email" placeholder="Enter your email" required>

    <label>Password</label>
    <div class="password-container">
      <input type="password" name="password" placeholder="Create a password" required>
      <i class="fas fa-eye"></i>
    </div>

    <!-- ✅ Added name="register" -->
    <button type="submit" name="register" class="auth-btn">Sign up</button>
  </form>

  <?php if (!empty($error)): ?>
    <p style="color:red; text-align:center; margin-top:10px;"><?= $error ?></p>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
    <p style="color:green; text-align:center; margin-top:10px;"><?= $success ?></p>
  <?php endif; ?>

  <div class="extra-links">
    <a href="#" onclick="toggleForms()">Already have an account? Login</a>
  </div>
</div>

  </section>

  <script>
    function toggleForms() {
      document.getElementById('login-box').classList.toggle('hidden');
      document.getElementById('signup-box').classList.toggle('hidden');
    }

    
  // Select all eye icons
  const toggles = document.querySelectorAll('.password-container i');

  toggles.forEach(toggle => {
    toggle.addEventListener('click', () => {
      const input = toggle.previousElementSibling; // the password input

      if (input.type === "password") {
        input.type = "text";
        toggle.classList.remove("fa-eye");
        toggle.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        toggle.classList.remove("fa-eye-slash");
        toggle.classList.add("fa-eye");
      }
    });
  });


  </script>
</body>
</html>
