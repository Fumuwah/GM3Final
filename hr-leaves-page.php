<?php

session_start();

include 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    echo "Session is not set. Redirecting to login...";
    header("Location: login.php");
    exit();
}

$role_name = strtolower($_SESSION['role_name']);

function createNotification($employee_id, $message, $request_type, $request_id, $db)
{
    $stmt = $db->prepare("INSERT INTO notifications (employee_id, message, request_type, request_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $employee_id, $message, $request_type, $request_id);
    $stmt->execute();
    $stmt->close();
}

function notifyLeaveRequest($leave_request_id, $employee_id, $project_name, $db)
{
    $message = "An employee has requested leave approval.";

    $stmt = $db->prepare("SELECT employee_id FROM employees 
                          JOIN roles ON employees.role_id = roles.role_id 
                          WHERE (roles.role_name = 'Admin' OR roles.role_name = 'Super Admin') 
                          AND employees.project_name = ?");
    $stmt->bind_param("s", $project_name);
    $stmt->execute();
    $stmt->bind_result($admin_id);

    while ($stmt->fetch()) {
        createNotification($admin_id, $message, 'leave_request', $leave_request_id, $db);
    }
    $stmt->close();
}

$query = "
    SELECT lr.request_id, e.firstname, e.lastname, lr.leave_type, lr.start_date, lr.end_date, lr.reason,
    (COALESCE(l.sick_leave, 0) + COALESCE(l.vacation_leave, 0)) AS total_leaves,
    lr.status
    FROM leave_requests lr
    JOIN employees e ON lr.employee_id = e.employee_id
    LEFT JOIN leaves l ON e.employee_id = l.employee_id
";
$activePage = 'hr-leaves-page';
$requests = $conn->query($query);

include './layout/header.php';
?>
<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main pt-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container">
            <h2>Leaves</h2>
            <div class="d-flex justify-content-between align-items-center">
                <form class="form-inline my-3 col-10 pl-0">
                    <div class="form-group mb-2 col-8 col-lg-6">
                        <label for="type-of-leaves" class="sr-only">Type of Leaves</label>
                        <select name="" id="" class="form-control w-100" id="type-of-leaves">
                            <option value="">Type of Leave</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Leave Type</th>
                            <th scope="col">Description</th>
                            <th scope="col">No. Of Leaves</th>
                            <th scope="col">From Date</th>
                            <th scope="col">To Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $requests->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['request_id'] ?></td>
                                <td><?= $row['firstname'] . ' ' . $row['lastname'] ?></td>
                                <td><?= $row['leave_type'] ?></td>
                                <td><?= $row['reason'] ?></td>
                                <td><?= $row['total_leaves'] ?></td>
                                <td><?= $row['start_date'] ?></td>
                                <td><?= $row['end_date'] ?></td>
                                <td>
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <a href="approve_leave.php?request_id=<?= $row['request_id'] ?>" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this leave request?');">Approve</a>
                                        <a href="decline_leave.php?request_id=<?= $row['request_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to decline this leave request?');">Decline</a>
                                    <?php elseif ($row['status'] === 'Approved'): ?>
                                        <span class="badge badge-success">Approved</span>
                                    <?php elseif ($row['status'] === 'Declined'): ?>
                                        <span class="badge badge-danger">Declined</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script>
    var deleteBtn = document.querySelectorAll('.delete-btn');
    var deleteModal = document.querySelector('#delete-modal');
    var closeModal = document.querySelectorAll('.close-modal');

    closeModal.forEach((d) => {
        d.addEventListener('click', function(i) {
            var modalParent = d.parentNode.parentNode.parentNode.parentNode;
            deleteModal.classList.add('fade');
            setTimeout(function() {
                deleteModal.style.display = 'none';
            }, 400)
        });
    });

    deleteBtn.forEach((d) => {
        d.addEventListener('click', function() {
            deleteModal.classList.remove('fade');
            deleteModal.style.display = 'block';
        });
    })
</script>
<?php include './layout/footer.php'; ?>