<?php
session_start();
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

if (isset($_POST['submit'])) {
    $id = $_POST['unarchive_id'];
    $status = $_POST['employee_status'] ?? '';

    // Validate that a status is selected
    if (empty($status)) {
        echo "<script>
                alert('Please select a valid employee status.');
                window.history.back();
              </script>";
        exit();
    }

    // Update employee status and reset archive reason
    $sql = "UPDATE employees SET employee_status = ?, archive_reason = 'None' WHERE employee_number = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $status, $id);

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
