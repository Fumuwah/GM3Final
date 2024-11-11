<?php
session_start();
include('database.php');

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$role_name = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];

$name = '';
$query = "SELECT lastname FROM employees WHERE employee_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error preparing the query.";
}

$conn->close();
include 'layout/header.php';
?>
<div class="d-flex align-items-stretch">
    <?php include 'layout/sidebar.php'; ?>
    <div class="main-content" style="max-height: calc(100vh - 70px); overflow-y:scroll">
        <div class="container-fluid">
            <h2>Hello <?php echo htmlspecialchars($name); ?></h2>
            <div class="row">
                <div class="col-12 col-lg-8 pt-3 pt-md-0">
                    <div class="row numbers-of">

                        <!-- DTR Summary -->
                        <div class="col-12 my-3">
                            <h4>DTR Summary</h4>
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time-In</th>
                                            <th>Time-Out</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT date, time_in, time_out FROM dtr WHERE employee_id = ?";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bind_param("i", $employee_id);
                                        $stmt->execute();
                                        $stmt->bind_result($date, $time_in, $time_out);
                                        while ($stmt->fetch()) {
                                            // Determine status based on time_in
                                            $time_in_status = strtotime($time_in);
                                            if ($time_in_status < strtotime("08:00:00")) {
                                                $status = "Early";
                                            } elseif ($time_in_status == strtotime("08:00:00")) {
                                                $status = "On Time";
                                            } else {
                                                $status = "Late";
                                            }
                                            
                                            echo "<tr>
                                                    <td>{$date}</td>
                                                    <td>{$time_in}</td>
                                                    <td>{$time_out}</td>
                                                    <td>{$status}</td>
                                                </tr>";
                                        }
                                        $stmt->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12">
                            <h4>Leaves Summary</h4>
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>No. of Days</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT start_date, end_date, leave_type, reason FROM leave_requests WHERE employee_id = ?";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bind_param("i", $employee_id);
                                        $stmt->execute();
                                        $stmt->bind_result($start_date, $end_date, $type, $description);

                                        while ($stmt->fetch()) {
                                            // Calculate number of leave days
                                            $start = new DateTime($start_date);
                                            $end = new DateTime($end_date);
                                            $num_days = $end->diff($start)->days + 1;

                                            echo "<tr>
                                                    <td>{$start_date}</td>
                                                    <td>{$end_date}</td>
                                                    <td>{$type}</td>
                                                    <td>{$description}</td>
                                                    <td>{$num_days}</td>
                                                </tr>";
                                        }
                                        $stmt->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payslips Summary -->
                        <div class="col-12">
                            <h4>Payslips Summary</h4>
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Payroll Period</th>
                                            <th>Basic Salary</th>
                                            <th>Overtime</th>
                                            <th>Deduction</th>
                                            <th>Net Pay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT p.payroll_period, e.basic_salary, p.other_ot, p.total_deduc, p.netpay
                                                FROM payroll p
                                                JOIN employees e ON p.employee_id = e.employee_id
                                                WHERE p.employee_id = ?";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bind_param("i", $employee_id);
                                        $stmt->execute();
                                        $stmt->bind_result($payroll_period, $basic_salary, $overtime, $deduction, $net_pay);

                                        while ($stmt->fetch()) {
                                            echo "<tr>
                                                    <td>{$payroll_period}</td>
                                                    <td>{$basic_salary}</td>
                                                    <td>{$overtime}</td>
                                                    <td>{$deduction}</td>
                                                    <td>{$net_pay}</td>
                                                </tr>";
                                        }
                                        $stmt->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Birthday List -->
                <div class="col-12 col-lg-4 pt-3 pt-md-0">
                    <?php include 'database.php'; ?>

                    <div class="card birthday-container mt-2">
                        <div class="card-header font-weight-bold">
                            Birthdays
                        </div>
                        <div class="card-body pt-2">
                            <ul class="p-0 mt-0">
                                <?php
                                try {
                                    $query = "SELECT employee_id, lastname, firstname, birthdate
                                              FROM employees
                                              WHERE MONTH(birthdate) = MONTH(CURDATE())
                                              AND DAY(birthdate) >= DAY(CURDATE())
                                              ORDER BY DAY(birthdate) ASC
                                              LIMIT 3";

                                    $stmt = $pdo->query($query);
                                    $upcoming_birthdays = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (!empty($upcoming_birthdays)) {
                                        foreach ($upcoming_birthdays as $employee) {
                                            $is_today = date('m-d') == date('m-d', strtotime($employee['birthdate']));

                                            echo "<li class='list-unstyled border-bottom py-3'>";
                                            echo "<div class='d-flex align-items-center'>";
                                            echo "<img src='/assets/images/account.png' alt='' style='width:35px;'>";
                                            echo "<div class='ml-2'>";
                                            echo "<p class='m-0'>" . htmlspecialchars($employee['firstname']) . " " . htmlspecialchars($employee['lastname']) . "</p>";
                                            echo "<p class='m-0' style='font-size: 12px; line-height: 8px;'>" . htmlspecialchars(date('m/d/Y', strtotime($employee['birthdate']))) . "</p>";
                                            echo "</div>";

                                            if ($is_today) {
                                                echo "<span class='badge badge-pill badge-success ml-2'>Today!</span>";
                                            } else {
                                                echo "<span class='badge badge-pill badge-info ml-2'>Coming Up</span>";
                                            }

                                            echo "</div>";
                                            echo "</li>";
                                        }
                                    } else {
                                        echo "<p class='text-center'>No upcoming birthdays this month.</p>";
                                    }
                                } catch (PDOException $e) {
                                    echo "Error fetching birthdays: " . $e->getMessage();
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Additional chart script if needed
</script>

<?php include 'layout/script.php'; ?>
<?php include 'layout/footer.php'; ?>
