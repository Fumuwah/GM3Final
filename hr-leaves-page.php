<?php
session_start();

include 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    echo "Session is not set. Redirecting to login...";
    header("Location: login.php");
    exit();
}

$role_name = strtolower($_SESSION['role_name']);
$project_name = $_SESSION['project_name'] ?? null;

$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

$selectedLeaveType = isset($_GET['leave_type']) ? $_GET['leave_type'] : '';

$excludeSuperAdminCondition = ($role_name === 'super admin') ? "AND lr.employee_id != ?" : "";

$types = "";
$params = [];

$countQuery = "SELECT COUNT(*) AS total FROM leave_requests lr
               JOIN employees e ON lr.employee_id = e.employee_id
               JOIN roles r ON e.role_id = r.role_id
               WHERE (r.role_name != 'Super Admin' OR (r.role_name = 'Super Admin' $excludeSuperAdminCondition))";

if ($selectedLeaveType) {
    $countQuery .= " AND lr.leave_type = ?";
    $types .= "s";
    $params[] = $selectedLeaveType;
}

if ($role_name === 'super admin') {
    $types .= "i";
    $params[] = $_SESSION['employee_id'];
}

$countStmt = $conn->prepare($countQuery);

if (strlen($types) !== count($params)) {
    die("Count Query Mismatch: Types length does not match params count.");
}

