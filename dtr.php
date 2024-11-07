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

$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$project_filter = $_POST['project_name'] ?? '';
$month_filter = $_POST['month'] ?? '';
$year_filter = $_POST['year'] ?? '';
$day_filter = $_POST['day'] ?? '';

$query_projects = "SELECT project_name FROM projects";
$result_projects = mysqli_query($conn, $query_projects) or die("Error fetching projects: " . mysqli_error($conn));

function getRoleBasedQuery($role_name, $employee_id) {
    if ($role_name === 'super admin') {
        $query = "
            SELECT d.*, e.lastname, e.firstname, p.project_name
            FROM dtr d
            JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN projects p ON e.project_name = p.project_name
            WHERE 1=1"; 
    } elseif ($role_name === 'admin') {
        $query = "
            SELECT d.*, e.lastname, e.firstname, p.project_name
            FROM dtr d
            JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN projects p ON e.project_name = p.project_name
            WHERE e.employee_id = ?";
    } else {
        $query = "
            SELECT d.*, e.lastname, e.firstname, p.project_name
            FROM dtr d
            JOIN employees e ON d.employee_id = e.employee_id
            LEFT JOIN projects p ON e.project_name = p.project_name
            WHERE d.employee_id = ?";
    }
    return $query;
}

$query_today = getRoleBasedQuery($role_name, $employee_id);

if (!empty($project_filter)) $query_today .= " AND p.project_name = ?";
if (!empty($month_filter)) $query_today .= " AND d.month = ?";
if (!empty($year_filter)) $query_today .= " AND d.year = ?";
if (!empty($day_filter)) $query_today .= " AND d.day = ?";

$query_today .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query_today);

$params = [];
if ($role_name !== 'super admin') {
    $params[] = $employee_id;
}
if (!empty($project_filter)) $params[] = $project_filter;
if (!empty($month_filter)) $params[] = $month_filter;
if (!empty($year_filter)) $params[] = $year_filter;
if (!empty($day_filter)) $params[] = $day_filter;
$params[] = $records_per_page;
$params[] = $offset;

if ($params) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}

if (!$stmt->execute()) {
    die("Error executing statement: " . $stmt->error);
}
$result_today = $stmt->get_result();

$total_query = getRoleBasedQuery($role_name, $employee_id);
if (!empty($project_filter)) $total_query .= " AND p.project_name = ?";
if (!empty($month_filter)) $total_query .= " AND d.month = ?";
if (!empty($year_filter)) $total_query .= " AND d.year = ?";
if (!empty($day_filter)) $total_query .= " AND d.day = ?";

$total_stmt = $conn->prepare($total_query);
$params_total = [];
if ($role_name !== 'super admin') $params_total[] = $employee_id;
if (!empty($project_filter)) $params_total[] = $project_filter;
if (!empty($month_filter)) $params_total[] = $month_filter;
if (!empty($year_filter)) $params_total[] = $year_filter;
if (!empty($day_filter)) $params_total[] = $day_filter;

if ($params_total) {
    $total_stmt->bind_param(str_repeat('s', count($params_total)), ...$params_total);
}

$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_records = $total_result->num_rows;

$total_pages = ceil($total_records / $records_per_page);

