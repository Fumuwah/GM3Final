<?php
require_once "database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employeeId = $_POST['id'];

    if (!empty($employeeId)) {
        $sql = "UPDATE employees SET employee_status = 'Archived' WHERE employee_number = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $employeeId);
            
            if (mysqli_stmt_execute($stmt)) {
                echo "Employee archived successfully.";
            } else {
                echo "Error executing query: " . mysqli_stmt_error($stmt);
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo "Error preparing query: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid employee ID.";
    }

    mysqli_close($conn);
} else {
    echo "Invalid request.";
}
?>
