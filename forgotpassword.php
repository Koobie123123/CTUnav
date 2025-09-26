<?php
session_start();
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$showSuccessPopup = false;

// Step 1: Send code
if (isset($_POST['send_code'])) {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $code = rand(100000, 999999);
        $update = $conn->prepare("UPDATE users SET code = ? WHERE email = ?");
        $update->bind_param("ss", $code, $email);
        $update->execute();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'crstyjn@gmail.com'; 
            $mail->Password   = 'dygj ewha ckkw ghnm'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('crstyjn@gmail.com', 'CTU Virtual Tour');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = "<h3>Your password reset code is: <b>$code</b></h3>";

            $mail->send();
            $_SESSION['reset_email'] = $email;
            $message = "✅ Verification code sent to your email.";
        } catch (Exception $e) {
            $message = "❌ Error sending email: " . $mail->ErrorInfo;
        }
    } else {
        $message = "❌ Email not found!";
    }
}

// Step 2: Verify code
if (isset($_POST['verify_code'])) {
    $code = trim($_POST['code']);
    $email = $_SESSION['reset_email'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND code = ?");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $_SESSION['verified'] = true;
        $showSuccessPopup = true; // Trigger JS popup
    } else {
        $message = "❌ Invalid code!";
    }
}

// Step 3: Reset password
if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['verified']) || $_SESSION['verified'] !== true) {
        $message = "❌ Please verify the code first!";
    } else {
        $newPassword = trim($_POST['new_password']);
        $email = $_SESSION['reset_email'];

        // Check if same as old password
        $check = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result()->fetch_assoc();

        if (password_verify($newPassword, $result['password'])) {
            $message = "❌ New password cannot be the same as the old password!";
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ?, code = NULL WHERE email = ?");
            $stmt->bind_param("ss", $hashedPassword, $email);

            if ($stmt->execute()) {
                unset($_SESSION['reset_email'], $_SESSION['verified']);
                $message = "✅ Password has been reset successfully!";
            } else {
                $message = "❌ Error resetting password!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f9fafc;
  margin: 0;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Container full screen */
.container {
  display: flex;
  background: #fff;
  border-radius: 0; /* remove rounded corners since it's fullscreen */
  box-shadow: none; /* optional – remove shadow */
  width: 100%;
  height: 100%;
  overflow: hidden;
}

/* Left illustration */
.illustration {
  flex: 1;
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px; /* more breathing room */
}

.illustration img {
  max-width: 100%;
  height: auto;
}

/* Right form */
.form-section {
  flex: 1;
  padding: 80px;  /* bigger padding */
  display: flex;
  flex-direction: column;
  justify-content: center;
}


    .form-section h2 {
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 10px;
      color: #333;
    }

    .form-section p {
      font-size: 14px;
      margin-bottom: 25px;
      color: #777;
    }

    .form-group {
      margin-bottom: 18px;
      text-align: left;
    }

    .form-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 6px;
      color: #555;
    }

    input[type="email"], input[type="text"], input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      transition: 0.3s;
    }

    input:focus {
      border-color: #f39c12;
      outline: none;
      box-shadow: 0 0 0 2px rgba(243,156,18,0.2);
    }

    .form-submit-btn {
      width: 100%;
      padding: 12px;
      background: #f39c12;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      transition: 0.3s;
    }

    .form-submit-btn:hover {
      background: #d9820f;
    }

    .back-link {
      margin-top: 15px;
      font-size: 14px;
    }

    .back-link a {
      color: #f39c12;
      text-decoration: none;
    }

    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Left side illustration -->
  <div class="illustration">
    <img src="images/Forgot Password Image 3.png" alt="Forgot Password Illustration">
  </div>

  <!-- Right side form -->
  <div class="form-section">
    <h2>Forgot Your Password?</h2>
    <p style="color:#f39c12;"><?= $message ?></p>

    <!-- Step 1: Enter Email -->
    <?php if (!isset($_SESSION['reset_email'])): ?>
    <form method="post">
      <div class="form-group">
        <label>Enter your registered email</label>
        <input type="email" name="email" required>
      </div>
      <button class="form-submit-btn" name="send_code">Send Code</button>
    </form>
    <?php endif; ?>

    <!-- Step 2: Verify Code -->
    <?php if (isset($_SESSION['reset_email']) && !isset($_SESSION['verified'])): ?>
    <form method="post">
      <div class="form-group">
        <label>Enter 6-digit code</label>
        <input type="text" name="code" maxlength="6" required>
      </div>
      <button class="form-submit-btn" name="verify_code">Verify Code</button>
    </form>
    <?php endif; ?>

    <!-- Step 3: Reset Password -->
    <?php if (isset($_SESSION['verified']) && $_SESSION['verified'] === true): ?>
    <form method="post">
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" required>
      </div>
      <button class="form-submit-btn" name="reset_password">Reset Password</button>
    </form>
    <?php endif; ?>

    <p class="back-link"><a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a></p>
  </div>
</div>

<?php if ($showSuccessPopup): ?>
<script>
    alert("✅ Code verified! Click OK to continue.");
    window.location.href = "index.php";
</script>
<?php endif; ?>
</body>
</html>
