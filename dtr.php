<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

include './layout/header.php';
include 'database.php';

$dtr_results = $_SESSION['dtr_results'] ?? [];
$dtr_filters = $_SESSION['dtr_filters'] ?? [];

$role_name = strtolower($_SESSION['role_name'] ?? '');
$employee_id = $_SESSION['employee_id'] ?? 1;

$from_day_filter = $_GET['from_day'] ?? '';
$to_day_filter = $_GET['to_day'] ?? '';
$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$search_user = $_GET['search_user'] ?? '';
$project_filter = $_GET['project_name'] ?? '';
$month_filter = $_GET['month'] ?? '';
$year_filter = $_GET['year'] ?? '';
$from_day_filter = $_GET['from_day'] ?? '';
$to_day_filter = $_GET['to_day'] ?? '';
$today_filter = '';

if (empty($from_day_filter) && empty($to_day_filter) && empty($month_filter) && empty($year_filter)) {
    date_default_timezone_set("Asia/Manila");
    $today_filter = date('Y-m-d');
}

$query_projects = "SELECT project_name FROM projects";
$result_projects = mysqli_query($conn, $query_projects) or die("Error fetching projects: " . mysqli_error($conn));

$project_name = $_SESSION['project_name'] ?? null;

function getRoleBasedQuery($role_name)
{
    if ($role_name === 'super admin') {
        return "
            SELECT d.*, e.lastname, e.firstname, e.role_id, p.project_name
            FROM dtr d
            JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN projects p ON e.project_name = p.project_name
            WHERE 1=1";
    } elseif ($role_name === 'hr admin') {
        return "
            SELECT d.*, e.lastname, e.firstname, e.role_id, p.project_name
            FROM dtr d
            JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN projects p ON e.project_name = p.project_name
            WHERE e.role_id != (SELECT role_id FROM roles WHERE role_name = 'Super Admin')";
    } elseif ($role_name === 'admin') {
        return "
            SELECT d.*, e.lastname, e.firstname, e.role_id, p.project_name
            FROM dtr d
            JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN projects p ON e.project_name = p.project_name
            WHERE e.role_id NOT IN (
                SELECT role_id FROM roles WHERE role_name IN ('Super Admin', 'HR Admin')
            ) AND p.project_name = ?";
    } else {
        return "
            SELECT d.*, e.lastname, e.firstname, e.role_id, p.project_name
            FROM dtr d
            JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN projects p ON e.project_name = p.project_name
            WHERE d.employee_id = ?";
    }
}

$query_today = getRoleBasedQuery($role_name);

// Add filters to the query
if (!empty($search_user)) $query_today .= " AND (e.firstname LIKE ? OR e.middlename LIKE ? OR e.lastname LIKE ?)";
if (!empty($project_filter)) $query_today .= " AND p.project_name = ?";
if (!empty($month_filter)) $query_today .= " AND d.month = ?";
if (!empty($year_filter)) $query_today .= " AND d.year = ?";
if (!empty($from_day_filter) && !empty($to_day_filter)) {
    $query_today .= " AND (STR_TO_DATE(CONCAT(d.year, '-', LPAD(d.month, 2, '0'), '-', LPAD(d.day, 2, '0')), '%Y-%m-%d') BETWEEN ? AND ?)";
} elseif (!empty($from_day_filter)) {
    $query_today .= " AND (STR_TO_DATE(CONCAT(d.year, '-', LPAD(d.month, 2, '0'), '-', LPAD(d.day, 2, '0')), '%Y-%m-%d') >= ?)";
} elseif (!empty($to_day_filter)) {
    $query_today .= " AND (STR_TO_DATE(CONCAT(d.year, '-', LPAD(d.month, 2, '0'), '-', LPAD(d.day, 2, '0')), '%Y-%m-%d') <= ?)";
}
if (!empty($today_filter)) $query_today .= " AND d.date = ?";

// Add pagination
$query_today .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query_today);

$params = [];

// Add role-based parameters
if ($role_name === 'admin') {
    $params[] = $project_name;
} elseif ($role_name !== 'super admin' && $role_name !== 'hr admin') {
    $params[] = $employee_id;
}

