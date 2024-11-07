<?php
session_start();  // Start the session
include 'database.php';

try {
    // Retrieve the employee_id from the session
    if (!isset($_SESSION['employee_id'])) {
        throw new Exception('Employee not logged in.');
    }
    $employee_id = $_SESSION['employee_id'];

    // Retrieve form data from POST request
    $leave_type = $_POST['leave_type'] ?? null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $reason = $_POST['reason'] ?? null;

    // Ensure all required fields are present
    if (!$leave_type || !$start_date || !$end_date || !$reason) {
        throw new Exception('Missing required fields.');
    }

    // Prepare and execute the INSERT query
    $stmt = $conn->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $employee_id, $leave_type, $start_date, $end_date, $reason);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Database error: ' . $stmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);  // Send proper error response
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
