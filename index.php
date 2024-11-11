<?php
session_start();
include('database.php');

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    echo "Session is not set. Redirecting to login...";
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila');

$activePage = "dashboard";

$role = $_SESSION['role_name'];
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
$posquery = "SELECT p.position_name, e.employee_status, COUNT(e.employee_id) AS employee_count
        FROM employees e
        JOIN positions p ON e.position_id = p.position_id
        WHERE employee_status != 'Archived'
        GROUP BY p.position_name
        ";

$result = $conn->query($posquery);
$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['position_name'];
        $data[] = $row['employee_count'];
    }
} else {
    echo 'No Data';
}

$permissions_query = "SELECT can_view_own_data, can_view_team_data, can_edit_data, can_manage_roles 
                      FROM permissions WHERE role_id = ?";

$permissions = [];
if ($stmt = $conn->prepare($permissions_query)) {
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->bind_result($can_view_own_data, $can_view_team_data, $can_edit_data, $can_manage_roles);
    if ($stmt->fetch()) {
        $permissions = [
            'can_view_own_data' => $can_view_own_data,
            'can_view_team_data' => $can_view_team_data,
            'can_edit_data' => $can_edit_data,
            'can_manage_roles' => $can_manage_roles
        ];
    }
    $stmt->close();
} else {
    echo "Error fetching permissions.";
}

$dash_employee_query = "SELECT * FROM employees WHERE employee_status != 'Archived'";
$dash_employee_query_run =  mysqli_query($conn, $dash_employee_query);

$dash_onleave_query = "SELECT * FROM leave_requests WHERE status != 'Archived' AND status = 'Approved'";
$dash_onleave_query_run = mysqli_query($conn, $dash_onleave_query);

$dash_onapproval_query = "SELECT * FROM leave_requests WHERE status != 'Archived' AND status = 'Pending'";
$dash_onapproval_query_run = mysqli_query($conn, $dash_onapproval_query);

$dash_onleave_query = "SELECT * FROM leave_requests WHERE status != 'Archived' AND status = 'Approved'";
$dash_onleave_query_run = mysqli_query($conn, $dash_onleave_query);

$dash_onapproval_query = "SELECT * FROM leave_requests WHERE status != 'Archived' AND status = 'Pending'";
$dash_onapproval_query_run = mysqli_query($conn, $dash_onapproval_query);

$query = "SELECT lastname FROM employees WHERE employee_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();

$today_date = date("Y-m-d");
$attendance_query = "
    SELECT e.employee_id, e.employee_number, e.firstname, e.lastname, d.time_in, p.project_name
    FROM dtr d
    JOIN employees e ON d.employee_id = e.employee_id
    LEFT JOIN projects p ON e.project_name = p.project_name
    WHERE d.date = ?";

$attendance_stmt = $conn->prepare($attendance_query);
$attendance_stmt->bind_param("s", $today_date);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();

$attendance_data = [];
while ($row = $attendance_result->fetch_assoc()) {
    $status = "On-Time";
    if (strtotime($row['time_in']) < strtotime('08:00:00')) {
        $status = "Early";
    } elseif (strtotime($row['time_in']) > strtotime('08:00:00')) {
        $status = "Late";
    }
    $attendance_data[] = [
        'employee_number' => $row['employee_number'],
        'name' => $row['firstname'] . ' ' . $row['lastname'],
        'time_in' => $row['time_in'],
        'project_name' => $row['project_name'],
        'status' => $status
    ];
}

$attendance_stmt->close();
$conn->close();
include 'layout/header.php';
?>

<div class="d-flex">
    <?php include 'layout/sidebar.php';
    ?>
    <div class="main pt-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid pl-5">
            <h2>Hello, <?php echo htmlspecialchars($name); ?>!</h2>
            <div class="row">
                <div class="col-12 col-lg-8 pt-3 pt-md-0">
                    <div class="row numbers-of">
                        <div class="col-12 my-2 col-lg-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="m-0">Number of Employees</div>
                                    <?php

                                    if ($employee_total = mysqli_num_rows($dash_employee_query_run)) {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">' . $employee_total . '</p>';
                                    } else {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">No Data</p>';
                                    }

                                    ?>
                                    <div class="m-0">This month</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-2 col-lg-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="m-0">On Leave</div>
                                    <?php

                                    if ($onleave_total = mysqli_num_rows($dash_onleave_query_run)) {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">' . $onleave_total . '</p>';
                                    } else {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">No Data</p>';
                                    }
                                    ?>
                                    <div class="m-0">This month</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-2 col-lg-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="m-0">Pending Approvals</div>
                                    <?php

                                    if ($onapproval_total = mysqli_num_rows($dash_onapproval_query_run)) {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">' . $onapproval_total . '</p>';
                                    } else {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">0</p>';
                                    }
                                    ?>
                                    <div class="m-0">For leaves</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-2 col-lg-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="m-0">Number of Request</div>
                                    <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">5</p>
                                    <div class="m-0">For Employees</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-3">
                            <h4>Attendance</h4>
                            <div class="table-responsive-lg" style="overflow-x: scroll;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employee Number</th>
                                        <th>Name</th>
                                        <th>Time-In</th>
                                        <th>Assigned Project</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendance_data as $attendance): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($attendance['employee_number']); ?></td>
                                            <td><?php echo htmlspecialchars($attendance['name']); ?></td>
                                            <td><?php echo htmlspecialchars($attendance['time_in']); ?></td>
                                            <td><?php echo htmlspecialchars($attendance['project_name']); ?></td>
                                            <td>
                                                <?php 
                                                    $badge_class = ($attendance['status'] === 'Early') ? 'badge-warning' : 
                                                                (($attendance['status'] === 'On-Time') ? 'badge-success' : 'badge-danger');
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo $attendance['status']; ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<canvas id="pie-grap" width="300" height="300"></canvas>

<script>
    const pie = document.getElementById('pie-grap').getContext('2d');

    const labels = <?php echo json_encode($labels); ?>;
    const data = <?php echo json_encode($data); ?>;

    const positionChart = new Chart(pie, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                label: 'Employees per Position',
                data: data,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#954535', '#00b300'
                ],
                hoverBackgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#954535', '#00b300'
                ],
                hoverOffset: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 2000,
                easing: 'easeInOutSine',
                animateRotate: true,
                animateScale: true
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            label += `: ${context.raw}`;
                            return label;
                        }
                    }
                },
            }
        },
    });
</script>
<?php include './layout/script.php'; ?>
<?php include 'layout/footer.php'; ?>