<?php
session_start();
include('database.php');

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$role_name = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];

$conn->close();
include 'layout/header.php';
?>
<div class="d-flex align-items-stretch">
    <?php include 'layout/sidebar.php'; ?>
    <div class="main" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid">
            <h2>Hello <?php echo htmlspecialchars($role_name); ?></h2>
            <div class="row">
                <div class="col-12 col-lg-8 pt-3 pt-md-0">
                    <div class="row numbers-of">
                        <div class="col-12 my-3">
                            <h4>DTR Summary</h4>
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time-In</th>
                                            <th>Time-out</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>10/28/24</td>
                                            <td>07:00 AM</td>
                                            <td>04:00 PM</td>
                                            <td>n</td>
                                        </tr>
                                        <tr>
                                            <td>10/29/24</td>
                                            <td>7:20 AM</td>
                                            <td>04:20 PM</td>
                                            <td>n</td>
                                        </tr>
                                        <tr>
                                            <td>10/30/24</td>
                                            <td>6:30 AM</td>
                                            <td>4:00 PM</td>
                                            <td>n</td>
                                        </tr>
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
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>No. of Leaves</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>11/04/24</td>
                                            <td>Sick Leave</td>
                                            <td>Fever</td>
                                            <td>2</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <h4>Payslips Summary</h4>
                            <div class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Payroll Period</th>
                                            <th>Basic Salary</th>
                                            <th>Over time</th>
                                            <th>Deduction</th>
                                            <th>Net Pay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>8-15</td>
                                            <td>10000</td>
                                            <td>4</td>
                                            <td>0</td>
                                            <td>2708.33</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 pt-3 pt-md-0">
                    <?php
                    include 'database.php';
                    ?>

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
                    <div class="card mt-2">
                        <div class="card-body">
                            <canvas id="pie-grap"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

</script>

<?php include 'layout/script.php'; ?>
<?php include 'layout/footer.php'; ?>