if (!empty($types)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRow = $countResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

$query = "
    SELECT lr.request_id, e.employee_id, e.firstname, e.lastname, lr.leave_type, lr.start_date, lr.end_date, lr.reason,
           (DATEDIFF(lr.end_date, lr.start_date) + 1) AS days_requested,
           (COALESCE(l.sick_leave, 0) + COALESCE(l.vacation_leave, 0)) AS total_leaves,
           lr.status, e.project_name
    FROM leave_requests lr
    JOIN employees e ON lr.employee_id = e.employee_id
    JOIN roles r ON e.role_id = r.role_id
    LEFT JOIN leaves l ON e.employee_id = l.employee_id
    WHERE (r.role_name != 'Super Admin' OR (r.role_name = 'Super Admin' $excludeSuperAdminCondition))";

$conditions = [];

if (!empty($_GET['employee_name'])) {
    $conditions[] = "CONCAT(e.firstname, ' ', e.lastname, ' ', e.middlename) LIKE ?";
    $params[] = '%' . $_GET['employee_name'] . '%';
    $types .= "s";
}

if (!empty($_GET['month'])) {
    $conditions[] = "MONTH(lr.start_date) = ?";
    $params[] = $_GET['month'];
    $types .= "i";
}

if (!empty($_GET['year']) && (int)$_GET['year'] > 0) {
    $conditions[] = "YEAR(lr.start_date) = ?";
    $params[] = $_GET['year'];
    $types .= "i";
}

if ($selectedLeaveType) {
    $conditions[] = "lr.leave_type = ?";
    $params[] = $selectedLeaveType;
    $types .= "s";
}

if ($role_name === 'admin') {
    if ($project_name) {
        $conditions[] = "e.project_name = ?";
        $params[] = $project_name;
        $types .= "s";
    } else {
        echo "Error: Project name is not set for Admin.<br>";
    }
}

if ($conditions) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY
    CASE
        WHEN lr.status = 'PENDING' THEN 1
        WHEN lr.status = 'APPROVED' THEN 2
        ELSE 3
    END,
    lr.start_date DESC
LIMIT ? OFFSET ?";

$types .= "ii";
$params[] = $recordsPerPage;
$params[] = $offset;

$stmt = $conn->prepare($query);

if (count($params) === strlen($types)) {
    if ($types) {
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param("");
    }
} else {
    die("Parameter count does not match the placeholders in the query.");
}
$stmt->execute();
$requests = $stmt->get_result();


$activePage = 'hr-leaves-page';

include './layout/header.php';
?>

<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main pt-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid pl-5">
            <h2>Leaves</h2>
            <div class="d-flex justify-content-between align-items-center">
            <form class="form-inline my-3 col-10 pl-0" method="get">
                <div class="form-group mb-2 col-8 col-lg-3">
                    <label for="type-of-leaves" class="sr-only">Type of Leaves</label>
                    <select name="leave_type" id="type-of-leaves" class="form-control w-100">
                        <option value="">All Types</option>
                        <option value="Sick Leave" <?= $selectedLeaveType === 'Sick Leave' ? 'selected' : '' ?>>Sick Leave</option>
                        <option value="Vacation Leave" <?= $selectedLeaveType === 'Vacation Leave' ? 'selected' : '' ?>>Vacation Leave</option>
                        <option value="Emergency Leave" <?= $selectedLeaveType === 'Emergency Leave' ? 'selected' : '' ?>>Emergency Leave</option>
                        <option value="Leave Without Pay" <?= $selectedLeaveType === 'Leave Without Pay' ? 'selected' : '' ?>>Leave Without Pay</option>
                    </select>
                </div>

                    <div class="form-group mb-2 col-8 col-lg-4">
                        <label for="employee-name" class="sr-only">Employee Name</label>
                        <input type="text" name="employee_name" id="employee-name" class="form-control w-100" placeholder="Employee Name" value="<?= htmlspecialchars($_GET['employee_name'] ?? '') ?>">
                    </div>

                    <div class="form-group mb-2 col-4 col-lg-2">
                        <label for="month" class="sr-only">Month</label>
                        <select name="month" id="month" class="form-control w-100">
                            <option value="">All Months</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group mb-2 col-4 col-lg-2">
                        <label for="year" class="sr-only">Year</label>
                        <select name="year" id="year" class="form-control">
                            <option value="">Year</option>
                            <?php for ($y = 2021; $y <= 2024; $y++) {
                                $selected = ($y == $_GET['year']) ? 'selected' : '';
                                echo "<option value='$y' $selected>$y</option>";
                            } ?>
                        </select>
                        <!-- <input type="number" name="year" id="year" class="form-control w-100" placeholder="Year" value="<?= htmlspecialchars($_GET['year'] ?? '') ?>"> -->
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Leave Type</th>
                            <th scope="col">Description</th>
                            <th scope="col">Days Requested</th>
                            <th scope="col">From Date</th>
                            <th scope="col">To Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($requests->num_rows > 0): ?>
                        <?php while ($row = $requests->fetch_assoc()): ?>
                            <?php
                                $canViewRequest = true;
                                if ($role_name === 'Admin' && $row['project_name'] !== $project_name) {
                                    $canViewRequest = false;
                                }                                
                            ?>
                            <?php if ($canViewRequest): ?>
                                <tr>
                                    <td><?= $row['request_id'] ?></td>
                                    <td><?= $row['firstname'] . ' ' . $row['lastname'] ?></td>
                                    <td><?= $row['leave_type'] ?></td>
                                    <td><?= $row['reason'] ?></td>
                                    <td><?= $row['days_requested'] ?></td>
                                    <td><?= $row['start_date'] ?></td>
                                    <td><?= $row['end_date'] ?></td>
                                    <td>
                                        <?php if ($row['employee_id'] == $_SESSION['employee_id']): ?>
                                            <?php if ($row['status'] === 'Approved'): ?>
                                                <span class="badge badge-success">Approved</span>
                                            <?php elseif ($row['status'] === 'Declined'): ?>
                                                <span class="badge badge-danger">Declined</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($row['status'] === 'Pending'): ?>
                                                <a href="approve_leave.php?request_id=<?= $row['request_id'] ?>" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this leave request?');">Approve</a>
                                                <a href="decline_leave.php?request_id=<?= $row['request_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to decline this leave request?');">Decline</a>
                                            <?php elseif ($row['status'] === 'Approved'): ?>
                                                <span class="badge badge-success">Approved</span>
                                            <?php elseif ($row['status'] === 'Declined'): ?>
                                                <span class="badge badge-danger">Declined</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&leave_type=<?= urlencode($selectedLeaveType) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&leave_type=<?= urlencode($selectedLeaveType) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<?php include './layout/script.php'; ?>
<?php include './layout/footer.php'; ?>