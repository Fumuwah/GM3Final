<?php
session_start();
include 'database.php';

// Redirect if not logged in
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$role_name = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];

// Get filter parameters
$month_filter = isset($_GET['month']) ? (int)$_GET['month'] : '';
$year_filter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$days_filter = isset($_GET['days']) ? $_GET['days'] : '';

// Pagination settings
$recordsPerPage = isset($_GET['recordsPerPage']) ? (int)$_GET['recordsPerPage'] : 5; // Default to 5 records per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Default to page 1
$offset = ($current_page - 1) * $recordsPerPage;

// Base query
$query = "SELECT pr.payroll_id, e.firstname, e.middlename, e.lastname, 
              pr.days, ps.position_name, pr.netpay, pr.payroll_period
          FROM payroll pr 
          LEFT JOIN employees e ON e.employee_id = pr.employee_id
          LEFT JOIN positions ps ON ps.position_id = e.position_id";

// Apply role-based restrictions
$conditions = [];  // Array to store conditions

if ($role_name === "Employee") {
    $conditions[] = "pr.employee_id = :employee_id";
} elseif ($role_name === "Admin") {
    // Get Admin's project_name
    $projectQuery = "SELECT project_name FROM employees WHERE employee_id = :employee_id";
    $projectStmt = $pdo->prepare($projectQuery);
    $projectStmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
    $projectStmt->execute();
    $project = $projectStmt->fetchColumn();

    $conditions[] = "(e.project_name = :project_name OR pr.employee_id = :employee_id)";
}

// Add conditions for month, year, and days filters if set
if (!empty($month_filter)) {
    $conditions[] = "pr.month = :month";
}
if (!empty($year_filter)) {
    $conditions[] = "pr.year = :year";
}
if (!empty($days_filter)) {
    $conditions[] = "pr.days = :days";
}

// Append conditions to the query if any exist
if (!empty($conditions)) {
    $query .= ' WHERE ' . implode(' AND ', $conditions);
}

// Add pagination
$query .= " LIMIT :limit OFFSET :offset";

// Prepare statement
$stmt = $pdo->prepare($query);

// Bind parameters for filtering
if ($role_name === "Employee") {
    $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
} elseif ($role_name === "Admin") {
    $stmt->bindParam(':project_name', $project, PDO::PARAM_STR);
    $stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
}

if (!empty($month_filter)) {
    $stmt->bindParam(':month', $month_filter, PDO::PARAM_INT);
}
if (!empty($year_filter)) {
    $stmt->bindParam(':year', $year_filter, PDO::PARAM_INT);
}
if (!empty($days_filter)) {
    $stmt->bindParam(':days', $days_filter, PDO::PARAM_STR);
}

$stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// Execute the query
$stmt->execute();
$payslips = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch days values for the dropdown
$daysQuery = "SELECT DISTINCT days FROM payroll";
$daysStmt = $pdo->prepare($daysQuery);
$daysStmt->execute();
$payrollPeriods = $daysStmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination total calculation
$totalQuery = "SELECT COUNT(*) as total FROM payroll pr";
if (!empty($conditions)) {
    $totalQuery .= ' WHERE ' . implode(' AND ', $conditions);
}
$totalStmt = $pdo->prepare($totalQuery);

// Bind parameters for total count
if ($role_name === "Employee") {
    $totalStmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
} elseif ($role_name === "Admin") {
    $totalStmt->bindParam(':project_name', $project, PDO::PARAM_STR);
    $totalStmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
}

if (!empty($month_filter)) {
    $totalStmt->bindParam(':month', $month_filter, PDO::PARAM_INT);
}
if (!empty($year_filter)) {
    $totalStmt->bindParam(':year', $year_filter, PDO::PARAM_INT);
}
if (!empty($days_filter)) {
    $totalStmt->bindParam(':days', $days_filter, PDO::PARAM_STR);
}

$totalStmt->execute();
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalRecords = $totalRow['total'] ?? 0;
$total_pages = ceil($totalRecords / $recordsPerPage);

$activePage = 'payslip';
include './layout/header.php';

?>

<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <h2>Payslips</h2>
        <form action="" method="get">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <select name="month" id="month" class="form-control">
                            <option value="">Month</option>
                            <?php for ($i = 1; $i <= 12; $i++) {
                                $selected = ($i == $month_filter) ? 'selected' : '';
                                echo "<option value='$i' $selected>" . date("F", mktime(0, 0, 0, $i, 10)) . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <select name="year" id="year" class="form-control">
                            <option value="">Year</option>
                            <?php for ($y = 2021; $y <= 2024; $y++) {
                                $selected = ($y == $year_filter) ? 'selected' : '';
                                echo "<option value='$y' $selected>$y</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <select name="payroll_period" class="form-control">
                        <option value="">Payroll Period</option>
                        <?php foreach ($payrollPeriods as $period): ?>
                            <option value="<?php echo htmlspecialchars($period['days']); ?>">
                                <?php echo htmlspecialchars($period['days']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-3">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Payroll Period</th>
                            <th>Position</th>
                            <th>Net Pay</th>
                            <th>Payslip</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payslips as $payslip): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payslip['firstname'] . ' ' . $payslip['middlename'] . ' ' . $payslip['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($payslip['payroll_period']) ?></td>
                                <td><?php echo htmlspecialchars($payslip['position_name']) ?></td>
                                <td><?php echo htmlspecialchars($payslip['netpay']) ?></td>
                                <td><a class="btn btn-primary text-light" href="payslip_overview.php?pid=<?php echo $payslip['payroll_id'] ?>">Overview</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&days=<?php echo $days_filter; ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&days=<?php echo $days_filter; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&days=<?php echo $days_filter; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php include './layout/script.php'; ?>
<?php include './layout/footer.php'; ?>