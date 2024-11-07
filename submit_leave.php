<?php
session_start();
include 'database.php';

try {
    if (!isset($_SESSION['employee_id'])) {
        throw new Exception('Employee not logged in.');
    }
    $employee_id = $_SESSION['employee_id'];

    $leave_type = $_POST['leave_type'] ?? null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $reason = $_POST['reason'] ?? null;

    if (!$leave_type || !$start_date || !$end_date || !$reason) {
        throw new Exception('Missing required fields.');
    }

    $stmt = $conn->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $employee_id, $leave_type, $start_date, $end_date, $reason);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Database error: ' . $stmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
