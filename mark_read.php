<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ctunav");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION['user_id'] ?? null;
$chatUser = $_GET['user_id'] ?? null;

if ($userID && $chatUser) {
    $chatUser = intval($chatUser);

    // Get conversationID
    $res = $conn->query("SELECT conversationID FROM conversations 
                         WHERE staffID = $userID AND userID = $chatUser LIMIT 1");

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $conversationID = $row['conversationID'];

        $update = $conn->prepare("
            UPDATE messages 
            SET is_read = 1, read_at = NOW() 
            WHERE conversationID = ? AND userID = ? AND is_read = 0
        ");
        $update->bind_param("ii", $conversationID, $chatUser);
        $update->execute();

        echo "success";
        exit;
    }
}

echo "error";
