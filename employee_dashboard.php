<?php
session_start();
include('database.php');
include('handleAttendance.php');

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila');

$role_name = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];

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



$activePage = 'employee_dashboard';

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
    <div class="main" style="max-height: calc(100vh - 80px);overflow-y:scroll">
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
                                    <?php if ($role_name === 'Super Admin' || $role_name === 'HR Admin'): ?>
                                    <button class="btn btn-primary announce-btn" id="announce-btn">Announcement</button>
                                    <?php endif; ?>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 my-3">
                            <h4>DTR Summary</h4>
                            <div class="overflow-x-auto custom-border">
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
                                        $query = "SELECT date, time_in, time_out FROM dtr WHERE employee_id = ? ORDER BY date DESC LIMIT 3";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bind_param("i", $employee_id);
                                        $stmt->execute();
                                        $stmt->bind_result($date, $time_in, $time_out);
                                        while ($stmt->fetch()) {
                                            $formatted_time_in = $time_in ? date("h:i A", strtotime($time_in)) : "-";
                                            $formatted_time_out = $time_out ? date("h:i A", strtotime($time_out)) : "-";
                                            
                                            $time_in_status = strtotime($time_in);
                                            if ($time_in_status < strtotime("07:00:00")) {
                                                $status = "Early";
                                            } elseif ($time_in_status == strtotime("07:00:00")) {
                                                $status = "On Time";
                                            } else {
                                                $status = "Late";
                                            }
                                        
                                            echo "<tr>
                                                    <td>{$date}</td>
                                                    <td>{$formatted_time_in}</td>
                                                    <td>{$formatted_time_out}</td>
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
                            <div class="overflow-x-auto custom-border">
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
                                         $query = "SELECT start_date, end_date, leave_type, reason 
                                         FROM leave_requests 
                                         WHERE employee_id = ? AND status = 'Approved' 
                                         ORDER BY start_date DESC 
                                         LIMIT 3";                               
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


                    </div>
                </div>

                <div class="col-12 col-lg-4 pt-3 pt-md-0">
                    <?php include 'database.php'; ?>

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
