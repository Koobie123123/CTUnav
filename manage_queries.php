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

// ✅ Handle updates for logo/label/description
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

// ✅ Fetch staff details
$query = $conn->query("SELECT s.fullName, d.departmentID, d.name AS department_name, d.description
                      FROM staff s
                      JOIN departments_and_offices d ON s.departmentID = d.departmentID
                      WHERE s.userID = $userID");
$data = $query->fetch_assoc();

$fullName = $data['fullName'] ?? 'Staff';
$department = $data['department_name'] ?? 'N/A';
$departmentID = $data['departmentID'] ?? 'N/A';
$description = $data['description'] ?? '';

// ✅ Fetch only "user" role accounts (for chat list + search)
$sql = "
SELECT u.userID, 
       CONCAT(u.first_name, ' ', u.last_name) AS fullName,
       MAX(m.created_at) AS last_message_time,
       SUM(CASE 
             WHEN m.is_read = 0 
              AND m.userID = u.userID   -- student sent it
              AND c.staffID = ?         -- this staff is the receiver
           THEN 1 ELSE 0 
           END) AS unread_count,
       MAX(CASE 
             WHEN m.is_read = 0 
              AND m.userID = u.userID
              AND c.staffID = ?
           THEN 1 ELSE 0 
           END) AS has_unread
FROM users u
JOIN conversations c 
     ON (c.userID = u.userID AND c.staffID = ?)
LEFT JOIN messages m 
     ON c.conversationID = m.conversationID
WHERE u.role = 'user'
GROUP BY u.userID, fullName
ORDER BY has_unread DESC, last_message_time DESC
";   

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userID, $userID, $userID);
$stmt->execute();
$result = $stmt->get_result();


$allUsers = [];
while ($row = $result->fetch_assoc()) {
    $allUsers[] = $row;
}

// ✅ Get selected chat user (from GET parameter)
$chatUser = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$conversationID = null;

if ($chatUser > 0) {
    // Find conversationID between staff and this user
    $res = $conn->query("SELECT conversationID 
                         FROM conversations 
                         WHERE staffID = $userID AND userID = $chatUser 
                         LIMIT 1");

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $conversationID = $row['conversationID'];
    } else {
        $conversationID = null; // no conversation yet
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Staff Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
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


    #queries .card {
      display: flex;
      height: 100vh;
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
      padding: 7px 15px;
      background-color: #ff7b00;
      color: white;
      border-top-right-radius: 00px;
    }

    #chatBox {
      flex: 1;             /* fills remaining space */
      overflow-y: auto;    /* scroll messages */
      padding: 15px;
      background-color: #f9f9f9;
    }

    .chat-container {
      display: flex;
      flex-direction: column;
      height: 100%; /* full height of chat area */
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
      background-color: #ff7b00;
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

    .message-left { 
      background-color: #e0e0e0; 
      float: left; 
    }

    .message-right { 
      background-color: #ff7b00;
      color: white; 
      float: right;
     }

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

      /* Hide chat form by default */
     #chatForm {
        flex-shrink: 0;       /* prevent shrinking */
        position: sticky;     /* sticky to bottom */
        bottom: 0;
        display: flex;
        flex-direction: column; /* stack preview above input row */
        width: 100%;
        background: #fff;
        border-top: 1px solid #ddd;
        z-index: 10;          
    }

      #previewFile img {
        display: block;
        margin: 10px 0;
        max-width: 150px;
        border: 2px solid #ccc;
        border-radius: 10px;
      }
      #previewFile p {
        margin: 10px 0;
        padding: 5px;
        background: #eee;
        border-radius: 5px;
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

      #filesMenuIcon {
        cursor: pointer;
        font-size: 18px;
        margin-left: auto; /* ensures it's pushed to the far right */
      }



</style>
<body>

<div class="wrapper">
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

<!-- Content (Header + Main) -->
  <div class="content">
    <div class="top-header">
      <i class="fas fa-bars" id="burgerIcon" style="font-size:22px; cursor:pointer;"></i>
      <div class="dropdown" id="userDropdown">
    <i class="fas fa-user-circle" style="cursor:pointer;"></i>
    <div class="dropdown-menu">
      <a href="#"><i class="fas fa-user"></i> Profile</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
    </div>




