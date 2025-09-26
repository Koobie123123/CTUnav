<?php
session_start();


// ✅ Check role and department
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    die("Unauthorized access. Please log in.");
}

$staff_department = $_SESSION['departmentID'];

$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['user_id'];
$logoPath = "logo.png";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_logo_label'])) {
    if (!empty($_FILES['new_logo']['tmp_name'])) {
        $target = 'uploads/' . basename($_FILES['new_logo']['name']);
        if (move_uploaded_file($_FILES['new_logo']['tmp_name'], $target)) {
            $logoPath = $target;
        }
    }
    if (!empty($_POST['new_label'])) {
        $panelLabel = htmlspecialchars($_POST['new_label']);
    }
    if (!empty($_POST['new_description'])) {
        $newDesc = $conn->real_escape_string($_POST['new_description']);
        $conn->query("UPDATE departments_and_offices d
                      JOIN staff s ON s.departmentID = d.id
                      SET d.description = '$newDesc'
                      WHERE s.userID = $userID");
    }
}

$query = $conn->query("SELECT s.fullName, d.departmentID, d.name AS department_name, d.description
                      FROM staff s
                      JOIN departments_and_offices d ON s.departmentID = d.departmentID
                      WHERE s.userID = $userID");
$data = $query->fetch_assoc();

$fullName = $data['fullName'] ?? 'Staff';
$department = $data['department_name'] ?? 'N/A';
$departmentID = $data['departmentID'] ?? 'N/A';  // ✅ now defined
$description = $data['description'] ?? '';


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Staff Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
<style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }

    html, body { height: 100vh; }

    body {
      display: flex;
      background-color: #f4f6f8;
    }

    .sidebar {
      width: 250px;
      background: linear-gradient(to bottom, #ffb347, #ff7b00);
      color: white;
      padding: 20px;
      height: 100vh;
      transition: all 0.3s ease;
      overflow: hidden; /* prevent showing content when collapsed */
    }

    .sidebar img {
      width: 100px;
      height: 100px;
      display: block;
      margin: 0 auto 10px;
      border-radius: 50%;
      object-fit: cover;
      background: white;
    }

    .sidebar h2 {
      text-align: center;
      font-size: 18px;
      margin-bottom: 10px;
    }

    .panel-title {
      text-align: center;
      font-size: 14px;
      margin-bottom: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 5px;
    }

    .panel-title i {
      font-size: 10px;
      cursor: pointer;
    }

    .sidebar a {
      display: block;
      padding: 10px;
      margin: 5px 0;
      color: white;
      text-decoration: none;
      border-radius: 20px;
      
    }

    .sidebar a:hover {
      background-color: #f9f3eeff;
      color: #ff7b00;
    }

    .main {
      flex: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    .header {
      background-color: #7c0000;
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .card {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .section { display: none; }

    .stats-box {
      background-color: #ffffff;
      color: #7c0000;
      border-left: 8px solid #7c0000;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      width: 200px;
      text-align: center;
    }

    #queries .card {
      display: flex;
      height: calc(100vh - 40px);
      padding: 0;
    }

    #studentList {
      list-style: none; padding: 0; margin: 0;
    }

    #studentList li {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      cursor: pointer;
    }

    #studentList li:hover{
      background-color: #ddd;
    }

    #studentList li.active {
      background-color: #ddd;
      color: black;
      font-weight: bold;

    }

    #chatHeader {
      padding: 10px 15px;
      background-color: #7c0000;
      color: white;
      border-top-right-radius: 10px;
    }

    #chatBox {
      flex: 1;
      padding: 15px;
      background-color: #f9f9f9;
      overflow-y: auto;
    }

    #queries form {
      display: flex;
      padding: 10px;
      background-color: white;
      border-bottom-right-radius: 10px;
      align-items: center;
    }

    #chatInput {
      flex: 1;
      padding: 10px;
      border-radius: 20px;
      border: 1px solid #ccc;
    }

    #queries button {
      margin-left: 10px;
      padding: 10px 20px;
      background-color: #7c0000;
      color: white;
      border: none;
      border-radius: 20px;
      cursor: pointer;
    }

    .message-left, .message-right {
      padding: 10px 15px;
      border-radius: 20px;
      max-width: 70%;
      margin-bottom: 10px;
      display: inline-block;
      clear: both;
    }

    .message-left { background-color: #e0e0e0; float: left; }
    .message-right { background-color: #7c0000; color: white; float: right; }

    .message-file {
        background-color: transparent;
        border: none;
        padding: 0;
      }
      .message-file img {
        max-width: 200px;
        border-radius: 10px;
        cursor: pointer;
      }
      .message-file a {
        color: #333;
        text-decoration: underline;
        cursor: pointer;
      }


    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      padding: 20px 30px;
      border-radius: 8px;
      width: 300px;
      text-align: center;
    }

    .modal-content input {
      margin-top: 10px;
      padding: 10px;
      width: 100%;
      
    }

     .modal-content button{
      margin-top: 10px;
      padding: 10px;
      background-color: #7c0000;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
     }

    #previewFile img {
      display: block;
      margin-top: 10px;
      max-width: 100px;
      border-radius: 10px;
    }

    #previewFile p {
      margin-top: 10px;
    }

    .top-header {
      background: #f3ededff;
      color: #ee8f00ff;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 4px solid #ff7b00;
    }

    .top-header h1 {
      font-size: 20px;
      font-weight: 600;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 15px;
      font-size: 16px;
    }

    .wrapper {
        display: flex;
        width: 100%;
        height: 100vh;
      }


      .sidebar.closed {
        width: 0;
        padding: 0; /* remove padding when collapsed */
      }

      /* Right side content */
      .content {
        flex: 1;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        width: calc(100% - 250px);
      }

      .content.expanded {
        width: 100%; /* fill screen when sidebar hidden */
      }


      /* Dropdown Menu */
      .dropdown {
        position: relative;
        display: inline-block;
      }

      .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 120%; /* place below the icon */
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        min-width: 160px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        z-index: 1000;
      }

      .dropdown-menu a {
        display: block;
        padding: 10px 15px;
        color: #ff7b00;
        text-decoration: none;
        transition: background 0.2s;
      }

      .dropdown-menu a:hover {
        background: #f1f1f1;
      }

      /* Show menu when active */
      .dropdown.open .dropdown-menu {
        display: block;
      }


