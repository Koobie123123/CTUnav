<?php
session_start();

// ✅ Ensure staff logged in
if (!isset($_SESSION['departmentID'])) {
    die("Unauthorized access. Please log in.");
}
$staff_department = (int) $_SESSION['departmentID']; // cast to int for safety

// ✅ DB Connection
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


// ✅ Handle Add FAQ
if (isset($_POST['add_faq'])) {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $link = $_POST['link'];

    $stmt = $conn->prepare("INSERT INTO faqs (departmentID, question, answer, link) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $staff_department, $question, $answer, $link);
    $stmt->execute();
    $stmt->close();
}

// ✅ Handle Edit FAQ
if (isset($_POST['edit_faq'])) {
    $faq_id = (int)$_POST['faq_id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $link = $_POST['link'];

    $stmt = $conn->prepare("UPDATE faqs SET question=?, answer=?, link=? WHERE faqID=? AND departmentID=?");
    $stmt->bind_param("sssii", $question, $answer, $link, $faq_id, $staff_department);
    $stmt->execute();
    $stmt->close();
}

// ✅ Handle Delete FAQ
if (isset($_POST['delete_faq'])) {
    $faq_id = (int)$_POST['faq_id'];

    $stmt = $conn->prepare("DELETE FROM faqs WHERE faqID=? AND departmentID=?");
    $stmt->bind_param("ii", $faq_id, $staff_department);
    $stmt->execute();
    $stmt->close();
}

// ✅ Fetch FAQs for this staff’s department only
$stmt = $conn->prepare("SELECT * FROM faqs WHERE departmentID=? ORDER BY faqID ASC");
$stmt->bind_param("i", $staff_department);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Staff Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <link rel="stylesheet" href="CSS/staff_dashboard.css" />
  <link rel="stylesheet" href="CSS/manage-chatbot.css" />

</head>
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

    /* Layout */
.faq-wrapper {
    display: flex;
    gap: 20px;
    margin: 20px;
}

/* Card Style */
.faq-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    flex: 1;
}

/* Table Styling */
.faq-table {
    width: 100%;
    border-collapse: collapse;
}
.faq-table th {
    background: #ff7b00;
    color: white;
    padding: 12px;
    text-align: left;
}
.faq-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}
.faq-table tr:nth-child(even) {
    background: #f9f9f9;
}

/* Actions with icons */
.actions {
    display: flex;
     flex-direction: row;   /* ➡ side by side */
    gap: 6px;                /* spacing between buttons */
    align-items: center; /* align to left inside cell */
}

.icon-btn {
    border: none;
    background: none;
    cursor: pointer;
    font-size: 16px;
}
.icon-btn.edit-btn {
    color: #007bff;
}
.icon-btn.delete-btn {
    color: #d9534f;
}
.icon-btn:hover {
    opacity: 0.7;
}

/* Add FAQ Form */
.faq-add input,
.faq-add textarea {
    width: 100%;
    margin-bottom: 12px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
}
.btn-add {
   
    
    background: #ffffffff;
    color: #f29130ff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.btn-add:hover {
    background: #fbf8f5ff;
}


    .main {
      flex: 1;
      padding: 20px;
      display: flex;
      flex-direction: column;
      height: 100vh;
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
      align-items: start;
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

/* Modal */
.modal {
  display: none; 
  position: fixed; 
  z-index: 2000; 
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center; 
  align-items: center;
}

.modal-content {
  background: #ffffffff;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  position: relative;
}

.modal-content h3 {
  margin-bottom: 15px;
}

.modal-content .close {
  position: absolute;
  top: 10px; right: 15px;
  font-size: 20px;
  cursor: pointer;
}

.modal-content input,
.modal-content textarea { 
  width: 100%;
  margin-bottom: 12px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
}

.modal-content .close {
  color: #aaa;
}

button type="button" {
  cursor: pointer;
  background-color: #ff7b00;
} 


</style>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo" />
  <div class="panel-title">
    <h2><?= htmlspecialchars($department) ?></h2>
    
  </div>
  <a href="staff_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
  <a href="manage_queries.php" ><i class="fas fa-envelope"></i> Queries</a>
  <a href="manage_chatbot.php" ><i class="fas fa-robot"></i> Manage Chatbot</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Right Side (Header + Main) -->
<div style="flex:1; display:flex; flex-direction:column;">

  <!-- 🔹 Global Header (above main) -->
  <!-- 🔹 Global Header (above main) -->
  <div class="top-header">
  <i class="fas fa-bars" id="burgerIcon" style="font-size:22px; cursor:pointer;"></i>
    <div class="header-actions">
        <div class="dropdown" id="userDropdown">
            <i class="fas fa-user-circle" style="cursor:pointer;"></i>
            <div class="dropdown-menu">
              <a href="#"><i class="fas fa-user"></i> Profile</a>
              <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>

</div>
<!-- FAQ Wrapper -->
<div class="faq-wrapper">
    <!-- Existing FAQs -->
    <div class="faq-card" style="flex:1;">
      
       <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
  <h3 style="margin:0;">FAQ List</h3>
  <button class="btn-add" onclick="openAddModal()">+ Add New FAQ</button>
</div>



<table class="faq-table">

        <table class="faq-table">
            <thead>
                <tr>
                    
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Link</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= htmlspecialchars($row['question']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['answer'])) ?></td>
        <td>
            <?= $row['link'] ? "<a href='" . htmlspecialchars($row['link']) . "' target='_blank'>View</a>" : '-' ?>
        </td>
        <td class="actions">
            <!-- Edit -->
            <form method="POST" class="inline-form">
                <input type="hidden" name="faq_id" value="<?= $row['faqID'] ?>">
                <button type="button" class="icon-btn edit-btn" onclick="openEditModal(<?= $row['faqID'] ?>)">
                    <i class="fas fa-pen"></i>
                </button>
            </form>

            <!-- Delete -->
            <form method="POST" class="inline-form" onsubmit="return confirm('Delete this FAQ?');">
                <input type="hidden" name="faq_id" value="<?= $row['faqID'] ?>">
                <button type="submit" name="delete_faq" class="icon-btn delete-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    <?php } ?>
</tbody>

        </table>
    </div>
</div>

<!-- 🔹 Add FAQ Modal -->
<div id="addFaqModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeAddModal()">&times;</span>
    <h3>Add New FAQ</h3>
    <form method="POST" class="add-form">
        <input type="text" name="question" placeholder="Enter question" required>
        <textarea name="answer" placeholder="Enter answer" required></textarea>
        <input type="text" name="link" placeholder="Optional: link (http://...)">
        <button type="submit" name="add_faq" class="btn-add">Add FAQ</button>
    </form>
  </div>
</div>


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

// Add FAQ modal
function openAddModal() {
  document.getElementById('addFaqModal').style.display = 'flex';
}
function closeAddModal() {
  document.getElementById('addFaqModal').style.display = 'none';
}

// Close modal if clicking outside
window.onclick = function(event) {
  const modal = document.getElementById('addFaqModal');
  if (event.target === modal) {
    modal.style.display = 'none';
  }
}

</script>

</body>
</html>

