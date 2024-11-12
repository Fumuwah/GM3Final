<?php
session_start();
include 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$recordsPerPage = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $recordsPerPage;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$projectName = isset($_GET['project_name']) ? $_GET['project_name'] : '';
$roleId = isset($_GET['role_id']) ? $_GET['role_id'] : '';
$payrollPeriod = isset($_GET['payroll_period']) ? $_GET['payroll_period'] : '';

$projectQuery = "SELECT project_name FROM projects";
$projectStmt = $pdo->prepare($projectQuery);
$projectStmt->execute();
$projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);

$roleQuery = "SELECT role_id, role_name FROM roles";
$roleStmt = $pdo->prepare($roleQuery);
$roleStmt->execute();
$roles = $roleStmt->fetchAll(PDO::FETCH_ASSOC);

$payrollPeriodQuery = "SELECT DISTINCT payroll_period FROM payroll";
$payrollPeriodStmt = $pdo->prepare($payrollPeriodQuery);
$payrollPeriodStmt->execute();
$payrollPeriods = $payrollPeriodStmt->fetchAll(PDO::FETCH_ASSOC);


$totalQuery = "SELECT COUNT(*) as total FROM payroll";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute();
$totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
$totalRecords = $totalRow['total'] != null ? $totalRow['total'] : 0;
$total_pages = ceil($totalRecords / $recordsPerPage);

$query = "
    SELECT e.employee_id AS employee_id, employee_number, e.firstname, e.middlename, e.lastname, 
           pr.monthly, pr.allowance, pr.total_hrs, pr.other_ot, pr.special_holiday, 
           pr.gross, pr.cash_adv, pr.total_deduc, pr.netpay
    FROM payroll pr
    LEFT JOIN employees e ON e.employee_id = pr.employee_id
    WHERE (e.firstname LIKE :search OR e.lastname LIKE :search OR e.employee_number LIKE :search)
";

$params = [':search' => '%' . $search . '%'];

if ($_SESSION['role_name'] === 'Admin' && isset($_SESSION['project_name'])) {
    $query .= " AND e.project_name = :userProjectName";
    $params[':userProjectName'] = $_SESSION['project_name'];
}

if (!empty($projectName)) {
    $query .= " AND e.project_name = :projectName";
    $params[':projectName'] = $projectName;
}

if (!empty($roleId)) {
    $query .= " AND e.role_id = :roleId";
    $params[':roleId'] = $roleId;
}

if (!empty($payrollPeriod)) {
    $query .= " AND pr.payroll_period = :payrollPeriod";
    $params[':payrollPeriod'] = $payrollPeriod;
}

$query .= " LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);

$activePage = 'generate_payroll_site';
// $role = $_SESSION['role'];
include './layout/header.php';
?>
<div class="d-flex align-items-stretch">
    <?php include './layout/sidebar.php'; ?>
    <div class="main pt-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid pl-5">
            <h2>Payroll Summary</h2>
            <form class="form-row align-items-center">
                <div class="form-group col-md-3 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-user" name="search" placeholder="Search User" value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>

                <div class="form-group col-md-2 mb-2">
                    <select name="project_name" class="form-control">
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?php echo htmlspecialchars($project['project_name']); ?>">
                                <?php echo htmlspecialchars($project['project_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-2 mb-2">
                    <select name="role_id" class="form-control">
                        <option value="">Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo htmlspecialchars($role['role_id']); ?>">
                                <?php echo htmlspecialchars($role['role_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-2 mb-2">
                    <select name="payroll_period" class="form-control">
                        <option value="">Payroll Period</option>
                        <?php foreach ($payrollPeriods as $period): ?>
                            <option value="<?php echo htmlspecialchars($period['payroll_period']); ?>">
                                <?php echo htmlspecialchars($period['payroll_period']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-1 mb-2">
                    <button type="submit" class="btn btn-primary ml-2">Generate</button>
                </div>
            </form>

            <div class="row mt-4">
                <div class="col-12 table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th colspan="2"></th>
                                <th colspan=2 class="text-center">Rate</th>
                                <th colspan=2 class="text-center">Payroll</th>
                                <th colspan="5"></th>
                            </tr>
                            <tr>
                                <th>Emp #</th>
                                <th>Name</th>
                                <th>Basic Salary</th>
                                <th>Allowance</th>
                                <th>REG</th>
                                <th>OT</th>
                                <th>S.H/V.L</th>
                                <th>Gross</th>
                                <th>Cash Advance</th>
                                <th>Deduction</th>
                                <th>Netpay</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($payrolls as $payroll): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payroll['employee_number']); ?></td>
                                    <td><?php echo htmlspecialchars($payroll['firstname'] . ' ' . $payroll['middlename'] . ' ' . $payroll['lastname']); ?></td>
                                    <td><?php echo number_format($payroll['monthly'], 2); ?></td>
                                    <td><?php echo number_format($payroll['allowance'], 2); ?></td>
                                    <td><?php echo number_format($payroll['total_hrs'], 2); ?></td>
                                    <td><?php echo number_format($payroll['other_ot'], 2); ?></td>
                                    <td><?php echo number_format($payroll['special_holiday'], 2); ?></td>
                                    <td><?php echo number_format($payroll['gross'], 2); ?></td>
                                    <td><?php echo number_format($payroll['cash_adv'], 2); ?></td>
                                    <td><?php echo number_format($payroll['total_deduc'], 2); ?></td>
                                    <td><?php echo number_format($payroll['netpay'], 2); ?></td>
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
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
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