<!-- Main Chat Area -->
    <div id="queries">
      <div class="card">
        <!-- Left (user list) -->
        <div style="width:280px; background:#f1f1f1; border-right:1px solid #ccc; display:flex; flex-direction:column;">
          <h3 style="text-align:center; background:#ddd; margin:0; padding:7px;">Accounts</h3>
          <input type="text" id="searchUser" placeholder="Search user..." style="padding:6px; margin:5px; width:90%;  border-radius:20px; border:1px solid #ccc;" />

          <ul id="studentList">
        <?php foreach ($allUsers as $u): ?>
          <li class="chat-user" 
              data-user-id="<?= $u['userID'] ?>" 
              style="padding:8px; border-bottom:1px solid #ddd; cursor:pointer; font-weight:<?= ($u['has_unread'] ? 'bold' : 'normal') ?>;">
              <?= htmlspecialchars(trim($u['fullName'])) ?>
              <?php if ($u['unread_count'] > 0): ?>
                <span class="unread-count" style="color:red;">(<?= $u['unread_count'] ?>)</span>
              <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>


    </div>

    <!-- Chat area -->
    <div style="flex:1; display:flex; flex-direction:column;">
     <div class="chat-container">
  <!-- Chat Header -->
  <div id="chatHeader" style="display:flex; justify-content:space-between; align-items:center; padding:10px; background:#ff7b00; color:white;">
    <h3>Select a User</h3>
    <i class="fa-solid fa-ellipsis-vertical" id="filesMenuIcon" onclick="toggleFilesDropdown()" style="cursor:pointer;"></i>
  </div>

  <!-- Messages -->
  <div id="chatBox">
    <p id="noConversationMsg" style="text-align:center; color:gray;">No conversation selected</p>
  </div>

  <!-- Chat Form (sticky at bottom) -->
  <form id="chatForm" onsubmit="event.preventDefault(); sendMessage();" style="display:none; align-items:flex-start; flex-direction:column; padding:10px; background:#fff; border-top:1px solid #ddd;">
    
    <!-- Preview goes first -->
    <div id="previewFile" style="width:100%; margin-bottom:5px;"></div>

    <div style="display:flex; width:100%; gap:10px; align-items:center;">
        <label for="fileInput" style="cursor:pointer; color:gray;">
          <i class="fa fa-file" aria-hidden="true"></i>
        </label>
        <input type="file" id="fileInput" style="display:none;" />

        <input type="text" id="chatInput" placeholder="Type a message..." style="flex:1; padding:10px; border-radius:20px; border:1px solid #ccc;"/>
        <button type="submit" style="padding:10px 20px; background:#ff7b00; color:white; border:none; border-radius:20px; cursor:pointer;">Send</button>
    </div>
</form>
</div>

  </div>
</div>

<script>
  setInterval(refreshUserList, 3000); // every 5s Auto-refresh

</script>

<script>
  // ✅ Pass PHP users list to JS
  const allUsers = <?= json_encode($allUsers); ?>;

  const studentList = document.getElementById('studentList');
  const searchInput = document.getElementById('searchUser');
  const chatBox = document.getElementById('chatBox');

  const studentChats = {};
  let currentUserID = null;
  let selectedFile = null;

 

  // Initial list
  renderUserList(allUsers);

  // Search filter
  searchInput.addEventListener('input', function () {
    const term = this.value.toLowerCase();
    const filtered = allUsers.filter(u =>
      u.fullName.toLowerCase().includes(term) ||
      u.role.toLowerCase().includes(term)
    );
    renderUserList(filtered);
  });

  
 document.getElementById('fileInput').addEventListener('change', function () {
  selectedFile = this.files[0] || null;
  if (selectedFile) {
      selectedFile.name = this.files[0].name; // Ensure name stays the same
  }
  previewSelectedFile();
});

