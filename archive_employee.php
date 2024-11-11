<?php
session_start();
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";
if (isset($_POST['submit'])) {
    $id = $_POST['archive_id'];
    $sql = "UPDATE employees SET employee_status = 'Archived' WHERE employee_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: employees.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }


    $stmt->close();
    $conn->close();
}
