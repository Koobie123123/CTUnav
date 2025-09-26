<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get totals
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalStaff = $conn->query("SELECT COUNT(*) AS total FROM staff")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" href="Logo.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    display: flex;
    background-color: #f4f6f8;
  }

  .main {
    flex: 1;
    padding: 20px;
    margin-left: 18%;
  }

  .info-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
  }

  .info-card {
    flex: 1 1 200px;
    display: flex;
    align-items: center;
    padding: 20px;
    background: linear-gradient(135deg, #85190bff, #838382ff);
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    color: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
  }

  .info-card i {
    font-size: 2.5rem;
    margin-right: 20px;
  }

  .card-details h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 500;
  }

  .card-details p {
    margin: 5px 0 0;
    font-size: 1rem;
    font-weight: 600;
  }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
  
<?php if (!isset($_GET['page'])): ?>
  <div class="info-row">
    <div class="info-card">
      <i class="fas fa-users"></i>
      <div class="card-details">
        <h4>Total Users</h4>
        <p><?= $totalUsers ?></p>
      </div>
    </div>

    <div class="info-card">
      <i class="fas fa-user-tie"></i>
      <div class="card-details">
        <h4>Total Staff</h4>
        <p><?= $totalStaff ?></p>
      </div>
    </div>
  </div>
  
<?php endif; ?>



  <!-- Dynamic content loading -->
  <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        if ($page === 'manageAccount') {
            include 'manage_account.php';
        } elseif ($page === 'staffList') {
            include 'manage_staff.php';
        } elseif ($page === 'manageMap') {
            include 'manage_map.php';
        } elseif ($page === 'manageOffices') {
            include 'manage_offices.php';
        } else {
            echo "<p>Page not found.</p>";
        }
    }
  ?>
  
  
</div>

</body>
</html>
