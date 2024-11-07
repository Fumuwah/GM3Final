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

$query = "UPDATE leave_requests SET status = 'Declined' WHERE request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);

if ($stmt->execute()) {
  echo "Leave request declined successfully.";
} else {
  echo "Error declining leave request: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: hr-leaves-page.php");
exit();
?>
