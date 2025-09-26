<?php
session_start();
$staffID = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = intval($_POST['user_id']);
    $message = trim($_POST['message']);
    $filePath = "";

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "ctunav";
    $conn = new mysqli($host, $user, $pass, $db);

    // Find conversation
    $res = $conn->query("SELECT conversationID FROM conversations 
                         WHERE staffID=$staffID AND userID=$userID LIMIT 1");

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $conversationID = $row['conversationID'];
    } else {
        $conn->query("INSERT INTO conversations (userID, staffID, created_at, updated_at) 
                      VALUES ($userID, $staffID, NOW(), NOW())");
        $conversationID = $conn->insert_id;
    }

    // File upload
    if (!empty($_FILES['file']['tmp_name'])) {
        $target = "uploads/" . time() . "_" . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], $target);
        $filePath = $conn->real_escape_string($target);
    }

    // Save message
    $msg = $conn->real_escape_string($message);
    $conn->query("INSERT INTO messages (conversationID, userID, body, file_path, created_at, is_read) 
                  VALUES ($conversationID, $staffID, '$msg', '$filePath', NOW(), 0)");

    // Update conversation timestamp
    $conn->query("UPDATE conversations SET updated_at = NOW() WHERE conversationID = $conversationID");

    echo "success";
}
