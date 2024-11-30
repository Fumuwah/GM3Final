<?php
session_start();

if (!isset($_SESSION['role_name'])) {
    echo "<div class='alert alert-danger'>You must be logged in to update employee data.</div>";
    exit(); 
}
require_once "database.php";
$current_user_role = $_SESSION['role_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $employee_id = $_POST['employee_id'];

    $sql_fetch = "SELECT password FROM employees WHERE employee_id = ?";
    $stmt_fetch = mysqli_prepare($conn, $sql_fetch);
    mysqli_stmt_bind_param($stmt_fetch, "i", $employee_id);
    mysqli_stmt_execute($stmt_fetch);
    mysqli_stmt_bind_result($stmt_fetch, $current_password);
    mysqli_stmt_fetch($stmt_fetch);
    mysqli_stmt_close($stmt_fetch);

    $employee_id = $_POST['employee_id'];
    $employee_number = $_POST['employee_number'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $contactno = $_POST['contactno'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $civil_status = $_POST['civil_status'];
    $daily_salary = $_POST['daily_salary'];
    $basic_salary = $_POST['basic_salary'];
    $position_name = $_POST['position'];
    $hire_date = $_POST['hire_date'];
    $employee_status = $_POST['employee_status'];
    $sss_no = $_POST['sss_no'];
    $philhealth_no = $_POST['philhealth_no'];
    $hdmf_no = $_POST['hdmf_no'];
    $tin_no = $_POST['tin_no'];
    $sss_con = $_POST['sss_con'];
    $philhealth_con = $_POST['philhealth_con'];
    $pag_ibig_con = $_POST['pag_ibig_con'];
    $withhold_tax = $_POST['withhold_tax'];
    $email = $_POST['email'];
    $emergency_contactno = $_POST['emergency_contactno'];
    $role_id = $_POST['role_id'];
    $project_name = $_POST['project_name'];

    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $current_password;

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

    if ($project_name === "add_new") {
        $new_project_name = $_POST['new_project_name'];

        $sql_insert_project = "INSERT INTO projects (project_name) VALUES (?)";
        $stmt_insert_project = mysqli_prepare($conn, $sql_insert_project);
        mysqli_stmt_bind_param($stmt_insert_project, "s", $new_project_name);

        if (mysqli_stmt_execute($stmt_insert_project)) {
            $project_name = $new_project_name;
        } else {
            echo "<div class='alert alert-danger'>Error inserting project: " . mysqli_error($conn) . "</div>";
            exit;
        }

        mysqli_stmt_close($stmt_insert_project);
    }

    $sql_position = "SELECT position_id FROM positions WHERE position_name = ?";
    $stmt_position = mysqli_prepare($conn, $sql_position);
    mysqli_stmt_bind_param($stmt_position, "s", $position_name);
    mysqli_stmt_execute($stmt_position);
    mysqli_stmt_bind_result($stmt_position, $position_id);
    mysqli_stmt_fetch($stmt_position);
    mysqli_stmt_close($stmt_position);

    $sql_role = "SELECT role_id FROM roles WHERE role_name = ?";
    $stmt_role = mysqli_prepare($conn, $sql_role);
    mysqli_stmt_bind_param($stmt_role, "s", $role_name);
    mysqli_stmt_execute($stmt_role);
    mysqli_stmt_bind_result($stmt_role, $role_id);
    mysqli_stmt_fetch($stmt_role);
    mysqli_stmt_close($stmt_role);

    $sql_employee = "UPDATE employees 
                     SET employee_number = ?, lastname = ?, firstname = ?, middlename = ?, contactno = ?, 
                         address = ?, birthdate = ?, civil_status = ?, daily_salary = ?, basic_salary = ?, 
                         position_id = ?, hire_date = ?, employee_status = ?, sss_no = ?, philhealth_no = ?, 
                         hdmf_no = ?, tin_no = ?, sss_con = ?, philhealth_con = ?, pag_ibig_con = ?, withhold_tax = ?,
                         email = ?, emergency_contactno = ?, role_id = ?, project_name = ?
                     WHERE employee_id = ?";

    $stmt_employee = mysqli_prepare($conn, $sql_employee);
    mysqli_stmt_bind_param($stmt_employee, "ssssssssssissssssssssssisi", 
        $employee_number, $lastname, $firstname, $middlename, $contactno, $address, $birthdate, 
        $civil_status, $daily_salary, $basic_salary, $position_id, $hire_date, $employee_status, 
        $sss_no, $philhealth_no, $hdmf_no, $tin_no, $sss_con, $philhealth_con, $pag_ibig_con, $withhold_tax,
        $email, $emergency_contactno, $role_id, 
        $project_name, $employee_id);

    if (mysqli_stmt_execute($stmt_employee)) {
        echo "<div class='alert alert-success'>Employee updated successfully!</div>";
        header('Location: employees.php');
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating employee: " . mysqli_error($conn) . "</div>";
    }

    mysqli_stmt_close($stmt_employee);
    mysqli_close($conn);
}
?>
