<?php
session_start();
include 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$activePage = 'my_leaves';

$employee_id = $_SESSION['employee_id'];
$role_name = strtolower(trim($_SESSION['role_name']));

$query = "SELECT e.employee_number, e.firstname, e.lastname, p.position_name, 
                pr.project_name, e.last_leave_reset_year 
        FROM employees e
        LEFT JOIN projects pr ON e.project_name = pr.project_name
        LEFT JOIN positions p ON e.position_id = p.position_id
        WHERE e.employee_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();

if (!$employee) {
    echo "Error: Employee not found.";
    exit();
}

$leave_query = "SELECT sick_leave, vacation_leave, leave_without_pay, used_leave 
                FROM leaves WHERE employee_id = ?";
$leave_stmt = $conn->prepare($leave_query);
$leave_stmt->bind_param("i", $employee_id);
$leave_stmt->execute();
$leave_balances = $leave_stmt->get_result()->fetch_assoc();

$sick_leave = $leave_balances['sick_leave'] ?? 0;
$vacation_leave = $leave_balances['vacation_leave'] ?? 0;
$leave_without_pay = $leave_balances['leave_without_pay'] ?? 0;
$used_leave = $leave_balances['used_leave'] ?? 0;

$last_reset_year = $employee['last_leave_reset_year'] ?? null;
$current_year = date('Y');

$superAdminAndAdminLeaves = 7;
$employeeLeaves = 6;

if ($last_reset_year === null || $last_reset_year < $current_year) {
    if ($role_name === 'super admin' || $role_name === 'admin') {
        $sick_leave = $superAdminAndAdminLeaves;
        $vacation_leave = $superAdminAndAdminLeaves;
    } else {
        $sick_leave = $employeeLeaves;
        $vacation_leave = $employeeLeaves;
    }

    $reset_leave_query = "
        UPDATE leaves 
        SET sick_leave = ?, vacation_leave = ?, leave_without_pay = 0, used_leave = 0 
        WHERE employee_id = ?";
    $reset_leave_stmt = $conn->prepare($reset_leave_query);
    $reset_leave_stmt->bind_param("iii", $sick_leave, $vacation_leave, $employee_id);
    $reset_leave_stmt->execute();

    $update_reset_year_query = "
        UPDATE employees 
        SET last_leave_reset_year = ? 
        WHERE employee_id = ?";
    $update_reset_year_stmt = $conn->prepare($update_reset_year_query);
    $update_reset_year_stmt->bind_param("ii", $current_year, $employee_id);
    $update_reset_year_stmt->execute();
}

$employee_name = $employee['firstname'] . ' ' . $employee['lastname'];
$employee_number = $employee['employee_number'];
$position = $employee['position_name'];
$project_name = $employee['project_name'];

?>

<?php include 'layout/header.php'; ?>

<div class="d-flex">
    <?php include 'layout/sidebar.php'; ?>
    <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2>Leave Credits</h2>
                </div>
                <div class="col-12 mt-2">
                    <div class="d-flex align-items-center">
                        <h6 class="m-0 col-4">Employee Name:</h6>
                        <p class="m-0 ml-3"><?= htmlspecialchars($employee_name) ?></p>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <h6 class="m-0 col-4">Employee No:</h6>
                        <p class="m-0 ml-3"><?= htmlspecialchars($employee_number) ?></p>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <h6 class="m-0 col-4">Position:</h6>
                        <p class="m-0 ml-3"><?= htmlspecialchars($position) ?></p>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <hr>
                    <h3>Current Leave</h3>
                    <div class="d-flex align-items-center">
                        <h6 class="m-0 col-4">Sick Leave:</h6>
                        <p class="m-0 ml-3"><?= $sick_leave ?></p>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <h6 class="m-0 col-4">Vacation Leave:</h6>
                        <p class="m-0 ml-3"><?= $vacation_leave ?></p>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <h6 class="m-0 col-4">Leave Without Pay:</h6>
                        <p class="m-0 ml-3"><?= $leave_without_pay ?></p>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <h6 class="m-0 col-4">Used Leave:</h6>
                        <p class="m-0 ml-3"><?= $used_leave + $leave_without_pay ?></p>
                    </div>
                </div>
                <div class="text-right col-12 mt-3">
                    <button class="btn btn-success" id="request-leave-btn">Request Leave</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="request-leave-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="leave-form" action="submit_leave.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Leave Form</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row form-group">
                        <div class="col-4">
                            <label for="leave-type">Type of Leave</label>
                            <select name="leave_type" id="leave-type" class="form-control" required>
                                <option value="">Select Type of Leave</option>
                                <option value="Sick Leave">Sick Leave</option>
                                <option value="Vacation Leave">Vacation Leave</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="start_date">From Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label for="end_date">To Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label for="reason">Description</label>
                            <textarea name="reason" id="reason" class="form-control" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger close-modalwForm">Close</button>
                    <button type="submit" class="btn btn-success">Submit Leave Form</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#collapse1').on('click', function() {
            $('#collapsetest1').slideToggle()
        })
        $('#collapse2').on('click', function() {
            $('#collapsetest2').slideToggle()
        })
    });

    document.getElementById('leave-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        fetch('submit_leave.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        alert('Leave request submitted successfully.');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Invalid JSON:', error, text);
                    alert('An unexpected error occurred. Please check the console for more details.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Failed to submit the form. Please try again.');
            });
    });


    document.querySelectorAll('.close-modalwForm').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('request-leave-modal').classList.add('fade');
            setTimeout(() => {
                document.getElementById('request-leave-modal').style.display = 'none';
            }, 400);
        });
    });

    var arrow = document.querySelector('#arrow');
    const width = window.innerWidth || document.documentElement.clientWidth ||
        document.body.clientWidth;
    console.log(width)
    if (width < 768) {
        var show = false;
    } else {
        var show = true;
    }

    arrow.addEventListener('click', function() {
        var sidebarContainer = document.querySelector('.sidebar-container');

        if (show) {
            sidebarContainer.style.marginLeft = '-250px';
            arrow.style.transform = 'translate(-50%,-50%) rotate(180deg)';
            show = false;
        } else {
            sidebarContainer.style.marginLeft = '0px';
            arrow.style.transform = 'translate(-50%,-50%) rotate(0deg)';
            show = true;
        }

    });

    var requestLeaveBtn = document.querySelector('#request-leave-btn');
    var requestLeaveModal = document.querySelector('#request-leave-modal');
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
<?php include './layout/footer.php'; ?>