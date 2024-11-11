<?php
session_start();
include 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$role_name = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];

// Filtering
$month_filter = isset($_GET['month']) ? (int)$_GET['month'] : '';
$year_filter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$payroll_period_filter = isset($_GET['payroll_period']) ? $_GET['payroll_period'] : '';

// Base query
$query = "SELECT pr.payroll_id, e.firstname, e.middlename, e.lastname, 
              pr.payroll_period, ps.position_name, pr.netpay
          FROM payroll pr 
          LEFT JOIN employees e ON e.employee_id = pr.employee_id
          LEFT JOIN positions ps ON ps.position_id = e.position_id";

// Apply role-based restrictions
if ($role_name === "Employee") {
    $query .= " WHERE pr.employee_id = :employee_id";
} elseif ($role_name === "Admin") {
    // Get Admin's project_name
    $projectQuery = "SELECT project_name FROM employees WHERE employee_id = :employee_id";
    $projectStmt = $pdo->prepare($projectQuery);
    $projectStmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
    $projectStmt->execute();
    $project = $projectStmt->fetchColumn();

    $query .= " WHERE (e.project_name = :project_name OR pr.employee_id = :employee_id)";
}

// Additional filtering for month, year, and payroll period
$filters = [];
if (!empty($month_filter)) {
    $filters[] = "MONTH(pr.payroll_period) = :month";
}
if (!empty($year_filter)) {
    $filters[] = "YEAR(pr.payroll_period) = :year";
}
if (!empty($payroll_period_filter)) {
    $filters[] = "pr.payroll_period = :payroll_period";
}

// Append filters if any
if (!empty($filters)) {
    $query .= ' AND ' . implode(' AND ', $filters);
}

// Pagination parameters
$recordsPerPage = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $recordsPerPage;
$query .= " LIMIT :limit OFFSET :offset";

// Prepare statement
$stmt = $pdo->prepare($query);

// Bind parameters for filtering and pagination
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
if (!empty($payroll_period_filter)) {
    $stmt->bindParam(':payroll_period', $payroll_period_filter, PDO::PARAM_STR);
}

$stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// Execute the query
$stmt->execute();
$payslips = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch payroll periods for the dropdown
$payrollPeriodQuery = "SELECT DISTINCT payroll_period FROM payroll";
$payrollPeriodStmt = $pdo->prepare($payrollPeriodQuery);
$payrollPeriodStmt->execute();
$payrollPeriods = $payrollPeriodStmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination total records count
$totalQuery = "SELECT COUNT(*) as total FROM payroll";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute();
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalRecords = $totalRow['total'] != null ? $totalRow['total'] : 0;
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
                            <option value="<?php echo htmlspecialchars($period['payroll_period']); ?>">
                                <?php echo htmlspecialchars($period['payroll_period']); ?>
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
                                <td><a class="btn btn-primary text-light" href="payslip_overview?pid=<?php echo $payslip['payroll_id'] ?>">Overview</a></td>
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
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&payroll_period=<?php echo $payroll_period_filter; ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&payroll_period=<?php echo $payroll_period_filter; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&payroll_period=<?php echo $payroll_period_filter; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php include './layout/script.php'; ?>
<?php include './layout/footer.php'; ?>
