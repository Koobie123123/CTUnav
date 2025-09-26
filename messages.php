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

/* 🔹 1. Get or Create Conversation */
function getConversationID($conn, $userID, $staffID) {
    $stmt = $conn->prepare("SELECT conversationID FROM conversations WHERE userID = ? AND staffID = ?");
    $stmt->bind_param("ii", $userID, $staffID);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        return $row['conversationID']; // existing convo
    } else {
        // create new
        $stmt = $conn->prepare("INSERT INTO conversations (userID, staffID, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->bind_param("ii", $userID, $staffID);
        $stmt->execute();
        return $stmt->insert_id;
    }
}

/* 🔹 2. Handle AJAX: Load messages */
/* 🔹 2. Handle AJAX: Load messages */
if (isset($_GET['action']) && $_GET['action'] === 'load' && isset($_GET['receiverID'])) {
    $staffID = intval($_GET['receiverID']);
    $conversationID = getConversationID($conn, $userID, $staffID);

    $stmt = $conn->prepare("
        SELECT m.messageID, m.userID, m.body, m.file_path, m.created_at, u.first_name, u.last_name
        FROM messages m
        JOIN users u ON m.userID = u.userID
        WHERE m.conversationID = ?
        ORDER BY m.created_at ASC
    ");
    $stmt->bind_param("i", $conversationID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='text-align:center; color:#666;'>No conversations yet.</p>";
    } else {
        while ($row = $result->fetch_assoc()) {
            $class = ($row['userID'] == $userID) ? "sent" : "received";
            echo "<div class='message {$class}'>";

            if (!empty($row['body'])) {
                echo htmlspecialchars($row['body']) . "<br>";
            }

            if (!empty($row['file_path'])) {
                $ext = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo "<img src='" . htmlspecialchars($row['file_path']) . "' style='max-width:150px; display:block; margin-top:5px;' />";
                } else {
                    echo "<a href='" . htmlspecialchars($row['file_path']) . "' target='_blank'>📎 Download File</a>";
                }
            }

            echo "</div>";
        }
    }
    exit();
}


/* 🔹 3. Handle AJAX: Send message */
/* 🔹 3. Handle AJAX: Send message */
/* 🔹 Send message + files */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiverID'])) {
    $staffID = intval($_POST['receiverID']);
    $msg = trim($_POST['message'] ?? "");
    $conversationID = getConversationID($conn, $userID, $staffID);

    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $hasInserted = false;

    // Insert text message if any
    if ($msg !== "") {
        $stmt = $conn->prepare("INSERT INTO messages (conversationID, userID, body, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iis", $conversationID, $userID, $msg);
        $stmt->execute();
        $hasInserted = true;
    }
  }
  
    // Insert each uploaded file
    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['name'] as $key => $name) {
            if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = time() . "_" . basename($name);
                $target = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $target)) {
                    $filePath = $conn->real_escape_string($target);
                    $stmt = $conn->prepare("INSERT INTO messages (conversationID, userID, file_path, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)");
                    $stmt->bind_param("iis", $conversationID, $userID, $filePath);
                    $stmt->execute();
                    $hasInserted = true;
                }
            }
        }
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiverID'])) {
    $staffID = intval($_POST['receiverID']);
    $msg = trim($_POST['message'] ?? "");
    $conversationID = getConversationID($conn, $userID, $staffID);

    // Handle multiple files
    $uploadedFiles = [];
    if (!empty($_FILES['files']['name'][0])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['files']['name'] as $key => $name) {
            $fileName = time() . "_" . basename($name);
            $target = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $target)) {
                $uploadedFiles[] = $conn->real_escape_string($target);
            }
        }
    }

    // Insert text message if any
    if ($msg !== "" || empty($uploadedFiles)) {
        $stmt = $conn->prepare("INSERT INTO messages (conversationID, userID, body, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iis", $conversationID, $userID, $msg);
        $stmt->execute();
    }

    // Insert each file as a separate message
    foreach ($uploadedFiles as $filePath) {
        $stmt = $conn->prepare("INSERT INTO messages (conversationID, userID, file_path, created_at, is_read) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iis", $conversationID, $userID, $filePath);
        $stmt->execute();
    }

    $stmt = $conn->prepare("UPDATE conversations SET updated_at = NOW() WHERE conversationID = ?");
    $stmt->bind_param("i", $conversationID);
    $stmt->execute();

    exit("success");
}

/* 🔹  Handle AJAX: Load files in a conversation */
if (isset($_GET['action']) && $_GET['action'] === 'files' && isset($_GET['receiverID'])) {
    $staffID = intval($_GET['receiverID']);
    $conversationID = getConversationID($conn, $userID, $staffID);

    $stmt = $conn->prepare("
        SELECT messageID, file_path, 
               SUBSTRING_INDEX(file_path, '/', -1) AS file_name, 
               created_at
        FROM messages
        WHERE conversationID = ? AND file_path IS NOT NULL
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $conversationID);
    $stmt->execute();
    $result = $stmt->get_result();

    $files = [];
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }

    header("Content-Type: application/json");
    echo json_encode($files);
    exit();
}



