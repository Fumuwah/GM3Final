<?php
include 'database.php';

$employee_number = $_GET['employee_number'] ?? '';

$query = "SELECT vacation_leave, sick_leave, leave_without_pay, used_leave, total_leave 
          FROM leaves 
          WHERE employee_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employee_number);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode($data);
?>
