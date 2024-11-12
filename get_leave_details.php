<?php
require_once "database.php";

if (isset($_GET['employee_id']) && is_numeric($_GET['employee_id'])) {
    $employee_id = (int)$_GET['employee_id'];

    $query = "SELECT sick_leave, vacation_leave, leave_without_pay, used_leave,
                     (sick_leave + vacation_leave) AS total_leave
              FROM leaves
              WHERE employee_id = ?";
              
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $leaveData = $result->fetch_assoc();
            echo json_encode($leaveData);
        } else {
            echo json_encode(null);
        }
        $stmt->close();
    }
}
$conn->close();
?>
