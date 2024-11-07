<?php

session_start();
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
  }
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $requestId = $_POST['request_id'];
    $action = $_POST['action'];
    
    $stmt = $conn->prepare("SELECT employee_id, request_type, new_data FROM profile_change_requests WHERE request_id = ?");
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    
    if ($action === 'approve') {
        $column = $request['request_type'];
        $stmt = $conn->prepare("UPDATE employees SET $column = ? WHERE employee_id = ?");
        $stmt->bind_param("si", $request['new_data'], $request['employee_id']);
        $stmt->execute();
    }
    
    $stmt = $conn->prepare("UPDATE profile_change_requests SET status = ? WHERE request_id = ?");
    $stmt->bind_param("si", $action, $requestId);
    $stmt->execute();
}

function createNotification($employee_id, $message, $request_type, $request_id, $db) {
    $stmt = $db->prepare("INSERT INTO notifications (employee_id, message, request_type, request_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $employee_id, $message, $request_type, $request_id);
    $stmt->execute();
    $stmt->close();
}

function notifyProfileChangeRequest($profile_change_request_id, $db) {
    $message = "An employee has requested a profile change.";

    $stmt = $db->prepare("SELECT employee_id FROM employees 
                          JOIN roles ON employees.role_id = roles.role_id 
                          WHERE roles.role_name = 'Super Admin'");
    $stmt->execute();
    $stmt->bind_result($super_admin_id);

    while ($stmt->fetch()) {
        createNotification($super_admin_id, $message, 'profile_change', $profile_change_request_id, $db);
    }
    $stmt->close();
}

$role_name = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];
include './layout/header.php';
?>
      <div class="d-flex">
        <?php include './layout/sidebar.php'; ?>
          <div class="main-content">
            <div class="container-fluid">
                <h2>Employees</h2>
                <div class="d-flex justify-content-between align-items-center">
                    <form class="form-inline my-3 col-10 pl-0">
                        <div class="form-group col-8 col-lg-3">
                          <label for="search-user" class="sr-only">Search User</label>
                          <input type="text" class="form-control w-100" id="search-user" placeholder="Search User">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                      </form>
                </div>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">Employee #</th>
                                <th scope="col">Name</th>
                                <th scope="col">Change Request Type</th>
                                <th scope="col">Old Data</th>
                                <th scope="col">New Data</th>
                                <th scope="col">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                            $query = "SELECT r.request_id, r.employee_id, e.firstname, e.lastname, r.request_type, r.old_data, r.new_data FROM profile_change_requests r JOIN employees e ON r.employee_id = e.employee_id WHERE r.status = 'pending'";
                            $requests = $conn->query($query);
                            
                            while ($row = $requests->fetch_assoc()) {
                            ?>
                              <tr>
                                <td><?php echo $row['employee_id']; ?></td>
                                <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>
                                <td><?php echo ucfirst($row['request_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['old_data']); ?></td>
                                <td><?php echo htmlspecialchars($row['new_data']); ?></td>
                                <td>
                                  <form method="POST">
                                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success mb-2">Approve</button>
                                    <button type="submit" name="action" value="decline" class="btn btn-danger mb-2">Decline</button>
                                  </form>
                                </td>
                              </tr>
                            <?php } ?>
                            </tbody>
                          </table>
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
      var arrow = document.querySelector('#arrow');
            const width  = window.innerWidth || document.documentElement.clientWidth || 
            document.body.clientWidth;
            console.log(width)
            if(width < 768){
                var show = false;
            }else{
                var show = true;
            }

            arrow.addEventListener('click',function(){
                var sidebarContainer = document.querySelector('.sidebar-container');
            
                if(show){
                    sidebarContainer.style.marginLeft = '-250px';
                    arrow.style.transform = 'translate(-50%,-50%) rotate(180deg)';
                    show = false;
                }else{
                    sidebarContainer.style.marginLeft = '0px';
                    arrow.style.transform = 'translate(-50%,-50%) rotate(0deg)';
                    show = true;
                }
            
            });

      </script>
      
<?php 
    include './layout/footer.php';
?>