<?php
session_start();
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);

    $sql = "UPDATE employees SET employee_status = 'Regular' WHERE employee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    if ($stmt->execute()) {
        echo "Employee successfully unarchived.";
    } else {
        echo "Error unarchiving employee: " . $stmt->error;
    }
}
?>
