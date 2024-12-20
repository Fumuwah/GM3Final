<?php
session_start();
include('database.php');
include('handleAttendance.php');

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    echo "Session is not set. Redirecting to login...";
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila');

$activePage = "dashboard";

$role = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = $_SESSION['employee_id']; // Get employee_id from form data or session
    $action = $_POST['action']; // 'time_in' or 'time_out'

    $message = handleAttendance($employee_id, $action, $conn);
    echo "<script>alert('$message');</script>";

    // Redirect to prevent form re-submission on page reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit(); // Make sure to call exit after redirect
}


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
    echo '0';
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

$query = "SELECT lastname FROM employees WHERE employee_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();

$today_date = date("Y-m-d");

$attendance_data = []; // Initialize attendance data array

$attendance_query = "
    SELECT e.employee_number, e.firstname, e.lastname, lr.leave_type, lr.start_date, lr.end_date, lr.status
    FROM leave_requests lr
    JOIN employees e ON lr.employee_id = e.employee_id
    WHERE lr.start_date <= ? 
      AND lr.end_date >= ? 
      AND lr.status = 'Approved'";

// Prepare and bind parameters
$attendance_stmt = $conn->prepare($attendance_query);
$attendance_stmt->bind_param("ss", $today_date, $today_date);

// Execute and fetch results
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();

$attendance_data = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_data[] = [
        'employee_number' => $row['employee_number'],
        'name' => $row['firstname'] . ' ' . $row['lastname'],
        'leave_type' => $row['leave_type'],
        'from_date' => $row['start_date'],
        'to_date' => $row['end_date']
    ];
}

$attendance_stmt->close();

include 'layout/header.php';
?>

<div class="d-flex">
    <?php include 'layout/sidebar.php';
    ?>
    <div class="main pt-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid pl-5">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2>Hello <?php echo htmlspecialchars($name); ?>!</h2>
                    <div>
                        <form method="POST" action="">
                            <input type="hidden" name="employee_id" value="<?php echo $_SESSION['employee_id']; ?>">
                            <button type="submit" name="action" value="time_in" class="btn btn-success mr-2">Time-In</button>
                            <button type="submit" name="action" value="time_out" class="btn btn-danger">Time-Out</button>
                        </form>
                    </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-8 pt-3 pt-md-0">
                    <div class="row numbers-of">
                        <div class="col-12 my-2 col-lg-4">
                            <div class="card custom-border">
                                <div class="card-body text-center">
                                    <div class="m-0">Number of Employees</div>
                                    <?php
                                    
                                    if ($employee_total = mysqli_num_rows($dash_employee_query_run)) {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;"><a href="employees.php">' . $employee_total . '</a></p>';
                                    } else {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">0</p>';
                                    }
                                    
                                    ?>
                                    <div class="m-0">This month</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-2 col-lg-4">
                            <div class="card custom-border">
                                <div class="card-body text-center">
                                    <div class="m-0">Leave</div>
                                    <?php

                                    if ($onleave_total = mysqli_num_rows($dash_onleave_query_run)) {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;"><a href="hr-leaves-page.php">' . $onleave_total . '</a></p>';
                                    } else {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">No Data</p>';
                                    }
                                    ?>
                                    <div class="m-0">This month</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-2 col-lg-4">
                            <div class="card custom-border">
                                <div class="card-body text-center">
                                    <div class="m-0">Pending Approvals</div>
                                    <?php

                                    if ($onapproval_total = mysqli_num_rows($dash_onapproval_query_run)) {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;"><a href="hr-leaves-page.php">' . $onapproval_total . '</a></p>';
                                    } else {
                                        echo ' <p class="card-text font-weight-bold mt-3" style="font-size:2.2em;">0</p>';
                                    }
                                    ?>
                                    <div class="m-0">For leaves</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 my-10">
                            <h3>Announcement</h3>
                            <div class="card custom-border">
                                <div class="card-body">
                                    <?php
                                    $announcements_query = "SELECT a.message, a.created_at, e.firstname, e.lastname 
                                                            FROM announcements a 
                                                            JOIN employees e ON a.created_by = e.employee_id 
                                                            ORDER BY a.created_at DESC LIMIT 1";

                                    $result = $conn->query($announcements_query);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<div class='announcement-item'>";
                                            echo "<p><strong>" . htmlspecialchars($row['message']) . "</strong></p>";
                                            echo "<p><small>Posted by: " . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . " on " . htmlspecialchars($row['created_at']) . "</small></p>";
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<div class='m-0'>No announcements for today.</div>";
                                    }
                                    ?>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-primary announce-btn" id="announce-btn">New Announcement</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-12 my-3">
                            <h4>On-Leave</h4>
                            <div class="table-responsive-lg custom-border" style="overflow-x: scroll;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employee Number</th>
                                        <th>Name</th>
                                        <th>Leave Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($attendance_data)): ?>
                            <?php foreach ($attendance_data as $attendance): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($attendance['employee_number']); ?></td>
                                    <td><?php echo htmlspecialchars($attendance['name']); ?></td>
                                    <td><?php echo htmlspecialchars($attendance['leave_type']); ?></td>
                                    <td><?php echo htmlspecialchars($attendance['from_date']); ?></td>
                                    <td><?php echo htmlspecialchars($attendance['to_date']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No records found for today.</td>
                            </tr>
                        <?php endif; ?>
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

                <div class="card birthday-container mt-2 custom-border">
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
                    <div class="card-body custom-border">
                        <canvas id="pie-grap"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<div class="modal fade" id="announce-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="announce-form" action="submit_announcement.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Announcement</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row form-group">
                        <div class="col-12">
                            <label for="reason"></label>
                            <textarea name="reason" id="reason" class="form-control" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-modalwForm">Close</button>
                    <button type="submit" class="btn btn-success">Announce</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<canvas id="pie-grap" width="300" height="300"></canvas>

<script>
    const pie = document.getElementById('pie-grap').getContext('2d');

    const labels = <?php echo json_encode($labels); ?>; // Position labels
    const data = <?php echo json_encode($data); ?>; // Data for each position

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
            },
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    const segmentIndex = elements[0].index; // Get the clicked segment's index
                    const position = labels[segmentIndex]; // Get the corresponding position label
                    // Redirect to the employees page with the position_name filter
                    window.location.href = `employees.php?position_name=${encodeURIComponent(position)}`;
                }
            }
        },
    });
</script>

<script>
    document.querySelectorAll('.close-modalwForm').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('announce-modal').classList.add('fade');
            setTimeout(() => {
                document.getElementById('announce-modal').style.display = 'none';
            }, 400);
        });
    });

    var requestLeaveBtn = document.querySelector('#announce-btn');
    var requestLeaveModal = document.querySelector('#announce-modal');
    var closeModalwForm = document.querySelectorAll('.close-modalwForm');

    requestLeaveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        requestLeaveModal.classList.remove('fade');
        requestLeaveModal.style.display = 'block';
    });

    closeModalwForm.forEach((d) => {
        d.addEventListener('click', function(i) {
            var modalParent = d.parentNode.parentNode.parentNode.parentNode.parentNode;
            modalParent.classList.add('fade');
            setTimeout(function() {
                modalParent.style.display = 'none';
            }, 400)
        });
    });
</script>

<?php include './layout/script.php'; ?>
<?php include 'layout/footer.php'; ?>