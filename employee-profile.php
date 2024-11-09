<?php
session_start();
require 'database.php';
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'];
    $newContactNo = $_POST['contactno'];
    $newAddress = $_POST['address'];
    $updateQueries = [];
    $fields = ['password' => $newPassword, 'contactno' => $newContactNo, 'address' => $newAddress];

    foreach ($fields as $type => $newData) {
        if (!empty($newData) && $newData != $employee[$type]) {
            $stmt = $conn->prepare("INSERT INTO profile_change_requests (employee_id, request_type, old_data, new_data) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $employee_id, $type, $employee[$type], $newData);
            $stmt->execute();
        }
    }

    header("Location: profile-change-requests.php");
    exit();
}

$imagePath = '';

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['file']['name']);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
        $imagePath = $targetFilePath;
        echo "Image uploaded successfully!";
    } else {
        echo "Failed to upload the image.";
    }
}
// else {
//     echo "No file uploaded or there was an upload error.";
// }


$activePage = "employee_profile";
$role_name = $_SESSION['role_name'];
$employee_id = $_SESSION['employee_id'];
include './layout/header.php';

$query = "
    SELECT e.employee_number, e.firstname, e.middlename, e.lastname, p.position_name, e.email, e.contactno, 
        e.address, e.birthdate, e.civil_status, e.religion, e.basic_salary, e.hire_date, e.employee_status,
        e.sss_no, e.philhealth_no, e.hdmf_no, e.tin_no, e.emergency_contactno, e.image_path
    FROM employees e
    LEFT JOIN positions p ON e.position_id = p.position_id
    WHERE e.employee_id = ?
";



$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error); // Debug output for prepare error
}

$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

$imagePath = $employee['image_path'] != null ? $employee['image_path'] : './assets/images/account.png';

$hire_date = date("m/d/Y", strtotime($employee['hire_date']));
$birthdate = date("F d, Y", strtotime($employee['birthdate']));
$age = date_diff(date_create($employee['birthdate']), date_create('today'))->y;
?>

<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-body d-flex align-items-center">
                            <div>
                                

                        <form action="submit_employee_image.php" enctype="multipart/form-data" id="form" method="POST">
                            <img src="<?php echo $imagePath; ?>" style="width:150px; height:150px; border-radius: 50%; object-fit: cover;">
                            <input type="file" name="file" accept="image/*" style="width: 0px; height:0px; opacity:0;" id="file">
                            <a href="#" class="btn btn-primary d-block" id="add-picture" style="margin-top:10px;">Add</a>
                        </form>

                    </div>
                    <div class="pl-3">
                        <h5 class="card-title m-0"><?php echo $employee['firstname'] . ' ' . $employee['lastname']; ?></h5>
                        <p class="card-text"><?php echo $employee['position_name']; ?></p>
                        <p class="card-text m-0"><?php echo $employee['employee_number']; ?></p>
                        <p class="card-text m-0"><?php echo $employee['contactno']; ?></p>
                        <p class="card-text m-0"><?php echo $employee['email']; ?></p>
                        <p class="card-text m-0 position-absolute text-primary" style="right:10px;bottom:5px;">Hired Date: <?php echo $hire_date; ?></p>
                        <p class="card-text m-0 position-absolute text-success" style="right:10px;top:5px;"><?php echo $employee['employee_status']; ?></p>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button class="btn accordion-btn" data-accordion="information-update" type="button">
                        Information Update
                    </button>
                    <button class="btn accordion-btn" data-accordion="resignation-form" type="button">
                        Resignation Form
                    </button>
                    <button class="btn accordion-btn" data-accordion="more-details" type="button">
                        More Details
                    </button>
                    <div class="card accordion" id="resignation-form">
                        <div class="card-body">
                            <h2>Leaves</h2>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>SMD Building</td>
                                            <td>On-Going</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card accordion" id="information-update">
                        <div class="card-body">
                            <h2>Edit Information</h2>
                            <form action="profile-change-request.php" method="POST">
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <label for="">Password</label>
                                        <input class="form-control" type="password" name="password" placeholder="New Password">
                                    </div>
                                    <div class="col-6">
                                        <label for="">Confirm Password</label>
                                        <input class="form-control" type="password" name="confirm_password" placeholder="Confirm Password">
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <div class="col-6">
                                        <label for="">Contact No.</label>
                                        <input class="form-control" type="text" name="contactno" placeholder="New Contact Number" value="<?php echo $employee['contactno']; ?>">
                                    </div>
                                    <div class="col-6">
                                        <label for="">Address</label>
                                        <textarea class="form-control" name="address" placeholder="New Address"><?php echo $employee['address']; ?></textarea>
                                    </div>
                                </div>
                                <div class="text-right d-flex align-items-center justify-content-end">
                                    <button type="submit" class="btn btn-success">Submit Request</button>
                                    <a href="#" class="btn btn-danger ml-3">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card accordion" id="more-details">
                        <div class="card-body">
                            <h2>More Details</h2>
                            <hr class="mb-4">
                            <div class="d-flex flex-wrap align-items-center">
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Address:</h5>
                                    <p><?php echo $employee['address']; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Birthdate:</h5>
                                    <p><?php echo $birthdate; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Age:</h5>
                                    <p><?php echo $age; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Civil Status:</h5>
                                    <p><?php echo $employee['civil_status']; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Religion:</h5>
                                    <p><?php echo $employee['religion']; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Basic Salary:</h5>
                                    <p>Php <?php echo number_format($employee['basic_salary'], 2); ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>SSS Number:</h5>
                                    <p><?php echo $employee['sss_no']; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Philhealth Number:</h5>
                                    <p><?php echo $employee['philhealth_no']; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>HDMF Number:</h5>
                                    <p><?php echo $employee['hdmf_no']; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>TIN Number:</h5>
                                    <p><?php echo $employee['tin_no']; ?></p>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                    <h5>Emergency Contact:</h5>
                                    <p><?php echo $employee['emergency_contactno']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let addPicture = document.querySelector('#add-picture');
            let inputTypeFile = document.querySelector('#file');
            addPicture.addEventListener('click', function(e) {
                e.preventDefault();
                inputTypeFile.click();
            });
            inputTypeFile.addEventListener('change', function(e) {
                let form = document.querySelector('#form');
                form.submit();
                console.log(this.files[0].name);
            });
        </script>
    </div>
</div>
<?php
include './layout/footer.php';
?>