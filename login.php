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

// Decide which form to show (login or register)
$show_form = $_SESSION['show_form'] ?? 'login';
unset($_SESSION['show_form']);


// REGISTER
if (isset($_POST['register'])) {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'user';

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        $_SESSION['show_form'] = 'register'; // ✅ Stay on signup
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT userID FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
        $_SESSION['show_form'] = 'register'; // ✅ Stay on signup
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (first_name, middle_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $middle_name, $last_name, $email, $hashedPassword, $role);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful!";
        $_SESSION['show_form'] = 'login'; // ✅ After success, go back to login
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
        $_SESSION['show_form'] = 'register';
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// LOGIN
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

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

    $conn->query("ALTER TABLE users MODIFY role ENUM('user', 'staff', 'admin') NOT NULL DEFAULT 'user'");

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
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
*{margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Roboto',sans-serif;
}


body{
  height:100vh;
  display:flex;
  overflow:hidden;
}

.left{
  width:40%;
  background: #f9f9f9;
  padding:10px;
  display:flex;
  flex-direction:column;
  align-items:center;
  position:relative;
}

.left {
  overflow-y: auto;             /* allow internal scroll if needed */
  -ms-overflow-style: none;     /* IE/Edge */
  scrollbar-width: none;        /* Firefox */
}
.left::-webkit-scrollbar {
  width: 0;
  height: 0;
}

.logo{
  display:flex;
  align-items:center;
  gap:10px;
  margin-bottom:20px;
}

.logo img{
  height:60px;
  width:auto;
}

.logo-text{
  font-size:20px;
  font-weight:700;
  color: #f39c12;
}
.right{
  width:60%;
  position:relative;
  overflow:hidden;
}
.right video,
.right img{
  width:100%;
  height:100%;
  object-fit:cover;
}
.auth-box{
  width:70%;
  background:white;
  padding:30px;
  border-radius:10px;
  box-shadow:0 5px 20px rgba(0,0,0,0.2);
  margin-bottom:20px;
}
.auth-box h2{
  text-align:center;
  margin-bottom:10px;
}
.auth-box p{
  text-align:center;
  margin-bottom:20px;
  font-size:13px;
  color: #666;
}
.auth-box form label{
  display:block;
  margin-bottom:2px;
  font-weight:500;
}
.auth-box input{
  width:100%;
  padding:12px;
  margin-bottom:15px;
  border:1px solid #ccc;
  border-radius:6px;
  font-size:12px;
}
.password-container{
  position:relative;
}
.password-container i{
  position:absolute;
  right:10px;
  top:14px;
  color: #888;
  cursor:pointer;
}
.auth-btn{
  width:100%;
  padding:12px;
  background: #f39c12;
  color:white;
  font-size:16px;
  border:none;
  border-radius:6px;
  cursor:pointer;
}
.auth-btn:hover{
  background: #e67e22;
}
.extra-links{
  text-align:center;
  margin-top:10px;
}
.extra-links a{
  font-size:14px;
  color: #f39c12;
  text-decoration:none;
}
.extra-links a:hover{
  text-decoration:underline;
}
.hidden{
  display:none;
}
.message{
  text-align:center;
  margin:10px;
  font-weight:600;
}
.error{
  color:red;
}
.success{
  color:green;
}
/* Center login form vertically in the left side */
#login-box {
  position: absolute;
  top: 50%;
  left: 50%; /* adjust horizontally */
  transform: translate(-50%, -50%);
  width: 70%;
}

/* Keep register form normal */
#signup-box {
  position: relative;
  margin-top: 0;
  transform: none;
  left: auto;
  top: auto;
}

.extra-links .highlight {
  color: #f39c12;  /* orange */
  text-decoration: none;
}

.back-btn {
  position: absolute;
  top: 15px;
  left: 15px;
  z-index: 1000;
  margin-left: 10px;
  margin-top: 10px;
}

.back-btn a {
  display: flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  color: black;
  font-weight: 60;
  transition: color 0.3s ease;
}

.back-btn a:hover {
  color: #f39c12; /* applies to both icon and text */
}
 
</style>
</head>
<body>




  <div class="left">

  <!-- Back Button -->
<div class="back-btn">
  <a href="index.php" class="back-link" aria-label="Back to Login">
    <i class="fas fa-arrow-left" aria-hidden="true"></i>
    <span>Back</span>
  </a>
</div>

<div class="logo" >
  <a href="index.php" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
    <img src="images/logo.png" alt="CTU Logo">
    <span class="logo-text">CTU Tuburan</span>
  </a>
</div>


    <!-- Messages -->
    <?php if ($error): ?>
      <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Login Box -->
    <div class="auth-box <?= ($show_form === 'login') ? '' : 'hidden' ?>" id="login-box">
      <h2>Welcome!</h2>
      <p>Sign in to access your account</p>
      <form method="POST" action="">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>
        <label>Password</label>
        <div class="password-container">
          <input type="password" name="password" placeholder="Enter your password" required>
          <i class="fas fa-eye"></i>
        </div>
        <button type="submit" name="login" class="auth-btn">Sign in</button>
      </form>
      <div class="extra-links">
        <a href="forgotpassword.php">Forgot Password?</a><br>
         <span class="text-black">Don’t have an account?</span>
          <a href="#" class="highlight" onclick="toggleForms()"> Sign up</a>
      </div>
    </div>

    <!-- Sign Up Box -->
    <div class="auth-box <?= ($show_form === 'register') ? '' : 'hidden' ?>" id="signup-box">
      <h2>Create Account</h2>
      <p>Fill in the details to register</p>
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
          <input type="password" name="password" placeholder="Enter your password" required>
          <i class="fas fa-eye"></i>
        </div>
        <label>Confirm Password</label>
        <div class="password-container">
          <input type="password" name="confirm_password" placeholder="Confirm your password" required>
          <i class="fas fa-eye"></i>
        </div>
        <button type="submit" name="register" class="auth-btn">Sign up</button>
      </form>
      <div class="extra-links">
        <span class="text-black">Already have an account?</span>
        <a href="#" class="highlight" onclick="toggleForms()"> Login</a>
      </div>
    </div>
  </div>

  <div class="right">
  <video autoplay muted loop style="width:100%; height:100vh; object-fit:cover;">
    <source src="images/CTU promotional Video- Bsit.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  </div>

  <script>
    function toggleForms() {
      document.getElementById('login-box').classList.toggle('hidden');
      document.getElementById('signup-box').classList.toggle('hidden');
    }

    document.querySelectorAll('.password-container i').forEach(icon => {
      icon.addEventListener('click', () => {
        const input = icon.previousElementSibling;
        if (input.type === "password") {
          input.type = "text";
          icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
          input.type = "password";
          icon.classList.replace("fa-eye-slash", "fa-eye");
        }
      });
    });
  </script>

</body>
</html>
