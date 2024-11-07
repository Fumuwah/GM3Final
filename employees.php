<?php
session_start();

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}
require_once "database.php";

$results_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$position_name = isset($_GET['position_name']) ? mysqli_real_escape_string($conn, $_GET['position_name']) : '';
$role_name = strtolower($user['role_name'] ?? '');

$sql = "SELECT e.employee_id, e.firstname, e.middlename, e.lastname, e.employee_number, 
               e.contactno, e.address, e.birthdate, e.civil_status, e.religion, 
               e.basic_salary, e.hire_date, e.employee_status, e.sss_no, e.philhealth_no, 
               e.hdmf_no, e.tin_no, e.email, e.password, e.emergency_contactno, 
               p.position_name, pr.project_name, r.role_name 
        FROM employees e
        JOIN positions p ON e.position_id = p.position_id
        LEFT JOIN projects pr ON e.project_name = pr.project_name
        LEFT JOIN roles r ON e.role_id = r.role_id
        WHERE e.employee_status != 'Archived'"; 

        if (isset($_GET['employee_number'])) {
            $employee_number = $_GET['employee_number'];
        
            $query = "SELECT e.*, r.role_name FROM employees e
                      LEFT JOIN roles r ON e.role_id = r.role_id
                      WHERE e.employee_number = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $employee_number);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $employeeData = $result->fetch_assoc();
            }
        }

if (!empty($search)) {
    $sql .= " AND (e.firstname LIKE '%$search%' 
              OR e.lastname LIKE '%$search%' 
              OR e.employee_number LIKE '%$search%')";
}

$total_sql = "SELECT COUNT(*) as total FROM employees e 
              JOIN positions p ON e.position_id = p.position_id 
              LEFT JOIN projects pr ON e.project_name = pr.project_name 
              LEFT JOIN roles r ON e.role_id = r.role_id 
              WHERE e.employee_status != 'Archived'";

if ($status === 'Archived') {
    $total_sql = str_replace("WHERE e.employee_status != 'Archived'", "WHERE e.employee_status = 'Archived'", $total_sql);
}

if (!empty($search)) {
    $total_sql .= " AND (e.firstname LIKE '%$search%' OR e.lastname LIKE '%$search%' OR e.employee_number LIKE '%$search%')";
}

if (!empty($position_name)) {
    $total_sql .= " AND p.position_name = '$position_name'";
}

$total_result = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $results_per_page);

$sql .= " LIMIT $results_per_page OFFSET $offset";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}
$can_manage_roles = $user['can_manage_roles'] ?? false;

$activePage = 'employees';

var_dump($_GET['position_name'], $_GET['employee_status']);


