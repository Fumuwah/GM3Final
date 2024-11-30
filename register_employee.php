<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "database.php";

    // Existing variables and logic
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
    $position_name = $_POST['position_name'];
    $hire_date = $_POST['hire_date'];
    $employee_status = $_POST['employee_status']; // Used for the new condition
    $sss_no = $_POST['sss_no'];
    $philhealth_no = $_POST['philhealth_no'];
    $hdmf_no = $_POST['hdmf_no'];
    $tin_no = $_POST['tin_no'];
    $sss_con = $_POST['sss_con'];
    $philhealth_con = $_POST['philhealth_con'];
    $pag_ibig_con = $_POST['pag_ibig_con'];
    $withhold_tax = $_POST['withhold_tax'];
    $email = $_POST['email'];
    $password_raw = "gm3builders" . $birthdate;
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $emergency_contactno = $_POST['emergency_contactno'];
    $role_id = (int)$_POST['role_id'];
    $project_name = $_POST['project_name'];

    // Check if a new project is being added
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

    // Get position_id from the positions table
    $sql_position = "SELECT position_id FROM positions WHERE position_name = ?";
    $stmt_position = mysqli_prepare($conn, $sql_position);
    mysqli_stmt_bind_param($stmt_position, "s", $position_name);
    mysqli_stmt_execute($stmt_position);
    mysqli_stmt_bind_result($stmt_position, $position_id);
    mysqli_stmt_fetch($stmt_position);
    mysqli_stmt_close($stmt_position);

    // Insert employee into the employees table
    $sql_employee = "INSERT INTO employees (
        employee_number, lastname, firstname, middlename, contactno, address, birthdate, 
        civil_status, daily_salary, basic_salary, hire_date, employee_status, 
        sss_no, philhealth_no, hdmf_no, tin_no, sss_con, philhealth_con, pag_ibig_con, withhold_tax,
        email, password, emergency_contactno, 
        position_id, role_id, project_name
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_employee = mysqli_prepare($conn, $sql_employee);
    mysqli_stmt_bind_param($stmt_employee, "sssssssssssssssssssssssiis", 
        $employee_number, $lastname, $firstname, $middlename, $contactno, $address, 
        $birthdate, $civil_status, $daily_salary, $basic_salary, $hire_date, $employee_status, 
        $sss_no, $philhealth_no, $hdmf_no, $tin_no, $sss_con, $philhealth_con,
        $pag_ibig_con, $withhold_tax, $email, $password, 
        $emergency_contactno, $position_id, $role_id, $project_name
    );

    if (mysqli_stmt_execute($stmt_employee)) {
        $employee_id = mysqli_insert_id($conn);  // Get the new employee ID

        // ** New Clause: Skip leave creation for "Contractual" employees **
        if (strtolower($employee_status) !== "contractual") {
            // Determine initial leave balances based on role_id
            $sick_leave = ($role_id <= 2) ? 7 : 6;  // 7 for Admins/Super Admins, 6 for Employees
            $vacation_leave = $sick_leave;

            // Insert leave record for the new employee
            $sql_leave = "INSERT INTO leaves (employee_id, sick_leave, vacation_leave, role_id) 
                          VALUES (?, ?, ?, ?)";
            $stmt_leave = mysqli_prepare($conn, $sql_leave);
            mysqli_stmt_bind_param($stmt_leave, "iiii", $employee_id, $sick_leave, $vacation_leave, $role_id);

            if (mysqli_stmt_execute($stmt_leave)) {
                echo "<div class='alert alert-success'>Employee and leave record registered successfully!</div>";
                header('Location: employees.php');
            } else {
                echo "<div class='alert alert-danger'>Error inserting leave record: " . mysqli_error($conn) . "</div>";
                header('Location: employees.php');
            }
            mysqli_stmt_close($stmt_leave);
        } else {
            echo "<div class='alert alert-success'>Contractual employee registered successfully without leave records.</div>";
            header('Location: employees.php');
        }
    } else {
        echo "<div class='alert alert-danger'>Error inserting employee: " . mysqli_error($conn) . "</div>";
    }

    mysqli_stmt_close($stmt_employee);
    mysqli_close($conn);
}
?>
