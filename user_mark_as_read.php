<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ctunav");
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$userID  = $_SESSION['user_id'];
$staffID = intval($_POST['staffID'] ?? 0);

if ($staffID > 0) {
    // ✅ Get the conversation ID
    $sql = "SELECT conversationID 
            FROM conversations 
            WHERE (userID = $userID AND staffID = $staffID)
               OR (userID = $staffID AND staffID = $userID)
            LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        $conversationID = $row['conversationID'];

        // ✅ Update messages as read (only messages not sent by current user)
        $conn->query("UPDATE messages 
                      SET is_read = 1, read_at = NOW() 
                      WHERE conversationID = $conversationID 
                        AND userID != $userID 
                        AND is_read = 0");
        echo "success";
    } else {
        echo "no conversation";
    }
} else {
    echo "invalid staffID";
}