$activePage = 'dtr';
?>
            <div class="d-flex">
                <?php include './layout/sidebar.php'; ?>
                <div class="main-content">
                    <div class="container-fluid">
                        <h2>Generate Daily Time Record</h2>
                        <form action="" method="POST">
                            <div class="form-group row">
                                <div class="col-sm-3">
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
                                <div class="col-sm-2">
                                    <select name="month" id="month" class="form-control">
                                        <option value="">Month</option>
                                        <?php for ($i = 1; $i <= 12; $i++) {
                                            $selected = ($i == $month_filter) ? 'selected' : '';
                                            echo "<option value='$i' $selected>" . date("F", mktime(0, 0, 0, $i, 10)) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select name="year" id="year" class="form-control">
                                        <option value="">Year</option>
                                        <?php for ($y = 2021; $y <= 2024; $y++) {
                                            $selected = ($y == $year_filter) ? 'selected' : '';
                                            echo "<option value='$y' $selected>$y</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <select name="day" id="day" class="form-control">
                                        <option value="">Day</option>
                                        <?php for ($d = 1; $d <= 31; $d++) {
                                            $selected = ($d == $day_filter) ? 'selected' : '';
                                            echo "<option value='$d' $selected>$d</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="#" class="btn btn-secondary" id="attendance-btn">Attendance</a>
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

                                                $update_sql = "UPDATE dtr 
                                                SET total_hrs = ? 
                                                WHERE employee_id = ? AND date = ?";
                                                $update_stmt = $conn->prepare($update_sql);
                                                $update_stmt->bind_param("dis", $today_hrs, $employee_id, $row['date']);

                                                if (!$update_stmt->execute()) {
                                                die("Error updating total_hrs: " . $update_stmt->error);
                                                }

                                                $sql = "SELECT time_in, time_out, other_ot FROM dtr 
                                                        WHERE employee_id = ? AND date < ?";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->bind_param("is", $employee_id, $row['date']);
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                $previous_hrs = 0;
                                                $previous_ot = 0;

                                                while ($previous_row = $result->fetch_assoc()) {
                                                    if (!empty($previous_row['time_in']) && !empty($previous_row['time_out'])) {
                                                        $prev_in = new DateTime($previous_row['time_in']);
                                                        $prev_out = new DateTime($previous_row['time_out']);
                                                        $prev_interval = $prev_in->diff($prev_out);
                                                        $previous_hrs += $prev_interval->h + ($prev_interval->i / 60); 
                                                    }

                                                    $previous_ot += !empty($previous_row['other_ot']) ? $previous_row['other_ot'] : 0;
                                                }

                                                $today_ot = !empty($row['other_ot']) ? $row['other_ot'] : 0;
                                                $cumulative_ot = $previous_ot + $today_ot;

                                                $cumulative_hrs = $today_hrs + $previous_hrs;

                                                echo "<tr>";
                                                echo "<th scope='row'>$i</th>";
                                                echo "<td>$day</td>";
                                                echo "<td>{$row['lastname']}, {$row['firstname']}</td>";
                                                echo "<td>" . ($row['project_name'] ?? 'No Project Assigned') . "</td>";
                                                echo "<td>$time_in_display</td>";
                                                echo "<td>$time_out_display</td>";
                                                echo "<td>" . number_format($cumulative_ot, 2) . "</td>";
                                                echo "<td>" . number_format($cumulative_hrs, 2) . "</td>";
                                                echo "<td><a href='#' class='btn btn-success'>Edit</a></td>";
                                                echo "</tr>";

                                                $i++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='9'>No attendance records found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                                    
                        <div class="pagination">
                            <?php if ($current_page > 1): ?>
                                <a href="?page=<?php echo $current_page - 1; ?>">&laquo; Previous</a>
                            <?php endif; ?>
                        
                            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                                <?php if ($page == $current_page): ?>
                                    <strong><?php echo $page; ?></strong>
                                <?php else: ?>
                                    <a href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        
                            <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?php echo $current_page + 1; ?>">Next &raquo;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="attendance-modal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="attendanceModalLabel">Attendance</h5>
                        </div>
                        <div class="modal-body">
                            <form id="attendanceForm" action="submit_attendance.php" method="POST" autocomplete="off">
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
                                    <input type="date" name="date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="time">Time</label>
                                    <input type="time" name="time" class="form-control" required>
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
            <script>
                var attendanceBtn = document.querySelector('#attendance-btn');
                var attendanceModal = document.querySelector('#attendance-modal');
            
                attendanceBtn.addEventListener('click', function() {
                    attendanceModal.classList.remove('fade');
                    attendanceModal.style.display = 'block';
            
                    var philippinesTime = new Date().toLocaleString("en-US", { timeZone: "Asia/Manila" });
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

                $(document).ready(function () {
                $('form').on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: 'submit_attendance.php',
                        type: 'POST',
                        data: $(this).serialize(),
                        success: function (response) {
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
                        error: function () {
                            alert('An error occurred. Please try again.');
                        }
                    });
                });

                $('#attendance-modal').on('hidden.bs.modal', function () {
                    $('.alert').remove();
                    $('form')[0].reset();
                });
            });

            </script>
      <?php include './layout/footer.php'; ?>