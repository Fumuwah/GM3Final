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
        $conn->begin_transaction(); // Start transaction
        try {
            // Insert the announcement
            $query = "INSERT INTO announcements (message, created_by) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $message, $created_by);

            if ($stmt->execute()) {
                // Fetch user details for the notification
                $user_query = "SELECT firstname, lastname FROM employees WHERE employee_id = ?";
                $user_stmt = $conn->prepare($user_query);
                $user_stmt->bind_param("i", $created_by);
                $user_stmt->execute();
                $user_result = $user_stmt->get_result();

                if ($user_result->num_rows > 0) {
                    $user = $user_result->fetch_assoc();
                    $fullname = $user['firstname'] . ' ' . $user['lastname'];

                    // Insert the notification
                    $notification_query = "INSERT INTO notification (message, request_type) VALUES (?, ?)";
                    $notif_stmt = $conn->prepare($notification_query);
                    $notif_message = $fullname . " posted a new announcement.";
                    $notif_type = "announcement";
                    $notif_stmt->bind_param("ss", $notif_message, $notif_type);
                    
                    if (!$notif_stmt->execute()) {
                        throw new Exception("Failed to insert notification: " . $notif_stmt->error);
                    }
                } else {
                    throw new Exception("User not found for employee ID: " . $created_by);
                }
                $_SESSION['success_message'] = "Announcement added successfully and notification sent.";
            } else {
                throw new Exception("Failed to add announcement: " . $stmt->error);
            }

            $conn->commit(); // Commit transaction
        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction on error
            $_SESSION['error_message'] = $e->getMessage();
        } finally {
            $stmt->close();
            $user_stmt->close();
            if (isset($notif_stmt)) {
                $notif_stmt->close();
            }
        }
    } else {
        $_SESSION['error_message'] = "Message cannot be empty.";
    }
}

header("Location: index.php");
exit();
?>
