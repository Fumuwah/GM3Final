<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $day = $_POST['day'];

    $query = "SELECT * FROM dtr WHERE project_id = ? AND month = ? AND year = ?";
    if (!empty($day)) {
        $query .= " AND day = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiii", $project_id, $month, $year, $day);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $project_id, $month, $year);
    }

    $query = "
    SELECT dtr.*, employees.lastname, employees.firstname 
    FROM dtr
    JOIN employees ON dtr.employee_number = employees.employee_number
    WHERE dtr.project_id = ?"; // Or any other condition

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table'>";
        echo "<thead><tr>
                <th>#</th>
                <th>Day</th>
                <th>Name</th>
                <th>Time-In</th>
                <th>Time-Out</th>
                <th>OT Hrs</th>
                <th>Total Hrs</th>
                <th>Action</th>
            </tr></thead><tbody>";
        $count = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$count}</td>
                    <td>{$row['day']}</td>
                    <td>{$row['employee_id']}</td>
                    <td>{$row['time_in']}</td>
                    <td>{$row['time_out']}</td>
                    <td>{$row['other_ot']}</td>
                    <td>{$row['total_hrs']}</td>
                    <td><a href='#' class='btn btn-success'>Edit</a></td>
                </tr>";
            $count++;
        }
        echo "</tbody></table>";
    } else {
        echo "No records found.";
    }
}
?>
