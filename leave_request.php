<?php
session_start();
include 'database.php';

if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

$employee_id = $_SESSION['employee_id'];
$leave_type = $_POST['leave_type'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$leave_file = $_FILES['leave_file']['name'] ?? '';

// Check if employee belongs to the same project
$project_query = "SELECT project_name FROM employees WHERE employee_id = ?";
$project_stmt = $conn->prepare($project_query);
$project_stmt->bind_param("i", $employee_id);
$project_stmt->execute();
$project = $project_stmt->get_result()->fetch_assoc()['project_name'];

$insert_query = "INSERT INTO leaves (employee_id, leave_type, from_date, to_date, leave_file, project_name) 
                 VALUES (?, ?, ?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_query);
$insert_stmt->bind_param("isssss", $employee_id, $leave_type, $from_date, $to_date, $leave_file, $project);

if ($insert_stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Leave request submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit leave request.']);
}
?>
