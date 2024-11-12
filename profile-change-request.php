<?php
session_start();
require 'database.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$contactno = $_POST['contactno'];
$address = $_POST['address'];

$current_stmt = $conn->prepare("SELECT password, contactno, address FROM employees WHERE employee_id = ?");
$current_stmt->bind_param("i", $employee_id);
$current_stmt->execute();
$current_result = $current_stmt->get_result()->fetch_assoc();

$requests = [];

if (!empty($password) && $password === $confirm_password) {
    if (!password_verify($password, $current_result['password'])) {
        $requests['password'] = [
            'old_data' => $current_result['password'],
            'new_data' => password_hash($password, PASSWORD_DEFAULT)
        ];
    }
}

if (!empty($contactno) && $contactno != $current_result['contactno']) {
    $requests['contactno'] = [
        'old_data' => $current_result['contactno'],
        'new_data' => $contactno
    ];
}

if (!empty($address) && $address != $current_result['address']) {
    $requests['address'] = [
        'old_data' => $current_result['address'],
        'new_data' => $address
    ];
}

if (!empty($requests)) {
    foreach ($requests as $request_type => $data) {
        $stmt = $conn->prepare("INSERT INTO profile_change_requests (employee_id, request_type, old_data, new_data, status, request_date) VALUES (?, ?, ?, ?, 'Pending', NOW())");
        $stmt->bind_param("isss", $employee_id, $request_type, $data['old_data'], $data['new_data']);
        $stmt->execute();
    }
    echo "Request submitted successfully.";
    header('Location: employee-profile.php');
} else {
    echo "No changes detected.";
    header('Location: employee-profile.php');
}
?>