/* 🔹 4. Page Render (same as yours, no major changes except cleaned up query) */

// Fetch user info
$stmt = $conn->prepare("SELECT profile_image, first_name, last_name FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$defaultImage = 'images/user.png';
$profileImage = (!empty($user['profile_image']) && file_exists($user['profile_image'])) ? $user['profile_image'] : $defaultImage;
$fullName = $user['first_name'] . " " . $user['last_name'];

// Fetch staff list with unread + latest message order
$sql = "
  SELECT s.userID, u.first_name, u.last_name, d.name AS department_name,
         MAX(m.created_at) AS last_message_time,
         MAX(CASE WHEN m.is_read = 0 AND m.userID != ? THEN 1 ELSE 0 END) AS has_unread
  FROM staff s
  JOIN users u ON u.userID = s.userID
  JOIN departments_and_offices d ON s.departmentID = d.departmentID
  JOIN conversations c ON (c.userID = ? AND c.staffID = s.userID)
                       OR (c.staffID = ? AND c.userID = s.userID)
  LEFT JOIN messages m ON c.conversationID = m.conversationID
  GROUP BY s.userID, u.first_name, u.last_name, d.name
  ORDER BY last_message_time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userID, $userID, $userID);
$stmt->execute();
$result = $stmt->get_result();

$staffList = [];
while ($row = $result->fetch_assoc()) {
    $staffList[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Messages - CTU Tuburan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="messages.css" />

  <style>
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-left">
        <!-- Logo clickable -->
        <a href="index.php">
            <img src="images/Logo.png" alt="Logo" />
        </a>
        <span class="logo-text">CTU-TUBURAN</span>
    </div>
    <div class="navbar-right">
        <!-- Home icon -->
        <a href="user_dashboard.php">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
        </a>

        <!-- Chat icon -->
        <a href="messages.php">
            <svg class="icon" viewBox="0 0 24 24">
                <path d="M20 2H4a2 2 0 0 0-2 2v16l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
            </svg>
        </a>

      <!-- Profile image -->
        <a href="profile.php">
            <img class="profile" src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile" />
        </a>

    </div>
</nav>

<div class="container">
  <!-- Sidebar Staff List -->
<div class="sidebar">
  <h3>Staff List</h3>
  <ul id="staffList">
    <?php foreach ($staffList as $staff): ?>
      <?php $isUnread = $staff['has_unread'] == 1; ?>
      <li onclick="openChat(<?= $staff['userID'] ?>, 
                            '<?= $staff['first_name'] . " " . $staff['last_name'] ?>', 
                            '<?= $staff['department_name'] ?>')">
        <div>
          <strong style="<?= $isUnread ? 'font-weight:bold;' : 'font-weight:normal;' ?>">
            <?= htmlspecialchars($staff['first_name'] . " " . $staff['last_name']) ?>
          </strong><br>
          <small style="font-size: 12px; color: #666; <?= $isUnread ? 'font-weight:bold;' : 'font-weight:normal;' ?>">
            <?= htmlspecialchars($staff['department_name']) ?>
          </small>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>


  <!-- Chat Area -->
<!-- Chat Area -->
<div class="chat-area">
  <div class="chat-header">
    <div id="chatWith">
      Select a staff to chat
    </div>
    <i class="fa-solid fa-ellipsis-vertical" id="filesMenuIcon" onclick="toggleFilesDropdown()"></i>
  </div>

  <div class="chat-box" id="chatBox">
    <p style="text-align:center;">No conversation selected</p>
  </div>

  <div id="filePreview"></div>

  <form class="chat-input" onsubmit="sendMessage(event)" enctype="multipart/form-data">
    <input type="hidden" id="receiverID">

    <!-- Hidden file input -->
    <input type="file" id="fileInput" multiple style="display:none;" />

    <!-- Paperclip icon -->
    <label for="fileInput" style="cursor:pointer; margin-right:8px;">
      <i class="fa-solid fa-paperclip"></i>
    </label>

    <input type="text" id="chatInput" placeholder="Type a message..." />
    <button type="submit">Send</button>
  </form>

  <!-- Files Dropdown -->
<div id="filesDropdown" class="files-dropdown hidden">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    <h4 style="margin:0;">Conversation Attachments</h4>
    <button onclick="closeFilesDropdown()" 
            style="background:none; border:none; font-size:18px; cursor:pointer;">✖</button>
  </div>
  <div id="filesGrid"></div>
  <p id="noFilesMsg" class="no-files">No files in this conversation yet.</p>
</div>
</div>
</div>


<script>
  function closeFilesDropdown() {
  const dropdown = document.getElementById("filesDropdown");
  dropdown.classList.add("hidden");
}
</script>

<script src="user JS/messages.js"></script>
</body>
</html>
