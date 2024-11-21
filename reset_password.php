<?php
require_once "database.php";

$display = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify token
    $verifyToken = "SELECT * FROM password_reset WHERE token = ? AND expires_at > NOW()";
    $verifyTokenPrep = mysqli_prepare($conn, $verifyToken);
    mysqli_stmt_bind_param($verifyTokenPrep, 's', $token);
    mysqli_stmt_execute($verifyTokenPrep);
    $result = mysqli_stmt_get_result($verifyTokenPrep);
    $resetRequest = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($resetRequest) {
        if (isset($_POST['reset_password'])) {
            $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            // Update password in employees table
            $updatePassword = "UPDATE employees SET password = ? WHERE employee_number = ?";
            $updatePasswordPrep = mysqli_prepare($conn, $updatePassword);
            mysqli_stmt_bind_param($updatePasswordPrep, 'ss', $newPassword, $resetRequest['employee_number']);
            mysqli_stmt_execute($updatePasswordPrep);

            if (mysqli_stmt_affected_rows($updatePasswordPrep) > 0) {
                // Delete the token
                $deleteToken = "DELETE FROM password_reset WHERE token = ?";
                $deleteTokenPrep = mysqli_prepare($conn, $deleteToken);
                mysqli_stmt_bind_param($deleteTokenPrep, 's', $token);
                mysqli_stmt_execute($deleteTokenPrep);

                $display = "<div class='alert alert-success'>Password has been reset successfully.</div>";
            } else {
                $display = "<div class='alert alert-danger'>Failed to reset the password. Please try again.</div>";
            }
        }
    } else {
        $display = "<div class='alert alert-danger'>Invalid or expired token.</div>";
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

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
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
            <?php echo $display; ?>
            <?php if (isset($resetRequest)): ?>
                <form action="" method="POST" class="mt-4">
                    <label for="new_password">New Password:</label>
                    <input type="password" name="new_password" required>
                    <img src="./assets/images/eye.png" style="position:absolute; width:21px; cursor:pointer; right:10px; top:9px;" id="eye">
                    <button type="submit" name="reset_password">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let eye = document.querySelector('#eye');
        let password = document.querySelector('#password');
        let seePassword = false;
        eye.addEventListener('click', function() {
            seePassword = !seePassword;
            if (seePassword) {
                password.type = 'text';
            } else {
                password.type = 'password';
            }

        });
    </script>
</body>
</html>
