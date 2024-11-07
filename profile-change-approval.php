<?php
require 'database.php';

// Assume $request_id and $action ('approve' or 'decline') come from the form submission.
$request_id = $_POST['request_id'];
$action = $_POST['action'];

// Fetch request details
$stmt = $conn->prepare("SELECT employee_id, request_data FROM profile_change_requests WHERE request_id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

if ($action === 'approve') {
    $employee_id = $request['employee_id'];
    $updates = json_decode($request['request_data'], true);
    
    // Prepare dynamic SQL for updates
    $sql = "UPDATE employees SET ";
    $params = [];
    foreach ($updates as $field => $value) {
        $sql .= "$field = ?, ";
        $params[] = $value;
    }
    $sql = rtrim($sql, ', ') . " WHERE employee_id = ?";
    $params[] = $employee_id;

    // Execute update
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    echo "Changes approved and updated.";
} else {
    echo "Request declined.";
}

// Update the status of the request
$stmt = $conn->prepare("UPDATE profile_change_requests SET status = ? WHERE request_id = ?");
$stmt->bind_param("si", $action, $request_id);
$stmt->execute();
?>
