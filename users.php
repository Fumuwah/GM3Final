<?php
session_start();

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    echo "Session is not set. Redirecting to login...";
    header("Location: login.php");
    exit();
}
$activePage = 'user_controller';

include 'database.php';

function fetchTotalEmployeesCount($conn, $search = '')
{
    $search = mysqli_real_escape_string($conn, $search);
    $sql = "SELECT COUNT(*) AS total_count FROM employees e 
            LEFT JOIN positions p ON e.position_id = p.position_id
            LEFT JOIN roles r ON e.role_id = r.role_id
            WHERE 1";

    if (!empty($search)) {
        $sql .= " AND (e.firstname LIKE '%$search%' OR e.lastname LIKE '%$search%' OR e.employee_number LIKE '%$search%')";
    }

    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_count'];
    }
    return 0;
}

function fetchEmployees($conn, $start, $limit, $search = '')
{
    $search = mysqli_real_escape_string($conn, $search);
    $sql = "SELECT e.employee_id, e.lastname, e.firstname, 
                p.position_name, e.project_name, r.role_name, 
                IFNULL(perm.can_manage_roles, 0) AS can_manage_roles 
            FROM employees e
            LEFT JOIN positions p ON e.position_id = p.position_id
            LEFT JOIN roles r ON e.role_id = r.role_id
            LEFT JOIN permissions perm ON r.role_id = perm.role_id
            -- WHERE 1
            WHERE e.employee_status != 'Archived'
            ";

    if (!empty($search)) {
        $sql .= " AND (e.firstname LIKE '%$search%' OR e.lastname LIKE '%$search%' OR e.employee_number LIKE '%$search%')";
    }

    $sql .= " LIMIT $start, $limit";

    

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Error fetching employees: " . mysqli_error($conn));
    }
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];
    $new_role_id = $_POST['role_id'];

    // Prevent Super Admin from changing their own role
    if ($_SESSION['role_name'] === 'Super Admin' && $employee_id == $_SESSION['employee_id']) {
        $_SESSION['message'] = "You cannot change your own role as a Super Admin.";
        header("Location: users.php");
        exit();
    }

    // Fetch the current role of the employee being updated
    $check_role_stmt = $conn->prepare("SELECT role_id FROM employees WHERE employee_id = ?");
    $check_role_stmt->bind_param("i", $employee_id);
    $check_role_stmt->execute();
    $check_role_result = $check_role_stmt->get_result();

    if ($check_role_result && $check_role_result->num_rows > 0) {
        $row = $check_role_result->fetch_assoc();
        $current_role_id = $row['role_id'];

        // Role transition rules
        $admin_roles = [2, 3]; // HR Admin (4) and Admin (2)

        // Prevent downgrading HR Admin or Admin to non-admin roles
        if (in_array($current_role_id, $admin_roles) && !in_array($new_role_id, $admin_roles)) {
            $_SESSION['message'] = "You cannot downgrade HR Admin or Admin to non-admin roles.";
            header("Location: users.php");
            exit();
        }

        // Allow role change if rules are satisfied
        if ($new_role_id != $current_role_id) {
            $update_stmt = $conn->prepare("UPDATE employees SET role_id = ? WHERE employee_id = ?");
            $update_stmt->bind_param("ii", $new_role_id, $employee_id);

            if ($update_stmt->execute()) {
                $_SESSION['message'] = "Role updated successfully!";
            } else {
                $_SESSION['message'] = "Error updating role: " . $conn->error;
            }
        } else {
            $_SESSION['message'] = "No changes detected. Role remains the same.";
        }
    } else {
        $_SESSION['message'] = "Error: Employee not found.";
    }

    header("Location: users.php");
    exit();
}



$search = isset($_GET['search']) ? $_GET['search'] : '';
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$start = ($current_page - 1) * $limit;

$total_count = fetchTotalEmployeesCount($conn, $search);
$total_pages = ceil($total_count / $limit);

$employees = fetchEmployees($conn, $start, $limit, $search);

include './layout/header.php';
?>

<script>
    function confirmRoleChange(form) {
        if (confirm("Are you sure you want to change this employee's role?")) {
            form.submit();
        }
        return false;
    }

    function showMessage() {
        var message = "<?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>";
        if (message) {
            alert(message);
            <?php unset($_SESSION['message']); ?>
        }
    }

    window.onload = showMessage;
</script>

<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main pt-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
    <div class="container-fluid pl-5">
            <h2>Users</h2>
            <form class="form-inline my-3">
                <div class="d-flex w-100 mb-2 justify-content-between">
                    <div class="flex-grow" style="flex-grow: 1;">
                        <input type="text" class="form-control w-100" id="search-user" name="search"
                            placeholder="Search User"
                            value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="flex-end" style="">
                        <button type="submit" class="btn btn-primary mb-2">Search</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive-lg" style="overflow-x: scroll;">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 30%;">Name</th>
                            <th scope="col" style="width: 10%;">Position</th>
                            <th scope="col" style="width: 15%;">Project</th>
                            <th scope="col" style="width: 10%;">Role</th>
                            <th scope="col" style="width: 30%;">Update Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($employees && mysqli_num_rows($employees) > 0): ?>
                            <?php
                            $counter = 0;
                            while ($row = mysqli_fetch_assoc($employees)):
                                $counter++;
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $row['employee_id']; ?></th>
                                    <td><?php echo $row['lastname'] . ', ' . $row['firstname']; ?></td>
                                    <td><?php echo ucfirst($row['position_name']); ?></td>
                                    <td><?php echo ucfirst($row['project_name']); ?></td>
                                    <td><?php echo ucfirst($row['role_name']); ?></td>
                                    <td>
                                        <?php if ($row['can_manage_roles'] || $_SESSION['role_name'] == 'Super Admin'): ?>
                                            <form method="POST" onsubmit="return confirmRoleChange(this);">
                                                <input type="hidden" name="employee_id" value="<?php echo $row['employee_id']; ?>">
                                                <div class="d-flex">
                                                    <div class="flex-fill">
                                                        <select name="role_id" class="form-control">
                                                            <option value="1" <?php if ($row['role_name'] == 'Super Admin') echo 'selected'; ?>>Super Admin</option>
                                                            <option value="2" <?php if ($row['role_name'] == 'HR Admin') echo 'selected'; ?>>HR Admin</option>
                                                            <option value="3" <?php if ($row['role_name'] == 'Admin') echo 'selected'; ?>>Admin</option>
                                                            <option value="4" <?php if ($row['role_name'] == 'Employee') echo 'selected'; ?>>Employee</option>
                                                        </select>
                                                    </div>
                                                    <div class="flex-end">
                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                    </div>
                                                </div>

                                            </form>
                                        <?php else: ?>
                                            <span>No permission to manage roles</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile;
                            ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No employees found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php include './layout/script.php'; ?>
<?php include './layout/footer.php'; ?>