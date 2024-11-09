<?php
include 'database.php';

if (isset($_GET['employee_number']) && isset($_GET['fromDay']) && isset($_GET['toDay'])) {
    $employee_number = $_GET['employee_number'];
    $fromDay = $_GET['fromDay'];
    $toDay = $_GET['toDay'];

    $sql = "
        SELECT e.employee_id, CONCAT(e.lastname, ', ', e.firstname, ' ', e.middlename) AS name, 
               e.hire_date, e.basic_salary, p.position_name, r.role_name
        FROM employees e
        LEFT JOIN positions p ON e.position_id = p.position_id
        LEFT JOIN roles r ON e.role_id = r.role_id
        WHERE e.employee_number = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $employee_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();

        $dtr_sql = "
            SELECT 
                SUM(total_hrs) AS total_hrs, 
                SUM(other_ot) AS other_ot 
            FROM dtr 
            WHERE employee_id = ? AND date BETWEEN ? AND ?";
        $dtr_stmt = $conn->prepare($dtr_sql);
        $dtr_stmt->bind_param("iss", $employee['employee_id'], $fromDay, $toDay);
        $dtr_stmt->execute();
        $dtr_result = $dtr_stmt->get_result();

        $dtr_data = $dtr_result->fetch_assoc();

        $response = array_merge($employee, $dtr_data);

        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Employee not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Employee number or date range not provided']);
}
?>
