<?php
session_start();
require 'database.php';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get current employee data from the database
    $query = "
        SELECT password, contactno, address 
        FROM employees 
        WHERE employee_id = ?
    ";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();

    if (!$employee) {
        die("Error: Employee data not found.");
    }

    // Get new input values
    $newPassword = $_POST['password'];
    $newContactNo = $_POST['contactno'];
    $newAddress = $_POST['address'];

    // Track updates
    $updateFields = [];
    $updateValues = [];
    $passwordChanged = false;

    // Check for password update (and hash it securely)
    if (!empty($newPassword)) {
        if (!password_verify($newPassword, $employee['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateFields[] = "password = ?";
            $updateValues[] = $hashedPassword;
            $passwordChanged = true;
        }
    }

    // Check for contact number change
    if (!empty($newContactNo) && $newContactNo != $employee['contactno']) {
        $updateFields[] = "contactno = ?";
        $updateValues[] = $newContactNo;
    }

    // Check for address change
    if (!empty($newAddress) && $newAddress != $employee['address']) {
        $updateFields[] = "address = ?";
        $updateValues[] = $newAddress;
    }

    // Update database if there are changes
    if (!empty($updateFields)) {
        $query = "UPDATE employees SET " . implode(", ", $updateFields) . " WHERE employee_id = ?";
        $updateValues[] = $employee_id; // Add employee_id for WHERE clause

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing update statement: " . $conn->error);
        }

        // Dynamically bind parameters
        $types = str_repeat("s", count($updateValues) - 1) . "i"; // 's' for strings, 'i' for the final employee_id
        $stmt->bind_param($types, ...$updateValues);

        if ($stmt->execute()) {
            if ($passwordChanged) {
                echo "Password updated successfully! You will now be logged out for security reasons.";
                // Log out the user
                session_unset();
                session_destroy();
                header("Location: login.php");
                exit();
            } else {
                echo "Profile updated successfully!";
            }
        } else {
            echo "Error updating profile: " . $stmt->error;
        }
    } else {
        echo "No changes detected in the profile.";
    }

    header("Location: employee-profile.php");
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
        e.address, e.birthdate, e.civil_status, e.basic_salary, e.hire_date, e.employee_status,
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
    <div class="main pt-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid pl-5">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div>
                        <?php
                        $imagePath = isset($_GET['image']) ? $_GET['image'] : 'default.jpg';
                        ?>

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
        <button class="btn accordion-btn" data-accordion="more-details" type="button">
            More Details
        </button>
    
        <!-- Information Update Accordion -->
        <div class="card accordion" id="information-update">
            <div class="card-body">
                <h2>Edit Information</h2>
                <form action="" method="POST">
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
                    <input class="form-control" 
                        type="text" 
                        name="contactno" 
                        placeholder="New Contact Number" 
                        value="<?php echo $employee['contactno']; ?>" 
                        pattern="^\d{11}$" 
                        maxlength="11" 
                        title="Please enter exactly 11 digits." 
                        inputmode="numeric" 
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                        required>
                </div>

                        <div class="col-6">
                            <label for="">Address</label>
                            <textarea class="form-control" name="address" placeholder="New Address"><?php echo $employee['address']; ?></textarea>
                        </div>
                    </div>
                    <div class="text-right d-flex align-items-center justify-content-end">
                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="#" class="btn btn-danger ml-3">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- More Details Accordion (default open) -->
        <div class="card accordion" id="more-details" style="display: block;">
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

<script>
    // JavaScript to handle the accordion toggle
    document.addEventListener('DOMContentLoaded', function () {
        var buttons = document.querySelectorAll('.accordion-btn');
        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var target = document.getElementById(button.getAttribute('data-accordion'));
                var allAccordions = document.querySelectorAll('.accordion');
                allAccordions.forEach(function (accordion) {
                    if (accordion !== target) {
                        accordion.style.display = 'none';
                    }
                });

                // Toggle the display of the selected accordion
                if (target.style.display === 'none' || target.style.display === '') {
                    target.style.display = 'block';
                } else {
                    target.style.display = 'none';
                }
            });
        });

        // Show the "More Details" accordion by default when the page loads
        document.getElementById('more-details').style.display = 'block';
    });
</script>

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

<script>
    var resigBtn = document.getElementById('rform');
    resigBtn.addEventListener('click', function() {
        window.open("assets/files/resignation.pdf", '_blank').focus();
    });
</script>
<?php include './layout/script.php'; ?>
<?php
include './layout/footer.php';
?>