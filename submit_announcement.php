<?php
session_start();
include('database.php');

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['reason']);
    $created_by = $_SESSION['employee_id'];

    if (!empty($message)) {
        $query = "INSERT INTO announcements (message, created_by) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $message, $created_by);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Announcement added successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to add announcement.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Message cannot be empty.";
    }
}
header("Location: index.php");
exit();
?>