function previewSelectedFile() {
  const preview = document.getElementById('previewFile');
  preview.innerHTML = ''; // clear previous preview
  if (!selectedFile) return;

  const wrapper = document.createElement('div');
  wrapper.style.position = "relative";
  wrapper.style.display = "inline-block";

  // Close button
  const closeBtn = document.createElement('span');
  closeBtn.textContent = "✖";
  closeBtn.style.position = "absolute";
  closeBtn.style.top = "-8px";
  closeBtn.style.right = "-8px";
  closeBtn.style.background = "red";
  closeBtn.style.color = "white";
  closeBtn.style.borderRadius = "50%";
  closeBtn.style.cursor = "pointer";
  closeBtn.style.fontSize = "12px";
  closeBtn.style.padding = "2px 5px";
  closeBtn.onclick = () => {
    selectedFile = null;
    document.getElementById('fileInput').value = "";
    preview.innerHTML = "";
  };

  if (selectedFile.type.startsWith('image/')) {
    const img = document.createElement('img');
    img.src = URL.createObjectURL(selectedFile);
    img.onload = () => URL.revokeObjectURL(img.src);
    img.style.maxWidth = "150px";
    img.style.borderRadius = "10px";
    wrapper.appendChild(img);
  } else {
    const p = document.createElement('p');
    p.textContent = `📄 ${selectedFile.name}`; // show original file name
    p.style.padding = "5px";
    p.style.background = "#eee";
    p.style.borderRadius = "5px";
    wrapper.appendChild(p);
  }

  wrapper.appendChild(closeBtn);
  preview.appendChild(wrapper);
}

// Send message
function sendMessage() {
  const input = document.getElementById('chatInput');
  const message = input.value.trim();
  if (!message && !selectedFile) return;
  if (!currentUserID) {
    alert("Please select a user first.");
    return;
  }

  const formData = new FormData();
  formData.append("user_id", currentUserID);
  formData.append("message", message);

  if (selectedFile) {
      // Ensure the file keeps its original name when sending
      formData.append("file", selectedFile, selectedFile.name);
  }

  fetch("send_message.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(resp => {
    if (resp === "success") {
      input.value = "";
      selectedFile = null;
      document.getElementById('fileInput').value = "";
      document.getElementById('previewFile').innerHTML = "";
      openChat(currentUserID, document.querySelector(`#studentList li[data-id='${currentUserID}']`).textContent);
    }
  });
}


  // Modal
  function openModal() {
    document.getElementById('editModal').style.display = 'flex';
  }
  function closeModal() {
    document.getElementById('editModal').style.display = 'none';
  }
  window.onclick = e => {
    if (e.target === document.getElementById('editModal')) closeModal();
  };

function renderUserList(users) {
  studentList.innerHTML = '';
  if (users.length === 0) {
    studentList.innerHTML = '<li style="text-align:center; color:gray;">No users found</li>';
    return;
  }
  users.forEach(user => {
    const li = document.createElement('li');
    li.textContent = `${user.fullName}`;
    li.dataset.id = user.userID;
    li.onclick = () => openChat(user.userID, user.fullName);

    // Bold if unread
    if (parseInt(user.unread_count) > 0) {
      li.style.fontWeight = "bold";
      li.style.color = "black";
    } else {
      li.style.fontWeight = "normal";
      li.style.color = "#333";
    }

    if (user.userID === currentUserID) li.classList.add('active');
    studentList.appendChild(li);
  });
}

function openChat(userID, name) {
  currentUserID = userID;
  document.querySelectorAll('#studentList li').forEach(li => {
    li.classList.toggle('active', li.dataset.id == userID);
  });
   document.querySelector("#chatHeader h3").textContent = name;

  

    // ✅ Show the chat form only when user selected
  const chatForm = document.getElementById('chatForm');
  chatForm.style.display = "flex";

  // Fetch history via AJAX
  fetch(`fetch_message.php?user_id=${userID}`)
    .then(res => res.text())
    .then(html => {
      chatBox.innerHTML = html;

      // Mark as read in DB
      fetch(`mark_read.php?user_id=${userID}`)
        .then(res => res.text())
        .then(data => {
          if (data.trim() === "success") {
            const li = document.querySelector(`#studentList li[data-id='${userID}']`);
            if (li) {
              li.style.fontWeight = "normal";
              li.style.color = "#333";
            }
          }
        });
    });
}


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
