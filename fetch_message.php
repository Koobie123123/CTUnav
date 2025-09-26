<?php
session_start();
$staffID = $_SESSION['user_id'];

if (!isset($_GET['user_id'])) {
    exit("No user selected");
}

$userID = intval($_GET['user_id']);
$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";
$conn = new mysqli($host, $user, $pass, $db);

// 1. Ensure conversation exists
$res = $conn->query("SELECT conversationID FROM conversations 
                     WHERE staffID = $staffID AND userID = $userID LIMIT 1");

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $conversationID = $row['conversationID'];
} else {
    $conn->query("INSERT INTO conversations (userID, staffID, created_at, updated_at) 
                  VALUES ($userID, $staffID, NOW(), NOW())");
    $conversationID = $conn->insert_id;
}

// 2. Fetch messages
$q = $conn->query("SELECT * FROM messages WHERE conversationID = $conversationID ORDER BY created_at ASC");
while ($msg = $q->fetch_assoc()) {
    $align = ($msg['userID'] == $staffID) ? "right" : "left";
    echo "<div class='message-$align'><p>".htmlspecialchars($msg['body'])."</p>";
    if (!empty($msg['file_path'])) {
        echo "<a href='".$msg['file_path']."' target='_blank'>📎 File</a>";
    }
    echo "</div>";
}