include './layout/header.php';
?>

      <div class="d-flex">
        <?php include './layout/sidebar.php'; ?>
          <div class="main-content">
            <div class="container-fluid">
                <h2>Pay slip</h2>
                <div class="d-flex justify-content-between align-items-center">
                <form class="form-inline my-3 col-10 pl-0" method="GET" action="">
                        <div class="form-group col-8 col-lg-3">
                            <label for="search-user" class="sr-only">Search User</label>
                            <input type="text" class="form-control w-100" id="search-user" name="search" placeholder="Search User" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        </div>

                        <div class="form-group">
                        <select name="status" id="status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="Regular" <?php echo isset($_GET['status']) && $_GET['status'] == 'Regular' ? 'selected' : ''; ?>>Regular</option>
                            <option value="Probationary" <?php echo isset($_GET['status']) && $_GET['status'] == 'Probationary' ? 'selected' : ''; ?>>Probationary</option>
                            <option value="Archived" <?php echo isset($_GET['status']) && $_GET['status'] == 'Archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                      <div class="form-group mx-2">
                          <select name="position_name" id="position_name" class="form-control">
                            <option value="">Select Position</option>
                            <option value="Owner" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Owner' ? 'selected' : ''; ?>>Owner</option>
                            <option value="HR/Admin Manager" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'HR/Admin Manager' ? 'selected' : ''; ?>>HR/Admin Manager</option>
                            <option value="Foreman" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Foreman' ? 'selected' : ''; ?>>Foreman</option>
                            <option value="Leadman" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Leadman' ? 'selected' : ''; ?>>Leadman</option>
                            <option value="Civil Engineer" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Civil Engineer' ? 'selected' : ''; ?>>Civil Engineer</option>
                            <option value="Mechanical Engineer" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Mechanical Engineer' ? 'selected' : ''; ?>>Mechanical Engineer</option>
                            <option value="Laborer" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Laborer' ? 'selected' : ''; ?>>Laborer</option>
                            <option value="HR Officer" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'HR Officer' ? 'selected' : ''; ?>>HR Officer</option>
                            <option value="Field Coordinator" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Field Coordinator' ? 'selected' : ''; ?>>Field Coordinator</option>
                            <option value="Warehouse Man" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Warehouse Man' ? 'selected' : ''; ?>>Warehouse Man</option>
                            <option value="Electrician" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Electrician' ? 'selected' : ''; ?>>Electrician</option>
                            <option value="Mason" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Mason' ? 'selected' : ''; ?>>Mason</option>
                            <option value="Surveyor" <?php echo isset($_GET['posiposition_nametion']) && $_GET['position_name'] == 'Surveyor' ? 'selected' : ''; ?>>Surveyor</option>
                            <option value="Driver" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Driver' ? 'selected' : ''; ?>>Driver</option>
                            <option value="Project Engineer" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Project Engineer' ? 'selected' : ''; ?>>Project Engineer</option>
                            <option value="Safety Officer" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Safety Officer' ? 'selected' : ''; ?>>Safety Officer</option>
                            <option value="Helper" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Helper' ? 'selected' : ''; ?>>Helper</option>
                            <option value="Architectural Designer" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Architectural Designer' ? 'selected' : ''; ?>>Architectural Designer</option>
                            <option value="Admin Specialist" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'Admin Specialist' ? 'selected' : ''; ?>>Admin Specialist</option>
                            <option value="HR Specialist" <?php echo isset($_GET['position_name']) && $_GET['position_name'] == 'HR Specialist' ? 'selected' : ''; ?>>HR Specialist</option>
                          </select>
                      </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                <div class="d-flex">
                    <form action="profile-change-requests.php">
                        <button type="submit" class="btn btn-success mb-2 mr-2" id="approvals">Approvals</button>
                      </form>

                      <form action="">
                        <button type="submit" class="btn btn-success mb-2" id="add-employee-btn">Add Employee</button>
                      </form>
                </div>
                </div>
                
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Status</th>
                                <th scope="col">Position</th>
                                <th scope="col">Hire Date</th>
                                <th scope="col">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                                $counter = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $employee_id = htmlspecialchars($row['employee_id']);
                                    $employee_number = htmlspecialchars($row['employee_number']);
                                    $lastname = htmlspecialchars($row['lastname']);
                                    $firstname = htmlspecialchars($row['firstname']);
                                    $middlename = htmlspecialchars($row['middlename']);
                                    $contactno = htmlspecialchars($row['contactno']);
                                    $address = htmlspecialchars($row['address'] ?? '');
                                    $birthdate = htmlspecialchars($row['birthdate'] ?? '');
                                    $civil_status = htmlspecialchars($row['civil_status'] ?? '');
                                    $religion = htmlspecialchars($row['religion'] ?? '');
                                    $basic_salary = htmlspecialchars($row['basic_salary'] ?? '');
                                    $project_name = htmlspecialchars($row['project_name'] ?? '');
                                    $position_name = htmlspecialchars($row['position_name'] ?? '');
                                    $hire_date = htmlspecialchars($row['hire_date'] ?? '');
                                    $employee_status = htmlspecialchars($row['employee_status'] ?? '');
                                    $sss_no = htmlspecialchars($row['sss_no'] ?? '');
                                    $philhealth_no = htmlspecialchars($row['philhealth_no'] ?? '');
                                    $hdmf_no = htmlspecialchars($row['hdmf_no'] ?? '');
                                    $tin_no = htmlspecialchars($row['tin_no'] ?? '');
                                    $email = htmlspecialchars($row['email'] ?? '');
                                    $password = htmlspecialchars($row['password'] ?? '');
                                    $emergency_contactno = htmlspecialchars($row['emergency_contactno'] ?? '');
                                    $role_name = strtolower($row['role_name'] ?? '');
                                    
                                    $archiveButtonText = ($employee_status === 'Archived') ? 'Unarchive' : 'Archive';

                                    echo "<tr>
                                        <th scope='row'>{$counter}</th>
                                        <td>{$firstname} {$lastname}</td>
                                        <td>{$employee_status}</td>
                                        <td>{$position_name}</td> 
                                        <td>{$hire_date}</td>
                                        <td>
                                            <button type='button' class='btn btn-danger mb-2 delete-btn' data-id='{$employee_number}'>{$archiveButtonText}</button>
                                            <button type='button' class='btn btn-primary mb-2 edit-employee-btn' data-employee-id='{$employee_id}' 
                                            data-employee-number='{$employee_number}' 
                                            data-lastname='{$lastname}' 
                                            data-firstname='{$firstname}' 
                                            data-middlename='" . htmlspecialchars($row['middlename']) . "' 
                                            data-contactno='{$contactno}' 
                                            data-address='{$address}' 
                                            data-birthdate='{$birthdate}' 
                                            data-civil-status='{$civil_status}' 
                                            data-religion='{$religion}'
                                            data-basic-salary='{$basic_salary}' 
                                            data-project-name='{$project_name}'
                                            data-position-name='{$position_name}' 
                                            data-hire-date='{$hire_date}' 
                                            data-employee-status='{$employee_status}' 
                                            data-sss-no='{$sss_no}' 
                                            data-philhealth-no='{$philhealth_no}' 
                                            data-hdmf-no='{$hdmf_no}' 
                                            data-tin-no='{$tin_no}' 
                                            data-email='{$email}' 
                                            data-password='{$password}' 
                                            data-emergency-contactno='{$emergency_contactno}' 
                                            data-role-name='{$role_name}'>Edit</button>
                                            <button type='button' class='btn btn-secondary mb-2 leaves-btn' data-id='{$employee_number}'>Leaves</button>
                                        </td>
                                    </tr>";

                                    $counter++;
                                }
                            ?>

                            </tbody>
                          </table>
                        <div class="d-flex justify-content-end">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&position_name=<?php echo urlencode($position_name); ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo urlencode($status); ?>&position_name=<?php echo $position_name; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                        
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&position_name=<?php echo urlencode($position_name); ?>">Previous</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
          </div>
      </div>
   
        <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Archive Employee</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to archive this employee?
                    <input type="hidden" id="employee-id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success close-modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirm-archive">Archive Employee</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="update_employee.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Employee</h5>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="employee_id" id="edit_employee_id">

                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="employee_number">Employee Number</label>
                    <input type="text" class="form-control" name="employee_number" id="edit_employee_number" required pattern="\d+" title="Please enter a valid integer.">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $employee_number = $_POST['employee_number'];
                        if (!filter_var($employee_number, FILTER_VALIDATE_INT)) {
                            echo "Employee Number must be an integer.";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                           <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" name="lastname" id="edit_lastname" required pattern="^[A-Za-zÀ-ÖØ-ÿ]+$" title="Please enter a valid last name (only letters are allowed).">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $lastname = $_POST['lastname'];
                        if (!preg_match("/^[A-Za-zÀ-ÖØ-ÿ]+$/", $lastname)) {
                            echo "<p style='color:red;'>Last Name must contain only letters and cannot include numbers or special characters.</p>";
                        } else {
                            echo "<p style='color:green;'>Last Name is valid.</p>";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                          <label for="firstname">First Name</label>
                    <input type="text" class="form-control" name="firstname" id="edit_firstname" required pattern="^[A-Za-zÀ-ÖØ-ÿ\s]+$" title="Please enter a valid first name (only letters and spaces are allowed).">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $firstname = $_POST['firstname'];
                        if (!preg_match("/^[A-Za-zÀ-ÖØ-ÿ\s]+$/", $firstname)) {
                            echo "<p style='color:red;'>First Name must contain only letters and spaces, and cannot include numbers or special characters.</p>";
                        } else {
                            echo "<p style='color:green;'>First Name is valid.</p>";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                            <label for="middlename">Middle Name</label>
                <input type="text" class="form-control" name="middlename" id="edit_middlename" required pattern="^[A-Za-zÀ-ÖØ-ÿ\s]+$" title="Please enter a valid middle name (only letters and spaces are allowed).">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $middlename = $_POST['middlename'];
                    if (!preg_match("/^[A-Za-zÀ-ÖØ-ÿ\s]+$/", $middlename)) {
                        echo "<p style='color:red;'>Middle Name must contain only letters and spaces, and cannot include numbers or special characters.</p>";
                    } else {
                        echo "<p style='color:green;'>Middle Name is valid.</p>";
                    }
                }
                ?>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                          <label for="contactno">Contact No.</label>
                        <input type="text" class="form-control" name="contactno" id="edit_contactno" required pattern="\d{11}" title="Please enter a valid contact number (11 digits are required)." maxlength="11">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $contactno = $_POST['contactno'];
                            if (!preg_match('/^\d{11}$/', $contactno)) {
                                echo "<p style='color:red;'>Contact No. must be exactly 11 digits.</p>";
                            } else {
                                echo "<p style='color:green;'>Contact No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                        <div class="col-9">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" name="address" id="edit_address" required>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" class="form-control" name="birthdate" id="edit_birthdate" required>
                        </div>
                        <div class="col-3">
                            <label for="civil_status">Civil Status</label>
                            <select name="civil_status" id="edit_civilstatus_select" class="form-control" required>
                                <option value="">Select Civil Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="religion">Religion</label>
                            <input type="text" class="form-control" name="religion" id="edit_religion">
                        </div>
                        <div class="col-3">
                            <label for="basic_salary">Basic Salary</label>
                           <input type="text" class="form-control" name="basic_salary" id="edit_basic_salary" required pattern="\d+" title="Please enter a valid integer.">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $basic_salary = $_POST['basic_salary'];
                        if (!filter_var($basic_salary, FILTER_VALIDATE_INT)) {
                            echo "Basic Salary must be an integer.";
                        }
                    }
                    ?>
                        </div>
                    </div>
                    <div class="form-row form-group">
                    <div class="col-3">
                    <label for="project_name">Project</label>
                        <select name="project_name" id="edit_project_name" class="form-control" onchange="toggleProjectField()" required>
                            <option value="">Select Project</option>
                            <?php
                            require_once "database.php";
                            $projectQuery = "SELECT project_name FROM projects";
                            $projectResult = $conn->query($projectQuery);
                            while ($projectRow = $projectResult->fetch_assoc()) {
                                echo "<option value='"  . htmlspecialchars($projectRow['project_name'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($projectRow['project_name'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                        </div>
                        <div class="col-3">
                            <label for="position">Position</label>
                            <select name="position" id="edit_position_select" class="form-control" required>
                              <option value="">Select Position</option>
                              <option value="Owner">Owner</option>
                              <option value="HR/Admin Manager">HR/Admin Manager</option>
                              <option value="Foreman">Foreman</option>
                              <option value="Leadman">Leadman</option>
                              <option value="Civil Engineer">Civil Engineer</option>
                              <option value="Mechanical Engineer">Mechanical Engineer</option>
                              <option value="Laborer">Laborer</option>
                              <option value="HR Officer">HR Officer</option>
                              <option value="Field Coordinator">Field Coordinator</option>
                              <option value="Warehouse Man">Warehouse Man</option>
                              <option value="Electrician">Electrician</option>
                              <option value="Mason">Mason</option>
                              <option value="Surveyor">Surveyor</option>
                              <option value="Driver">Driver</option>
                              <option value="Project Engineer">Project Engineer</option>
                              <option value="Safety Officer">Safety Officer</option>
                              <option value="Helper">Helper</option>
                              <option value="Architectural Designer">Architectural Designer</option>
                              <option value="Admin Specialist">Admin Specialist</option>
                              <option value="HR Specialist">HR Specialist</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="hire_date">Hire Date</label>
                            <input type="date" class="form-control" name="hire_date" id="edit_hire_date" required>
                        </div>
                        <div class="col-3">
                            <label for="employee_status">Employee Status</label>
                            <select name="employee_status" id="edit_employee_status_select" class="form-control" required>
                              <option value="">Select Status</option>
                              <option value="Regular">Regular</option>
                              <option value="Probationary">Probationary</option>
                              <option value="Archived">Archived</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="sss_no">SSS No.</label>
                    <input type="text" class="form-control" name="sss_no" id="edit_sss_no" required pattern="\d{13}" title="Please enter a valid SSS Number (13 digits are required)." maxlength="13">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $sss_no = $_POST['sss_no'];
                        if (!preg_match('/^\d{13}$/', $sss_no)) {
                            echo "<p style='color:red;'>SSS No. must be exactly 13 digits.</p>";
                        } else {
                            echo "<p style='color:green;'>SSS No. is valid.</p>";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                            <label for="Philhealth_no">Philhealth No.</label>
                        <input type="text" class="form-control" name="philhealth_no" id="edit_philhealth_no" required pattern="\d{13}" title="Please enter a valid Philhealth Number (13 digits are required)." maxlength="13">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $philhealth_no = $_POST['philhealth_no'];
                            if (!preg_match('/^\d{13}$/', $philhealth_no)) {
                                echo "<p style='color:red;'>Philhealth No. must be exactly 13 digits.</p>";
                            } else {
                                echo "<p style='color:green;'>Philhealth No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                        <div class="col-3">
                            <label for="hdmf_no">HDMF No.</label>
                        <input type="text" class="form-control" name="hdmf_no" id="edit_hdmf_no" required pattern="\d{12}" title="Please enter a valid HDMF Number (12 digits are required)." maxlength="12">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $hdmf_no = $_POST['hdmf_no'];
                            if (!preg_match('/^\d{12}$/', $tin_no)) {
                                echo "<p style='color:red;'>hdmf No. must be exactly 12 digits.</p>";
                            } else {
                                echo "<p style='color:green;'hdmf No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                        <div class="col-3">
                           <label for="Tin_no">TIN No.</label>
                        <input type="text" class="form-control" name="tin_no" id="edit_tin_no" required pattern="\d{12}" title="Please enter a valid TIN Number (12 digits are required)." maxlength="12">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $tin_no = $_POST['tin_no'];
                            if (!preg_match('/^\d{12}$/', $tin_no)) {
                                echo "<p style='color:red;'>TIN No. must be exactly 12 digits.</p>";
                            } else {
                                echo "<p style='color:green;'>TIN No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="col-3">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" name="password" id="edit_password" required>
                        </div>
                        <div class="col-3">
                            <label for="emergency_contactno">In case of Emergency</label>
                            <input type="text" class="form-control" name="emergency_contactno" id="edit_emergency_contactno">
                        </div>
                    <?php if ($can_manage_roles): ?>
                        <div class="col-3">
                            <label for="role_name">Role</label>
                            <select name="edit_role_name_select" id="edit_role_name_select" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="1" 
                                    <?php if (isset($employeeData) && $employeeData['role_name'] == 'super admin') echo 'selected'; ?>>
                                    Super Admin
                                </option>
                                <option value="2" 
                                    <?php if (isset($employeeData) && $employeeData['role_name'] == 'admin') echo 'selected'; ?>>
                                    Admin
                                </option>
                                <option value="3" 
                                    <?php if (isset($employeeData) && $employeeData['role_name'] == 'employee') echo 'selected'; ?>>
                                    Employee
                                </option>
                            </select>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" type="button" class="btn btn-danger close-modalwForm">Close</a>
                    <button type="submit" class="btn btn-success">Update Employee</button>
                </div>
            </div>
        </form>
    </div>
</div>


      <div class="modal fade" id="add-employee-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="employee-form" action="register_employee.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Employee</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="employee_number">Employee Number</label>
                    <input type="text" class="form-control" name="employee_number" id="employee_number" required pattern="\d+" title="Please enter a valid integer.">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $employee_number = $_POST['employee_number'];
                        if (!filter_var($employee_number, FILTER_VALIDATE_INT)) {
                            echo "Employee Number must be an integer.";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                            <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" name="lastname" id="lastname" required pattern="^[A-Za-zÀ-ÖØ-ÿ]+$" title="Please enter a valid last name (only letters are allowed).">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $lastname = $_POST['lastname'];
                        if (!preg_match("/^[A-Za-zÀ-ÖØ-ÿ]+$/", $lastname)) {
                            echo "<p style='color:red;'>Last Name must contain only letters and cannot include numbers or special characters.</p>";
                        } else {
                            echo "<p style='color:green;'>Last Name is valid.</p>";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                            <label for="firstname">First Name</label>
                    <input type="text" class="form-control" name="firstname" id="firstname" required pattern="^[A-Za-zÀ-ÖØ-ÿ\s]+$" title="Please enter a valid first name (only letters and spaces are allowed).">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $firstname = $_POST['firstname'];
                        if (!preg_match("/^[A-Za-zÀ-ÖØ-ÿ\s]+$/", $firstname)) {
                            echo "<p style='color:red;'>First Name must contain only letters and spaces, and cannot include numbers or special characters.</p>";
                        } else {
                            echo "<p style='color:green;'>First Name is valid.</p>";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                            <label for="middlename">Middle Name</label>
                <input type="text" class="form-control" name="middlename" id="middlename" required pattern="^[A-Za-zÀ-ÖØ-ÿ\s]+$" title="Please enter a valid middle name (only letters and spaces are allowed).">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $middlename = $_POST['middlename'];
                    if (!preg_match("/^[A-Za-zÀ-ÖØ-ÿ\s]+$/", $middlename)) {
                        echo "<p style='color:red;'>Middle Name must contain only letters and spaces, and cannot include numbers or special characters.</p>";
                    } else {
                        echo "<p style='color:green;'>Middle Name is valid.</p>";
                    }
                }
                ?>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="contactno">Contact No.</label>
                        <input type="text" class="form-control" name="contactno" id="contactno" required pattern="\d{11}" title="Please enter a valid contact number (11 digits are required)." maxlength="11">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $contactno = $_POST['contactno'];
                            if (!preg_match('/^\d{11}$/', $contactno)) {
                                echo "<p style='color:red;'>Contact No. must be exactly 11 digits.</p>";
                            } else {
                                echo "<p style='color:green;'>Contact No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                        <div class="col-9">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" name="address" required>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" class="form-control" name="birthdate" required>
                        </div>
                        <div class="col-3">
                            <label for="civil_status">Civil Status</label>
                            <select name="civil_status" id="civil_status" class="form-control" required>
                                <option value="">Select Civil Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="religion">Religion</label>
                            <input type="text" class="form-control" name="religion">
                        </div>
                        <div class="col-3">
                             <label for="basic_salary">Basic Salary</label>
                           <input type="text" class="form-control" name="basic_salary" id="basic_salary" required pattern="\d+" title="Please enter a valid integer.">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $basic_salary = $_POST['basic_salary'];
                        if (!filter_var($basic_salary, FILTER_VALIDATE_INT)) {
                            echo "Basic Salary must be an integer.";
                        } else {
                        }
                    }
                    ?>
                        </div>
                    </div>
                    <div class="form-row form-group">
                    <div class="col-3">
                    <label for="project_name">Project</label>
                        <select name="project_name" id="project_name" class="form-control" onchange="toggleProjectField()" required>
                            <option value="">Select Project</option>
                            <?php
                            require_once "database.php";
                            $projectQuery = "SELECT project_name FROM projects";
                            $projectResult = $conn->query($projectQuery);

                            while ($projectRow = $projectResult->fetch_assoc()) {
                                echo "<option value='"  . htmlspecialchars($projectRow['project_name'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($projectRow['project_name'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                            <option value="add_new">Add New Project</option>
                        </select>
                        </div>
                        <div class="col-9" id="new_project_field" style="display: none;">
                            <label for="new_project_name">New Project Name</label>
                            <input type="text" class="form-control" name="new_project_name" id="new_project_name">
                        </div>
                        <div class="col-3">
                            <label for="position_name">Position</label>
                            <select name="position_name" id="position_name" class="form-control" required>
                              <option value="">Select Position</option>
                              <option value="Owner">Owner</option>
                              <option value="HR/Admin Manager">HR/Admin Manager</option>
                              <option value="Foreman">Foreman</option>
                              <option value="Leadman">Leadman</option>
                              <option value="Civil Engineer">Civil Engineer</option>
                              <option value="Mechanical Engineer">Mechanical Engineer</option>
                              <option value="Laborer">Laborer</option>
                              <option value="HR Officer">HR Officer</option>
                              <option value="Field Coordinator">Field Coordinator</option>
                              <option value="Warehouse Man">Warehouse Man</option>
                              <option value="Electrician">Electrician</option>
                              <option value="Mason">Mason</option>
                              <option value="Surveyor">Surveyor</option>
                              <option value="Driver">Driver</option>
                              <option value="Project Engineer">Project Engineer</option>
                              <option value="Safety Officer">Safety Officer</option>
                              <option value="Helper">Helper</option>
                              <option value="Architectural Designer">Architectural Designer</option>
                              <option value="Admin Specialist">Admin Specialist</option>
                              <option value="HR Specialist">HR Specialist</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="hire_date">Hire Date</label>
                            <input type="date" class="form-control" name="hire_date" required>
                        </div>
                        <div class="col-3">
                            <label for="employee_status">Employee Status</label>
                            <select name="employee_status" id="employee_status" class="form-control" required>
                              <option value="">Select Status</option>
                              <option value="Regular">Regular</option>
                              <option value="Probationary">Probationary</option>
                              <option value="Resgined">Resigned</option>
                              <option value="Retired">Retired</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                             <label for="sss_no">SSS No.</label>
                    <input type="text" class="form-control" name="sss_no" id="sss_no" required pattern="\d{13}" title="Please enter a valid SSS Number (13 digits are required)." maxlength="13">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $sss_no = $_POST['sss_no'];
                        if (!preg_match('/^\d{13}$/', $sss_no)) {
                            echo "<p style='color:red;'>SSS No. must be exactly 13 digits.</p>";
                        } else {
                            echo "<p style='color:green;'>SSS No. is valid.</p>";
                        }
                    }
                    ?>
                        </div>
                        <div class="col-3">
                            <label for="Philhealth_no">Philhealth No.</label>
                        <input type="text" class="form-control" name="philhealth_no" id="philhealth_no" required pattern="\d{13}" title="Please enter a valid Philhealth Number (13 digits are required)." maxlength="13">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $philhealth_no = $_POST['philhealth_no'];
                            if (!preg_match('/^\d{13}$/', $philhealth_no)) {
                                echo "<p style='color:red;'>Philhealth No. must be exactly 13 digits.</p>";
                            } else {
                                echo "<p style='color:green;'>Philhealth No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                        <div class="col-3">
                            <label for="hdmf_no">HDMF No.</label>
                        <input type="text" class="form-control" name="hdmf_no" id="hdmf_no" required pattern="\d{12}" title="Please enter a valid HDMF Number (12 digits are required)." maxlength="12">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $hdmf_no = $_POST['hdmf_no'];
                            if (!preg_match('/^\d{12}$/', $tin_no)) {
                                echo "<p style='color:red;'>hdmf No. must be exactly 12 digits.</p>";
                            } else {
                                echo "<p style='color:green;'hdmf No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                        <div class="col-3">
                            <label for="Tin_no">TIN No.</label>
                        <input type="text" class="form-control" name="tin_no" id="tin_no" required pattern="\d{12}" title="Please enter a valid TIN Number (12 digits are required)." maxlength="12">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $tin_no = $_POST['tin_no'];
                            if (!preg_match('/^\d{12}$/', $tin_no)) {
                                echo "<p style='color:red;'>TIN No. must be exactly 12 digits.</p>";
                            } else {
                                echo "<p style='color:green;'>TIN No. is valid.</p>";
                            }
                        }
                        ?>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-3">
                            <label for="emergency_contactno">In case of Emergency</label>
                            <input type="text" class="form-control" name="emergency_contactno">
                        </div>
                        <div class="col-3">
                            <label for="role_id">Role</label>
                            <select name="role_id" id="role_id" class="form-control" required>
                                <option value="">Select Role</option>
                                <option value="1"
                                <?php if (isset($employeeData) && $employeeData['role_name'] == 'super admin') echo 'selected'; ?>>
                                    Super Admin
                                </option>
                                <option value="2" 
                                    <?php if (isset($employeeData) && $employeeData['role_name'] == 'admin') echo 'selected'; ?>>
                                    Admin
                                </option>
                                <option value="3" 
                                    <?php if (isset($employeeData) && $employeeData['role_name'] == 'employee') echo 'selected'; ?>>
                                    Employee
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" type="button" class="btn btn-danger close-modalwForm">Close</a>
                    <button type="submit" class="btn btn-success">Register Employee</button>
                </div>
            </div>
        </form>
    </div>
</div>



      <div class="modal fade" id="leave-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Employee Leave Details</h5>
            </div>
            <div class="modal-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Vacation Leave</th>
                      <th scope="col">Sick Leave</th>
                      <th scope="col">Leave without Pay</th>
                      <th scope="col">Leave without Pay</th>
                      <th scope="col">Total Leave</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>1</td>
                      <td>4</td>
                      <td>5</td>
                      <td>6</td>
                      <td>13</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success close-modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function () {

        var deleteBtn = document.querySelectorAll('.delete-btn');
        var deleteModal = document.querySelector('#delete-modal');
        var closeModal = document.querySelectorAll('.close-modal');

        closeModal.forEach((d)=>{
            d.addEventListener('click',function(i){
                var modalParent = d.parentNode.parentNode.parentNode.parentNode;
                modalParent.classList.add('fade');
                setTimeout(function(){
                  modalParent.style.display = 'none';
                },400)
            });
        });

        deleteBtn.forEach((d)=>{
            d.addEventListener('click',function(){
                deleteModal.classList.remove('fade');
                deleteModal.style.display = 'block';
            });
        })

        document.querySelectorAll('.delete-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-id');
                document.getElementById('employee-id').value = employeeId;
                const deleteModal = new bootstrap.Modal(document.getElementById('delete-modal'));
                deleteModal.show();
            });
        });

        document.getElementById('confirm-archive').addEventListener('click', function() {
            const employeeId = document.getElementById('employee-id').value;

            fetch('archive_employee.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'id': employeeId
                })
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        });

        var addEmployeeBtn = document.querySelector('#add-employee-btn');
        var addEmployeeModal = document.querySelector('#add-employee-modal');
        var editEmployeeBtns = document.querySelectorAll('.edit-employee-btn')
        var editEmployeeModal = document.querySelector('#edit-modal');
        var closeModalwForm = document.querySelectorAll('.close-modalwForm');

        addEmployeeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addEmployeeModal.classList.remove('fade');
            addEmployeeModal.style.display = 'block';
        });

        function setSelectedValue(selector, value) {
            console.log(`Setting value ${value} for selector ${selector}`);
            const dropdown = document.querySelector(selector);
            if (dropdown) {
                dropdown.value = value;
            } else {
                console.error(`Element not found for selector: ${selector}`);
            }
        }

        const editBtns = document.querySelectorAll('.edit-employee-btn');
        const editModal = document.querySelector('#edit-modal');

        editBtns.forEach((button) => {
            button.addEventListener('click', function () {
                const employeeId = button.getAttribute('data-employee-id');
                const employeeNumber = button.getAttribute('data-employee-number');
                const lastname = button.getAttribute('data-lastname');
                const firstname = button.getAttribute('data-firstname');
                const middlename = button.getAttribute('data-middlename');
                const contactNo = button.getAttribute('data-contactno');
                const address = button.getAttribute('data-address');
                const birthDate = button.getAttribute('data-birthdate');
                const civilStatus = button.getAttribute('data-civil-status');
                const religion = button.getAttribute('data-religion');
                const basicSalary = button.getAttribute('data-basic-salary');
                const projectName = button.getAttribute('data-project-name');
                const hireDate = button.getAttribute('data-hire-date');
                const sssNo = button.getAttribute('data-sss-no');
                const philhealthNo = button.getAttribute('data-philhealth-no');
                const hdmfNo = button.getAttribute('data-hdmf-no');
                const tinNo = button.getAttribute('data-tin-no');
                const email = button.getAttribute('data-email');
                const password = button.getAttribute('data-password');
                const emergencyContactNo = button.getAttribute('data-emergency-contactno');
                const position = button.getAttribute('data-position-name');
                const employeeStatus = button.getAttribute('data-employee-status');
                const role = button.getAttribute('data-role-name');

                document.querySelector('#edit_employee_id').value = employeeId;
                document.querySelector('#edit_employee_number').value = employeeNumber;
                document.querySelector('#edit_lastname').value = lastname;
                document.querySelector('#edit_firstname').value = firstname;
                document.querySelector('#edit_middlename').value = middlename;
                document.querySelector('#edit_contactno').value = contactNo;
                document.querySelector('#edit_address').value = address;
                document.querySelector('#edit_birthdate').value = birthDate;

                setSelectedValue('#edit_civilstatus_select', civilStatus);
                setSelectedValue('#edit_position_select', position);
                setSelectedValue('#edit_project_name', projectName);
                setSelectedValue('#edit_employee_status_select', employeeStatus);
                setSelectedValue('#edit_role_name_select', role);

                document.querySelector('#edit_religion').value = religion;
                document.querySelector('#edit_basic_salary').value = basicSalary;
                document.querySelector('#edit_hire_date').value = hireDate;
                document.querySelector('#edit_sss_no').value = sssNo;
                document.querySelector('#edit_philhealth_no').value = philhealthNo;
                document.querySelector('#edit_hdmf_no').value = hdmfNo;
                document.querySelector('#edit_tin_no').value = tinNo;
                document.querySelector('#edit_email').value = email;
                document.querySelector('#edit_password').value = password;
                document.querySelector('#edit_emergency_contactno').value = emergencyContactNo;

                editModal.classList.remove('fade');
                editModal.style.display = 'block';
            });
        });

        closeModalwForm.forEach((d) => {
            d.addEventListener('click', function(i) {
                var modalParent = d.closest('.modal');
                modalParent.classList.add('fade');
                setTimeout(function() {
                    modalParent.style.display = 'none';
                }, 400);
                clearFormFields();
            });
        });

        function clearFormFields() {
            const form = document.getElementById('employee-form');
            if (form) {
                form.reset();
                document.getElementById('new_project_field').style.display = 'none';
            }
        }

        var leavelBtn = document.querySelectorAll('.leaves-btn');
        var leaveModal = document.querySelector('#leave-modal');

        leavelBtn.forEach((d)=>{
            d.addEventListener('click',function(){
                leaveModal.classList.remove('fade');
                leaveModal.style.display = 'block';
            });
        })

    

    });

    function toggleProjectField() {
        var projectDropdown = document.getElementById('project_name');
        var newProjectField = document.getElementById('new_project_field');

        if (projectDropdown.value === 'add_new') {
            newProjectField.style.display = 'block';
            document.getElementById('new_project_name').required = true;
        } else {
            newProjectField.style.display = 'none';
            document.getElementById('new_project_name').required = false;
        }
    }
      </script>
      <?php include './layout/footer.php'; ?>