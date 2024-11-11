<?php
session_start();
include 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$month_filter = isset($_GET['month']) ? (int)$_GET['month'] : '';
$year_filter = isset($_GET['year']) ? (int)$_GET['year'] : '';
$payroll_period_filter = isset($_GET['payroll_period']) ? $_GET['payroll_period'] : '';

$recordsPerPage = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $recordsPerPage;

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

$query = "SELECT pr.payroll_id, e.firstname, e.middlename, e.lastname, 
        pr.payroll_period, ps.position_name, pr.netpay
        FROM payroll pr 
        LEFT JOIN employees e ON e.employee_id = pr.employee_id
        LEFT JOIN positions ps ON ps.position_id = e.employee_id
        LIMIT :limit 
        OFFSET :offset
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$payslips = $stmt->fetchAll(PDO::FETCH_ASSOC);




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
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&month=<?php $pmonth_filter ?>&year=<?php $year_filter ?>&payroll_period=<?php $payroll_period_filter ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&month=<?php $pmonth_filter ?>&year=<?php $year_filter ?>&payroll_period=<?php $payroll_period_filter ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&month=<?php $pmonth_filter ?>&year=<?php $year_filter ?>&payroll_period=<?php $payroll_period_filter ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php include './layout/script.php'; ?>
<?php include './layout/footer.php'; ?>