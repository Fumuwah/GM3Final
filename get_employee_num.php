<?php
include 'database.php'; // Make sure you have database connection

if (isset($_POST['employee_number'])) {
    $employee_number = $_POST['employee_number'];

    // Query to get matching employee numbers
    $query = "SELECT employee_id, employee_number FROM employees WHERE employee_number LIKE ?";
    $stmt = $conn->prepare($query);
    $like = "%" . $employee_number . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

    // Generate a list of employee number suggestions
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="suggestion-item" data-id="' . $row['employee_id'] . '">' . $row['employee_number'] . '</div>';
        }
    } else {
        echo '<div>No matching employee numbers found</div>';
    }
}
?>
