<?php
include 'database.php';

$fromDay = $_GET['fromDay'];
$toDay = $_GET['toDay'];
$employee_number = $_GET['employee_number'];

$response = ['total_hrs' => 0, 'other_ot' => 0];

if ($fromDay && $toDay && $employee_number) {
    $sql = "SELECT SUM(total_hrs) AS total_hrs, SUM(other_ot) AS other_ot 
            FROM dtr 
            WHERE date BETWEEN ? AND ? AND employee_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $fromDay, $toDay, $employee_number);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $response['total_hrs'] = $data['total_hrs'] ?? 0;
        $response['other_ot'] = $data['other_ot'] ?? 0;
    } else {
        $response['error'] = 'Failed to fetch data';
    }

    $stmt->close();
} else {
    $response['error'] = 'Invalid input';
}

echo json_encode($response);
?>
