<?php
session_start();
include 'database.php';  // Ensure this file connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_number = $_POST['employee_number'];
    $basic_salary = $_POST['basic_salary'];
    $allowance = $_POST['allowance'];
    $total_hours = $_POST['total_hours'];
    $other_ot = $_POST['other_ot'];
    $deductions = $_POST['deductions'];
    $incentives = $_POST['incentives'];
    $gross = $_POST['gross'];

    // Get employee ID from the employee number
    $query = $conn->prepare("SELECT employee_id FROM employees WHERE employee_number = ?");
    $query->bind_param("s", $employee_number);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        $employee_id = $employee['employee_id'];

        // Calculate net pay
        $netpay = $gross - $deductions;

        // Insert payroll data
        $stmt = $conn->prepare("INSERT INTO payroll (employee_id, basic_salary, allowance, total_hours, other_ot, gross, deductions, netpay) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iddddidd", $employee_id, $basic_salary, $allowance, $total_hours, $other_ot, $gross, $deductions, $netpay);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Payroll successfully processed."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to process payroll."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Employee not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
