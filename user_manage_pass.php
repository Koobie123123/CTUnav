<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ctunav");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$successMessage = "";
$currentPasswordError = "";
$newPasswordError = "";
$confirmPasswordError = "";

// Fetch user info for profile image
$stmt = $conn->prepare("SELECT profile_image, password FROM users WHERE userID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($profileImage, $hashedPassword);
$stmt->fetch();
$stmt->close();

// Handle password update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = trim($_POST["currentPassword"]);
    $newPassword = trim($_POST["newPassword"]);
    $confirmPassword = trim($_POST["confirmPassword"]);

    // Validate current password
    if (!password_verify($currentPassword, $hashedPassword)) {
        $currentPasswordError = "Incorrect current password.";
    }

    // Validate new password rules
    if (strlen($newPassword) < 8 || 
        !preg_match("/[A-Z]/", $newPassword) || 
        !preg_match("/[0-9]/", $newPassword) || 
        !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $newPassword)) {
        $newPasswordError = "Password must be at least 8 chars, contain uppercase, number, and special character.";
    }

    // Confirm new password
    if ($newPassword !== $confirmPassword) {
        $confirmPasswordError = "Passwords do not match.";
    }

    // If no errors → update DB
    if (empty($currentPasswordError) && empty($newPasswordError) && empty($confirmPasswordError)) {
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE userID = ?");
        $updateStmt->bind_param("si", $newHashedPassword, $userId);

        if ($updateStmt->execute()) {
            $successMessage = "Your password has been updated successfully!";
        } else {
            $confirmPasswordError = "Something went wrong. Please try again.";
        }
        $updateStmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Body */
body {
    background-color: #ffffffa2;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Navbar */
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

.navbar-right img.profile {
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

/* Main Container */
.main-container {
    display: flex;
    flex: 1;
}

/* Sidebar */
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
    border-left: 4px solid #e56c00;
    color: #e56c00;
}

.sidebar a.active {
    background-color: #f0f5ff;
    border-left: 4px solid #e56c00;
    color: #e56c00;
}

/* Content */
.content {
    flex: 1;
    width: 100%;
    padding: 30px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

/* Container */
.container {
    width: 60%;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

/* Header */
.header {
    background: #e56c00;
    color: white;
    padding: 25px 30px;
    text-align: center;
}

.header h1 {
    font-weight: 600;
    font-size: 24px;
}

.header p {
    margin-top: 8px;
    opacity: 0.9;
}

/* Form Container */
.form-container {
    padding: 30px;
}

/* Input Group */
.input-group {
    position: relative;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.input-group input {
    width: 100%;
    padding: 8px 8px 8px 8px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s;
}

.input-group input:focus {
    border-color: #e56c00;
    outline: none;
}

.input-group i {
    position: absolute;
    right: 15px;
    top: 40px;
    color: #777;
    cursor: pointer;
}

.input-group i.toggle-password {
    position: absolute;
    right: 15px;
    top: 42px;
    cursor: pointer;
    color: #777;
}

.input-group i.toggle-password:hover {
    color: #e56c00;
}

/* Error Message */
.error-message {
    color: #e74c3c;
    font-size: 13px;
    margin-top: 5px;
    min-height: 18px;
}

/* Password Rules */
.password-rules {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    font-size: 13px;
}

.password-rules ul {
    list-style: none;
    margin-top: 10px;
}

.password-rules li {
    margin-bottom: 6px;
    color: #555;
}

.password-rules li.valid {
    color: #2ecc71;
}

.password-rules li i {
    margin-right: 5px;
}

/* Button */
button {
    width: 100%;
    padding: 15px;
    background: #e56c00;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
}

button:hover {
    background: #c26112ff;
}

/* Success Message */
.success-message {
    background: #2ecc71;
    color: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    margin-top: 20px;
    display: none;
}

/* Media Queries */

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
    .main-container {
        flex-direction: column;
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
        border-bottom: 3px solid #e56c00;
    }

    .content {
        padding: 20px;
    }

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
}

@media (max-width: 480px) {
    .container {
        border-radius: 12px;
    }

    .form-container {
        padding: 20px;
    }

    .navbar {
        padding: 0 10px;
    }

    .navbar-left .logo-text {
        font-size: 14px;
    }
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

    <div class="main-container">
        <nav class="sidebar">
            <a href="profile.php">Manage Account</a>
            <a href="user_manage_pass.php" class="active">Manage Password</a>
            <a href="logout.php">Log Out</a>
        </nav>
        
        <section class="content">
            <div class="container" style="margin: auto;"> <!-- ✅ keeps it centered -->
                <div class="header">
                    <h1>Change Your Password</h1>
                    <p>Keep your account secure with a strong password</p>
                </div>
                
                <div class="form-container">
                    <form method="POST">
                        <div class="input-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" name="currentPassword" id="currentPassword" placeholder="Enter your current password">
                            <i class="fas fa-eye toggle-password" data-target="currentPassword"></i>
                            <div class="error-message"><?php echo $currentPasswordError; ?></div>
                        </div>
                        
                        <div class="input-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" name="newPassword" id="newPassword" placeholder="Create a new password">
                            <i class="fas fa-eye toggle-password" data-target="newPassword"></i>
                            <div class="error-message"><?php echo $newPasswordError; ?></div>
                        </div>
                        
                        <div class="input-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm your new password">
                            <i class="fas fa-eye toggle-password" data-target="confirmPassword"></i>
                            <div class="error-message"><?php echo $confirmPasswordError; ?></div>
                        </div> 
                        
                        <button type="submit">Update Password</button>
                        
                        <?php if (!empty($successMessage)): ?>
                        <div class="success-message" style="display:block;">
                            <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <script>
document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", function() {
        const input = document.getElementById(this.getAttribute("data-target"));
        if (input.type === "password") {
            input.type = "text";
            this.classList.remove("fa-eye");
            this.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            this.classList.remove("fa-eye-slash");
            this.classList.add("fa-eye");
        }
    });
});
</script>

</body>
</html>