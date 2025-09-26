<?php
// update_staff.php

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ctunav";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $middle_name = $conn->real_escape_string($_POST['middle_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);

    // Update query in users table
    $sql = "UPDATE users 
            SET first_name='$first_name', 
                middle_name='$middle_name', 
                last_name='$last_name', 
                email='$email'
            WHERE userID=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Staff information updated successfully.');
                window.location.href='admin_dashboard.php?page=staffList';
              </script>";
    } else {
        echo "<script>
                alert('Error updating staff: " . $conn->error . "');
                window.location.href='admin_dashboard.php?page=staffList';
              </script>";
    }
}
$conn->close();
?>