</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo" />
  <div class="panel-title">
    <h2><?= htmlspecialchars($department) ?></h2>
    
  </div>
  <a href="staff_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
  <a href="manage_queries.php"><i class="fas fa-envelope"></i> Queries</a>
  <a href="manage_chatbot.php"><i class="fas fa-robot"></i> Manage Chatbot</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Right Side (Header + Main) -->
<div style="flex:1; display:flex; flex-direction:column;">

  <!-- 🔹 Global Header (above main) -->
  <div class="top-header">
  <i class="fas fa-bars" id="burgerIcon" style="font-size:22px; cursor:pointer;"></i>
    <div class="header-actions">
        <div class="dropdown" id="userDropdown">
            <i class="fas fa-user-circle" style="cursor:pointer;"></i>
            <div class="dropdown-menu">
              <a href="staff_profile.php"><i class="fas fa-user"></i> Profile</a>
              <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>

</div>

  <!-- Main Content -->
  <div class="main">
    <div id="dashboard" class="section" style="display: block;">
    
    </div>

    
   


<!-- Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <form method="post" enctype="multipart/form-data">
      

       <label>Description:</label>
      <textarea name="new_description" rows="4" style="margin-top:10px; padding:10px; width:100%;"><?= htmlspecialchars($description) ?></textarea>

      <label>Logo:</label>
      <input type="file" name="new_logo" accept="image/*" />

     

      <button type="submit" name="save_logo_label">Save</button>
      <button type="button" onclick="closeModal()">Cancel</button>
    </form>
  </div>
</div>


<script>
 

  function openModal() {
    document.getElementById('editModal').style.display = 'flex';
  }
  function closeModal() {
    document.getElementById('editModal').style.display = 'none';
  }
  window.onclick = e => {
    if (e.target === document.getElementById('editModal')) closeModal();
  };
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const burgerIcon = document.getElementById('burgerIcon');
  const sidebar = document.querySelector('.sidebar');
  const content = document.querySelector('.content');
  const userDropdown = document.getElementById('userDropdown');

  // Sidebar toggle
  burgerIcon.addEventListener('click', () => {
    sidebar.classList.toggle('closed');
    content.classList.toggle('expanded');
  });

  // Dropdown toggle
  userDropdown.addEventListener('click', (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle('open');
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', () => {
    userDropdown.classList.remove('open');
  });
});

</script>
</body>
</html>

