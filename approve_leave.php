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
    $check_query = "SELECT status from leave_requests WHERE request_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $request_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if($check_row && $check_row['status'] === 'Approved') {
        throw new Exception("This leave request has already been approved.");
    }

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

$leave_balance_query = "SELECT sick_leave, vacation_leave, leave_without_pay FROM leaves WHERE employee_id = ?";
$balance_stmt = $conn->prepare($leave_balance_query);
$balance_stmt->bind_param("i", $employee_id);
$balance_stmt->execute();
$balance_result = $balance_stmt->get_result();
$balance = $balance_result->fetch_assoc();

if (!$balance) {
    throw new Exception("Unable to retrieve leave balances.");
}

$sick_leave = $balance['sick_leave'];
$vacation_leave = $balance['vacation_leave'];
$leave_without_pay = $balance['leave_without_pay'];

if ($leave_type === 'sick_leave') {
    if ($sick_leave >= $leave_days) {
        $update_leave_query = "UPDATE leaves SET sick_leave = sick_leave - ?, used_leave = used_leave + ? WHERE employee_id = ?";
        $update_leave_stmt = $conn->prepare($update_leave_query);
        $update_leave_stmt->bind_param("iii", $leave_days, $leave_days, $employee_id);
    }else {
        $leave_without_pay_days = $leave_days - $sick_leave;
        $update_leave_query = "UPDATE leaves SET sick_leave = 0, leave_without_pay = leave_without_pay + ?, used_leave = used_leave + ? WHERE employee_id = ?";
        $update_leave_stmt = $conn->prepare($update_leave_query);
        $update_leave_stmt->bind_param("iii", $leave_without_pay_days, $sick_leave, $employee_id);
    }
} elseif ($leave_type === 'vacation_leave') {
    if ($vacation_leave >= $leave_days) {
        $update_leave_query = "UPDATE leaves SET vacation_leave = vacation_leave - ?, used_leave = used_leave + ? WHERE employee_id = ?";
        $update_leave_stmt = $conn->prepare($update_leave_query);
        $update_leave_stmt->bind_param("iii", $leave_days, $leave_days, $employee_id);
    }else {
        $leave_without_pay_days = $leave_days - $vacation_leave;
        $update_leave_query = "UPDATE leaves SET vacation_leave = 0, leave_without_pay = leave_without_pay + ?, used_leave = used_leave + ? WHERE employee_id = ?";
        $update_leave_stmt = $conn->prepare($update_leave_query);
        $update_leave_stmt->bind_param("iii", $leave_without_pay_days, $vacation_leave, $employee_id);
    }
} else {
    throw new Exception("Invalid leave type!");
}

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
$balance_stmt->close();
$update_leave_stmt->close();
$approve_stmt->close();
$conn->close();

header("Location: hr-leaves-page.php");
exit();
?>
