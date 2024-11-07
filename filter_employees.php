<?php
require_once 'database.php'; // Ensure this path is correct

// Retrieve filter parameters from GET request
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$position = isset($_GET['position']) ? $_GET['position'] : '';

// Base SQL query: By default, we exclude 'Archived' employees
$sql = "SELECT * FROM employees WHERE employee_status != 'Archived'"; 

// If a specific status is selected (including 'Archived'), modify the query
if (!empty($status)) {
    if ($status === 'Archived') {
        $sql = "SELECT * FROM employees WHERE employee_status = 'Archived'"; // Only include Archived employees
    } else {
        $sql .= " AND employee_status = ?"; // For other statuses
    }
}

// Append additional conditions based on filters
if (!empty($search)) {
    $sql .= " AND (lastname LIKE ? OR firstname LIKE ?)";
}
if (!empty($position)) {
    $sql .= " AND position = ?";
}

// Prepare the statement
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Bind parameters if needed
    $params = [];
    $paramTypes = '';

    if (!empty($status) && $status !== 'Archived') {
        $paramTypes .= 's'; // For one string parameter (status)
        $params[] = $status;
    }

    if (!empty($search)) {
        $paramTypes .= 'ss'; // For two string parameters (search)
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }
    
    if (!empty($position)) {
        $paramTypes .= 's'; // For one string parameter (position)
        $params[] = $position;
    }

    // Bind the parameters to the statement
    if ($paramTypes) {
        mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    }

    // Execute and fetch results
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Display the results
        while ($row = mysqli_fetch_assoc($result)) {
            $counter = isset($counter) ? $counter + 1 : 1;
            $employee_id = htmlspecialchars($row['employee_id']);
            $employee_number = htmlspecialchars($row['employee_number']);
            $lastname = htmlspecialchars($row['lastname']);
            $firstname = htmlspecialchars($row['firstname']);
            $employee_status = htmlspecialchars($row['employee_status']);
            $position = htmlspecialchars($row['position']);
            $hire_date = htmlspecialchars($row['hire_date']);

            echo "<tr>
                <th scope='row'>{$counter}</th>
                <td>{$firstname} {$lastname}</td>
                <td>{$employee_status}</td>
                <td>{$position}</td> 
                <td>{$hire_date}</td>
                <td>
                    <button type='button' class='btn btn-danger mb-2 delete-btn' data-id='{$employee_number}'>Archive</button>
                    <button type='button' class='btn btn-primary mb-2 edit-employee-btn' data-employee-id='{$employee_id}' 
                    data-employee-number='{$employee_number}' 
                    data-lastname='{$lastname}' 
                    data-firstname='{$firstname}' 
                    data-employee-status='{$employee_status}' 
                    data-position='{$position}' 
                    data-hire-date='{$hire_date}'>Edit</button>
                    <button type='button' class='btn btn-secondary mb-2 leaves-btn' data-id='{$employee_number}'>Leaves</button>
                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No results found</td></tr>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
