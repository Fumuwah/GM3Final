<?php
include 'database.php'; // Ensure this file contains the database connection

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$employee_number = $data['employee_number'];

// Query to fetch leave details for the given employee
$query = "SELECT vacation_leave, sick_leave, leave_without_pay, used_leave, 
                 (vacation_leave + sick_leave) AS total_leave
          FROM leave_data 
          WHERE employee_number = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_number);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'vacation_leave' => $row['vacation_leave'],
        'sick_leave' => $row['sick_leave'],
        'leave_without_pay' => $row['leave_without_pay'],
        'used_leave' => $row['used_leave'],
        'total_leave' => $row['total_leave'],
    ]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
