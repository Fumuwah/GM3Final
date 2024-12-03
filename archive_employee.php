<?php
session_start();
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

if (isset($_POST['submit'])) {
    $id = $_POST['archive_id'];
    $reason = $_POST['archive_reason'] ?? '';

    // Check if a reason is provided
    if (empty($reason)) {
        echo "<script>
                alert('Please select a reason to archive.');
                window.history.back();
              </script>";
        exit();
    }

    $sql = "UPDATE employees SET employee_status = 'Archived', archive_reason = ? WHERE employee_number = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $reason, $id);

        if ($stmt->execute()) {
            header("Location: employees.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close();
}
?>
