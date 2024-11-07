<?php
session_start();  // Start the session at the top

if (!isset($_SESSION['role_name'])) {
    echo "<div class='alert alert-danger'>You must be logged in to update employee data.</div>";
    exit();  // Prevent further execution if the role_id is not set
}
require_once "database.php";
$current_user_role = $_SESSION['role_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect employee_id from POST
    $employee_id = $_POST['employee_id'];
    
    // Fetch the existing employee data, including the password
    $sql_fetch = "SELECT password FROM employees WHERE employee_id = ?";
    $stmt_fetch = mysqli_prepare($conn, $sql_fetch);
    mysqli_stmt_bind_param($stmt_fetch, "i", $employee_id);
    mysqli_stmt_execute($stmt_fetch);
    mysqli_stmt_bind_result($stmt_fetch, $current_password);
    mysqli_stmt_fetch($stmt_fetch);
    mysqli_stmt_close($stmt_fetch);

    // Collect employee data from the POST request
    $employee_id = $_POST['employee_id'];
    $employee_number = $_POST['employee_number'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $contactno = $_POST['contactno'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $civil_status = $_POST['civil_status'];
    $religion = $_POST['religion'];
    $basic_salary = $_POST['basic_salary'];
    $position_name = $_POST['position'];
    $hire_date = $_POST['hire_date'];
    $employee_status = $_POST['employee_status'];
    $sss_no = $_POST['sss_no'];
    $philhealth_no = $_POST['philhealth_no'];
    $hdmf_no = $_POST['hdmf_no'];
    $tin_no = $_POST['tin_no'];
    $email = $_POST['email'];
    $emergency_contactno = $_POST['emergency_contactno'];
    $role_name = $_POST['role_name'];  // Use role_name now
    $project_name = $_POST['project_name'];
    
    // Check if a new password is provided or retain the existing one
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $current_password;

    // Verify if the selected project exists
    $sql_check_project = "SELECT project_name FROM projects WHERE project_name = ?";
    $stmt_check_project = mysqli_prepare($conn, $sql_check_project);
    mysqli_stmt_bind_param($stmt_check_project, "s", $project_name);
    mysqli_stmt_execute($stmt_check_project);
    mysqli_stmt_store_result($stmt_check_project);

    if (mysqli_stmt_num_rows($stmt_check_project) == 0) {
        echo "<div class='alert alert-danger'>The selected project does not exist. Please add it first.</div>";
        exit();
    }
    mysqli_stmt_close($stmt_check_project);

    // Check if a new project is being added
    if ($project_name === "add_new") {
        $new_project_name = $_POST['new_project_name'];

        // Insert the new project into the projects table
        $sql_insert_project = "INSERT INTO projects (project_name) VALUES (?)";
        $stmt_insert_project = mysqli_prepare($conn, $sql_insert_project);
        mysqli_stmt_bind_param($stmt_insert_project, "s", $new_project_name);

        if (mysqli_stmt_execute($stmt_insert_project)) {
            $project_name = $new_project_name;  // Set the project_name to the new one added
        } else {
            echo "<div class='alert alert-danger'>Error inserting project: " . mysqli_error($conn) . "</div>";
            exit;
        }

        mysqli_stmt_close($stmt_insert_project);
    }

    // Get the position_id from the positions table based on the selected position_name
    $sql_position = "SELECT position_id FROM positions WHERE position_name = ?";
    $stmt_position = mysqli_prepare($conn, $sql_position);
    mysqli_stmt_bind_param($stmt_position, "s", $position_name);
    mysqli_stmt_execute($stmt_position);
    mysqli_stmt_bind_result($stmt_position, $position_id);
    mysqli_stmt_fetch($stmt_position);
    mysqli_stmt_close($stmt_position);

    // Get the role_id from the roles table based on the selected role_name
    $sql_role = "SELECT role_id FROM roles WHERE role_name = ?";
    $stmt_role = mysqli_prepare($conn, $sql_role);
    mysqli_stmt_bind_param($stmt_role, "s", $role_name);
    mysqli_stmt_execute($stmt_role);
    mysqli_stmt_bind_result($stmt_role, $role_id);
    mysqli_stmt_fetch($stmt_role);
    mysqli_stmt_close($stmt_role);

    // SQL to update the employee data
    $sql_employee = "UPDATE employees 
                     SET employee_number = ?, lastname = ?, firstname = ?, middlename = ?, contactno = ?, 
                         address = ?, birthdate = ?, civil_status = ?, religion = ?, basic_salary = ?, 
                         position_id = ?, hire_date = ?, employee_status = ?, sss_no = ?, philhealth_no = ?, 
                         hdmf_no = ?, tin_no = ?, email = ?, emergency_contactno = ?, role_id = ?, project_name = ?, password = ? 
                     WHERE employee_id = ?";

    // Prepare and bind parameters to the SQL query
    $stmt_employee = mysqli_prepare($conn, $sql_employee);
    mysqli_stmt_bind_param($stmt_employee, "ssssssssssissssssssissi", 
        $employee_number, $lastname, $firstname, $middlename, $contactno, $address, $birthdate, 
        $civil_status, $religion, $basic_salary, $position_id, $hire_date, $employee_status, 
        $sss_no, $philhealth_no, $hdmf_no, $tin_no, $email, $emergency_contactno, $role_id, 
        $project_name, $password, $employee_id);

    // Execute the statement and check for success
    if (mysqli_stmt_execute($stmt_employee)) {
        echo "<div class='alert alert-success'>Employee updated successfully!</div>";
        header('Location: employees.php');
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating employee: " . mysqli_error($conn) . "</div>";
    }

    // Close the statement and connection
    mysqli_stmt_close($stmt_employee);
    mysqli_close($conn);
}
?>
