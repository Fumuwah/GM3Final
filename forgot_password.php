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
        $display = "<div class='alert alert-danger'>No account found with that Employee Number.</div>";
    } else {
        $email = $user['email'];
        $token = bin2hex(random_bytes(50));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Insert the reset token into the database
        $insertToken = "INSERT INTO password_reset (employee_number, token, expires_at) VALUES (?, ?, ?)";
        $insertTokenPrep = mysqli_prepare($conn, $insertToken);
        mysqli_stmt_bind_param($insertTokenPrep, 'sss', $employeenums, $token, $expires_at);
        mysqli_stmt_execute($insertTokenPrep);

        if (mysqli_stmt_affected_rows($insertTokenPrep) > 0) {
            // Send reset email
            $resetLink = "https://gm3builders.com/reset_password.php?token=$token";
            $message = "Hello, " . $user['firstname'] . " " . $user['lastname'] . ",\n\n";
            $message .= "We received a request to reset your password. Please click the link below to reset your password:\n";
            $message .= "$resetLink\n\n";
            $message .= "This link will expire in 1 hour.\n\n";
            $message .= "If you did not request a password reset, please ignore this email.";

            if (smtp_mailer($email, "Password Reset Request", $message)) {
                $display = "<div class='alert alert-success'>A password reset link has been sent to your email.</div>";
            } else {
                $display = "<div class='alert alert-danger'>Failed to send reset email. Please try again later.</div>";
            }
        } else {
            $display = "<div class='alert alert-danger'>Failed to process your request. Please try again later.</div>";
        }
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