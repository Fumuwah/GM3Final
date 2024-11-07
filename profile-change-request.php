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

// Prepare the request data
$requests = [];
if (!empty($password) && $password === $confirm_password) {
    $requests['password'] = password_hash($password, PASSWORD_DEFAULT); // Hash the password
}
if (!empty($contactno) && $contactno != $employee['contactno']) {
    $requests['contactno'] = $contactno;
}
if (!empty($address) && $address != $employee['address']) {
    $requests['address'] = $address;
}

// Insert the request into a change requests table
if (!empty($requests)) {
    $stmt = $conn->prepare("INSERT INTO profile_change_requests (employee_id, request_data, status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("is", $employee_id, json_encode($requests));
    $stmt->execute();
    echo "Request submitted successfully.";
} else {
    echo "No changes detected.";
}
?>
