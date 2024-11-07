<?php
session_start();

include 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
  echo "Session is not set. Redirecting to login...";
  header("Location: login.php");
  exit();
}

if (!isset($_GET['request_id'])) {
  echo "Request ID is not provided.";
  exit();
}

$request_id = $_GET['request_id'];

$conn->begin_transaction();

try {
    $query = "
        SELECT lr.employee_id, lr.leave_type, DATEDIFF(lr.end_date, lr.start_date) + 1 AS leave_days
        FROM leave_requests lr
        WHERE lr.request_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        throw new Exception("Leave request not found.");
    }

    $leave_request = $result->fetch_assoc();
    $employee_id = $leave_request['employee_id'];
    $leave_type = strtolower($leave_request['leave_type']);
    $leave_days = $leave_request['leave_days'];
    echo "Leave type is: " . $leave_type;

    if ($leave_type === 'sick leave') {
        $update_leave_query = "UPDATE leaves SET sick_leave = sick_leave - ?, used_leave = used_leave + ? WHERE employee_id = ?";
    } elseif ($leave_type === 'vacation leave') {
        $update_leave_query = "UPDATE leaves SET vacation_leave = vacation_leave - ?, used_leave = used_leave + ? WHERE employee_id = ?";
    } else {
        throw new Exception("Invalid leave type.");
    }

    $update_leave_stmt = $conn->prepare($update_leave_query);
    $update_leave_stmt->bind_param("iii", $leave_days, $leave_days, $employee_id);

    if (!$update_leave_stmt->execute()) {
        throw new Exception("Error updating leave balances: " . $update_leave_stmt->error);
    }

    $approve_query = "UPDATE leave_requests SET status = 'Approved' WHERE request_id = ?";
    $approve_stmt = $conn->prepare($approve_query);
    $approve_stmt->bind_param("i", $request_id);

    if (!$approve_stmt->execute()) {
        throw new Exception("Error approving leave request: " . $approve_stmt->error);
    }

    $conn->commit();

    echo "Leave request approved and leave balance updated successfully.";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$stmt->close();
$update_leave_stmt->close();
$approve_stmt->close();
$conn->close();

header("Location: hr-leaves-page.php");
exit();
?>
