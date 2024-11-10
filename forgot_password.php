<?php
session_start();

require_once "database.php";
require_once './common/mailer.php';

$display = "";

if (isset($_POST['forgot_password'])) {
    $employeenums = isset($_POST['employeenumber']) ? $_POST['employeenumber'] : '';


    $getdata = "SELECT * FROM employees WHERE employee_number = ?";
    $getdataprep = mysqli_prepare($conn, $getdata);
    mysqli_stmt_bind_param($getdataprep, 's', $employeenums);
    mysqli_stmt_execute($getdataprep);
    $getdataresult = mysqli_stmt_get_result($getdataprep);
    $user = mysqli_fetch_array($getdataresult, MYSQLI_ASSOC);

    if (!$user) {
        $display = "<div class='alert alert-danger'>Your account has been archived and cannot access the website.</div>";
    } else {
        if ($user['role_id'] == 3) {
            send_email_to_admin($user['project_name'], $user);
            $display = "<div class='alert alert-danger'>Change Password Request has been sent to Project Admin.</div>";
        }
        if ($user['role_id'] == 2) {
            send_email_to_masteradmin($user);
            $display = "<div class='alert alert-danger'>Change Password Request has been sent to Master Admin.</div>";
        }
        if ($user['role_id'] == 1) {
            $display = "<div class='alert alert-danger'>Contact Tech Support.</div>";
        }
    }
}

function send_email_to_admin($project_name, $requester)
{
    $admin_id = 2;
    $admindata = "SELECT * FROM employees WHERE role_id = ? AND project_name = ?";
    $admindataprep = mysqli_prepare($GLOBALS['xconn'], $admindata);
    mysqli_stmt_bind_param($admindataprep, 'ss', $admin_id, $project_name);
    mysqli_stmt_execute($admindataprep);
    $admindataresult = mysqli_stmt_get_result($admindataprep);
    $admins = mysqli_fetch_all($admindataresult, MYSQLI_ASSOC);


    $message = $requester['employee_number'] . ": " . $requester['lastname'] . " " . $requester['firstname'] . " Forgot a password.";

    foreach ($admins as $admin) {
        smtp_mailer($admin['email'], "Forgot Password Request", $message);
    }
}

function send_email_to_masteradmin($requester)
{
    $masteradmin_id = 1;
    $masteradmindata = "SELECT * FROM employees WHERE role_id = ?";
    $masteradmindataprep = mysqli_prepare($GLOBALS['xconn'], $masteradmindata);
    mysqli_stmt_bind_param($masteradmindataprep, 's', $masteradmin_id);
    mysqli_stmt_execute($masteradmindataprep);
    $masteradmindataresult = mysqli_stmt_get_result($masteradmindataprep);
    $masteradmins = mysqli_fetch_all($masteradmindataresult, MYSQLI_ASSOC);

    $message = $requester['employee_number'] . ": " . $requester['lastname'] . " " . $requester['firstname'] . " Forgot a password.";

    foreach ($masteradmins as $masteradmin) {
        smtp_mailer($masteradmin['email'], "Forgot Password Request", $message);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GM3 Builders Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            height: 100vh;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgb(52, 219, 206);
            background: linear-gradient(90deg, rgba(52, 219, 206, 0.5690651260504201) 0%, rgba(47, 82, 207, 1) 100%);
        }

        #wrong-text {
            display: none;
        }

        @media screen and (max-width: 768px) {
            .h-100 {
                height: 200px !important;
            }

            .align-items-center {
                align-items: flex-start !important;
            }

            .pl-5 {
                padding-left: 30px !important;
            }

            body {
                height: auto;
            }

            .login-container {
                margin-top: 50px;
            }
        }
    </style>
</head>

<body class="d-flex flex-wrap">
    <div class="login-bg h-100 col-12 col-md-6 position-relative" style="background: url(assets/images/login-bg-low.jpg) no-repeat center center; background-size: cover;">
        <div class="overlay"></div>
    </div>
    <div class="login-container col-12 col-md-6 d-flex align-items-center pl-5">
        <div>
            <div class="d-flex align-items-center">
                <img src="assets/images/gm3-logo-small.png" alt="" style="width:50px;">
                <h2 class="ml-2">Forgot Password</h2>
            </div>

            <?php
            echo $display
            ?>

            <form action="forgot_password.php" class="mt-4" method="post">
                <div class="form-group">
                    <label for="exampleInputEmail1">Employee Number</label>
                    <input type="text" class="form-control" name="employeenumber" placeholder="Enter Employee Number" required>
                </div>
                <div class="form-btn">
                    <button type="submit" value="forgot_password" name="forgot_password" id="login-btn" class="btn btn-primary mt-3">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>