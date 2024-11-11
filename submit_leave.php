<?php
session_start();
include 'database.php';

try {
    if (!isset($_SESSION['employee_id'])) {
        throw new Exception('Employee not logged in.');
    }
    $employee_id = $_SESSION['employee_id'];

    $data = json_decode(file_get_contents("php://input"), true);
    $leave_type = $_GET['leave_type'] ?? null;
    $start_date = $_GET['start_date'] ?? null;
    $end_date = $_GET['end_date'] ?? null;
    $reason = $_GET['reason'] ?? null;


    if (!$leave_type || !$start_date || !$end_date || !$reason) {
        throw new Exception('Missing required fields.');
    }


    $stmt = $conn->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, reason) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $employee_id, $leave_type, $start_date, $end_date, $reason);

    if ($stmt->execute()) {

        $userquery = "SELECT * FROM employees WHERE employee_id = ?";
        $userqueryprep = mysqli_prepare($conn, $userquery);
        mysqli_stmt_bind_param($userqueryprep, 's', $employee_id);
        mysqli_stmt_execute($userqueryprep);
        $userqueryresult = mysqli_stmt_get_result($userqueryprep);
        $user = mysqli_fetch_array($userqueryresult, MYSQLI_ASSOC);


        $notification = $conn->prepare("INSERT INTO notification (message, request_type) VALUES (?, ?)");
        $message = $user['firstname'] . ' ' . $user['lastname'] . " Request Leave Approval";
        $notif_type = "leave_request";
        $notification->bind_param('ss', $message, $notif_type);
        $notification->execute();

        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Database error: ' . $stmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