// Add filters to parameters
if (!empty($search_user)) {
    $params[] = "%{$search_user}%";
    $params[] = "%{$search_user}%";
    $params[] = "%{$search_user}%";
}
if (!empty($project_filter)) $params[] = $project_filter;
if (!empty($month_filter)) $params[] = $month_filter;
if (!empty($year_filter)) $params[] = $year_filter;
if (!empty($from_day_filter) && !empty($to_day_filter)) {
    $params[] = $from_day_filter;
    $params[] = $to_day_filter;
} elseif (!empty($from_day_filter)) {
    $params[] = $from_day_filter;
} elseif (!empty($to_day_filter)) {
    $params[] = $to_day_filter;
}
if (!empty($today_filter)) $params[] = $today_filter;

// Add pagination parameters
$params[] = $records_per_page;
$params[] = $offset;

// Bind the parameters
$stmt->bind_param(str_repeat('s', count($params)), ...$params);

// Execute and fetch results
if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}
$result_today = $stmt->get_result();
$total_records = $result_today->num_rows;

$total_pages = ceil($total_records / $records_per_page);

$activePage = 'dtr';
?>

<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid pl-5">
            <h2>Generate Daily Time Record</h2>
            <form action="" method="GET">
                <div class="form-group row">
                <?php if ($role_name === 'super admin' || $role_name === 'hr admin' || $role_name === 'admin'): ?>
                    <div class="col-sm-2">
                        <input class="form-control" type="text" name="search_user" id="search_user" placeholder="Search User...">
                    </div>
                    <?php endif; ?>
                    <?php if ($role_name === 'super admin' || $role_name === 'hr admin'): ?>
                    <div class="col-sm-2">
                            <select name="project_name" id="project_name" class="form-control">
                                <option value="">Select Project</option>
                                <?php
                                while ($row_project = mysqli_fetch_assoc($result_projects)) {
                                    $selected = ($row_project['project_name'] === $project_filter) ? 'selected' : '';
                                    echo "<option value='{$row_project['project_name']}' $selected>{$row_project['project_name']}</option>";
                                }
                                ?>
                            </select>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-1 d-flex">
                        <select name="month" id="month" class="form-control">
                            <option value="">Month</option>
                            <?php for ($i = 1; $i <= 12; $i++) {
                                $selected = ($i == $month_filter) ? 'selected' : '';
                                echo "<option value='$i' $selected>" . date("F", mktime(0, 0, 0, $i, 10)) . "</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-1 d-flex">
                    <select name="year" id="year" class="form-control">
                            <option value="">Year</option>
                            <?php for ($y = 2021; $y <= 2024; $y++) {
                                $selected = ($y == $year_filter) ? 'selected' : '';
                                echo "<option value='$y' $selected>$y</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="col-sm-3 d-flex align-items-center justify-content-between">
                        <label>From</label>
                        &nbsp;
                        <input type="date" name="from_day" id="from_day" class="form-control" value="<?= htmlspecialchars($_GET['from_day'] ?? '') ?>" placeholder="From Day">
                        &nbsp;
                        <label>To</label>
                        &nbsp;
                        <input type="date" name="to_day" id="to_day" class="form-control" value="<?= htmlspecialchars($_GET['to_day'] ?? '') ?>" placeholder="To Day">
                    </div>
                    <div class="col-sm-3 d-flex">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        &nbsp;
                        <?php if ($role_name === 'super admin' || $role_name === 'hr admin' || $role_name === 'admin'): ?>
                        <button type="button" class="btn btn-secondary" id="attendance-btn">Attendance</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Day</th>
                                <th>Name</th>
                                <th>Project</th>
                                <th>Time-In</th>
                                <th>Time-Out</th>
                                <th>OT Hrs</th>
                                <th>Total Hrs</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $session_role_name = $_SESSION['role_name'];

                            if (mysqli_num_rows($result_today) > 0) {
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($result_today)) {
                                    $employee_id = $row['employee_id'];
                                    $day = date('j', strtotime($row['date']));
                                    $time_in_display = !empty($row['time_in']) ? date('h:i A', strtotime($row['time_in'])) : '';
                                    $time_out_display = !empty($row['time_out']) ? date('h:i A', strtotime($row['time_out'])) : '';

                                    if (!empty($row['time_in']) && !empty($row['time_out'])) {
                                        $time_in = new DateTime($row['time_in']);
                                        $time_out = new DateTime($row['time_out']);
                                        $interval = $time_in->diff($time_out);
                                        $today_hrs = $interval->h + ($interval->i / 60);
                                    } else {
                                        $today_hrs = 0;
                                    }

                                    // Subtract 1 hour for lunch break if total hours exceed 8
                                    if ($today_hrs > 8) {
                                        $today_hrs -= 1; // Subtract 1 hour for lunch break
                                    }

                                    // Calculate overtime (OT) if total hours exceed 8
                                    $overtime_threshold = 8;
                                    $today_ot = ($today_hrs > $overtime_threshold) ? $today_hrs - $overtime_threshold : 0;

                                    // Update the DTR table with total_hrs and other_ot
                                    $update_sql = "UPDATE dtr 
                                                SET total_hrs = ?, other_ot = ? 
                                                WHERE employee_id = ? AND date = ?";
                                    $update_stmt = $conn->prepare($update_sql);
                                    $update_stmt->bind_param("ddis", $today_hrs, $today_ot, $employee_id, $row['date']);

                                    if (!$update_stmt->execute()) {
                                        die("Error updating total_hrs and other_ot: " . $update_stmt->error);
                                    }

                                    // Retrieve other OT for this employee and date
                                    $sql = "SELECT time_in, time_out, other_ot FROM dtr 
                                                        WHERE employee_id = ? AND date < ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("is", $employee_id, $row['date']);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    $today_ot = !empty($row['other_ot']) ? $row['other_ot'] : 0;

                                    echo "<tr>";
                                    echo "<th scope='row'>$i</th>";
                                    echo "<td>$day</td>";
                                    echo "<td>{$row['lastname']}, {$row['firstname']}</td>";
                                    echo "<td>" . ($row['project_name'] ?? 'No Project Assigned') . "</td>";
                                    echo "<td>$time_in_display</td>";
                                    echo "<td>$time_out_display</td>";
                                    echo "<td>" . number_format($today_ot, 2) . "</td>";
                                    echo "<td>" . number_format($today_hrs, 2) . "</td>";
                                    if ($session_role_name !== 'Admin' && $session_role_name !== 'Employee') {
                                        echo "<td><button type='button' class='btn btn-success edit-btn' data-timein='{$row['time_in']}' data-timeout='{$row['time_out']}' data-did='{$row['dtr_id']}'>Edit</button></td>";
                                    } else {
                                        echo "<td></td>"; // Leave the cell empty for Admin and Employee
                                    }
                                    echo "</tr>";

                                    $i++;
                                }
                            } else {
                                echo "<tr><td colspan='9' style='text-align: center;font-size:20px;'>No attendance records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="d-flex justify-content-end">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <?php if ($current_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&search_user=<?php echo $search_user; ?>&project_name=<?php echo $project_filter; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&from_day=<?php echo $from_day_filter; ?>&to_day=<?php echo $to_day_filter; ?>">Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php if ($current_page == $i) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search_user=<?php echo $search_user; ?>&project_name=<?php echo $project_filter; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&from_day=<?php echo $from_day_filter; ?>&to_day=<?php echo $to_day_filter; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&search_user=<?php echo $search_user; ?>&project_name=<?php echo $project_filter; ?>&month=<?php echo $month_filter; ?>&year=<?php echo $year_filter; ?>&from_day=<?php echo $from_day_filter; ?>&to_day=<?php echo $to_day_filter; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendance-modal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">Edit Attendance</h5>
            </div>
            <div class="modal-body">
                <form id="attendanceForm" action="submit_attendance.php" method="POST" autocomplete="off" onsubmit="return validateAttendanceForm()">
                    <div class="form-group">
                        <label for="employee_number">Employee Number:</label>
                        <input type="text" id="employee_number" name="employee_number" class="form-control" placeholder="Enter employee number" required>
                        <input type="hidden" id="employee_id" name="employee_id">
                    </div>
                    <div class="form-group">
                        <label for="attendance_type">Time-In or Time-Out</label><br>
                        <input type="radio" name="attendance_action" value="time_in"> <span>Time-In</span><br>
                        <input type="radio" name="attendance_action" value="time_out"> <span>Time-Out</span>
                    </div>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" name="date" class="form-control" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" name="time" class="form-control" required readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendanceModalLabel">Attendance</h5>
            </div>
            <div class="modal-body">
                <form id="attendanceForm" action="edit_timein_timeout.php" method="POST" autocomplete="off">
                    <div class="form-group">
                        <input type="hidden" class="form-control edit-id" name="edit_id">
                        <div class="form-group mb-3">
                            <label for="edit_timein">Time-In:</label>
                            <input class="form-control edit-timein" type="time" id="edit_timein" name="edit_timein" value="" placeholder="Time in">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_timeout">Time-Out:</label>
                            <input class="form-control edit-timeout" type="time" id="edit_timeout" name="edit_timeout" value="" placeholder="Time out">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary edit-close">Close</button>
                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var attendanceBtn = document.querySelector('#attendance-btn');
    var attendanceModal = document.querySelector('#attendance-modal');

    attendanceBtn.addEventListener('click', function() {
        attendanceModal.classList.remove('fade');
        attendanceModal.style.display = 'block';

        var philippinesTime = new Date().toLocaleString("en-US", {
            timeZone: "Asia/Manila"
        });
        var philippinesDateTime = new Date(philippinesTime);

        var year = philippinesDateTime.getFullYear();
        var month = String(philippinesDateTime.getMonth() + 1).padStart(2, '0');
        var day = String(philippinesDateTime.getDate()).padStart(2, '0');

        var currentHours = String(philippinesDateTime.getHours()).padStart(2, '0');
        var currentMinutes = String(philippinesDateTime.getMinutes()).padStart(2, '0');

        var currentDate = `${year}-${month}-${day}`;
        var currentTime = `${currentHours}:${currentMinutes}`;

        document.querySelector('input[name="date"]').value = currentDate;
        document.querySelector('input[name="time"]').value = currentTime;
    });

    var attendanceBtn = document.querySelector('#attendance-btn');
    var attendanceModal = document.querySelector('#attendance-modal');
    attendanceBtn.addEventListener('click', function() {
        attendanceModal.classList.remove('fade');
        attendanceModal.style.display = 'block';
    });

    var closeModal = document.querySelectorAll('.close-modal');
    closeModal.forEach(function(button) {
        button.addEventListener('click', function() {
            attendanceModal.classList.add('fade');
            setTimeout(function() {
                attendanceModal.style.display = 'none';
            }, 400);
        });
    });

    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'submit_attendance.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    const res = JSON.parse(response);
                    let alertClass = '';
                    switch (res.status) {
                        case 'success':
                            alertClass = 'alert-success';
                            break;
                        case 'warning':
                            alertClass = 'alert-warning';
                            break;
                        case 'error':
                            alertClass = 'alert-danger';
                            break;
                    }
                    $('.modal-body').prepend(`
                                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                    ${res.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        $('#attendance-modal').on('hidden.bs.modal', function() {
            $('.alert').remove();
            $('form')[0].reset();
        });
    });

    function validateAttendanceForm() {
        const attendanceAction = document.querySelector('input[name="attendance_action"]:checked');

        if (!attendanceAction) {
            alert("Please select either Time-In or Time-Out.");
            return false;
        }

        return true;
    }
</script>
<?php include './layout/script.php'; ?>
<script>
    $(document).ready(function() {
        let editButton = $('.edit-btn');
        let editModal = $('#edit-modal')

        editButton.on('click', function() {
            let timein = $(this).data('timein');
            let timeout = $(this).data('timeout');
            let dtrID = $(this).data('did');
            editModal.removeClass('fade').css('display', 'block');
            $('.edit-id').val(dtrID);
            $('.edit-timein').val(timein);
            $('.edit-timeout').val(timeout);
        });
        $('.edit-close').on('click', function() {
            editModal.addClass('fade');
            setTimeout(function() {
                editModal.css('display', 'none');
            }, 400);
        });
    });
</script>
<?php include './layout/footer.php'; ?>