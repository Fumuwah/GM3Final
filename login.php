<?php
session_start();
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
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body{
            height: 100vh;
        }
        .overlay{
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgb(52,219,206);
background: linear-gradient(90deg, rgba(52,219,206,0.5690651260504201) 0%, rgba(47,82,207,1) 100%);
        }
        #wrong-text{
            display: none;
        }
        
        @media screen and (max-width: 768px){
            .h-100{
                height: 200px !important;
            }
            .align-items-center{
                align-items: flex-start !important;
            }
            .pl-5{
                padding-left:30px !important;
            }
            body{
                height: auto;
            }
            .login-container{
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
    <!-- Just an image -->
    <div class="login-bg h-100 col-12 col-md-6 position-relative" style="background: url(assets/images/login-bg-low.jpg) no-repeat center center; background-size: cover;">
        <div class="overlay"></div>
    </div>
    <div class="login-container col-12 col-md-6 d-flex align-items-center pl-5">
        <div>
            <div class="d-flex align-items-center">
                <img src="assets/images/gm3-logo-small.png" alt="" style="width:50px;">
                <h2 class="ml-2">Login</h2>
            </div>
          
            <?php
            require_once "database.php"; // Ensure your database connection is set up
            
            if (isset($_POST["login"])) {
                $email = $_POST["email"];
                $pass = $_POST["password"];
            
                // Fetch the employee's information based on the email
                $sql = "SELECT * FROM employees WHERE email = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 's', $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
                if ($user) {
                    // Check if employee_status is 'Archived'
                    if ($user['employee_status'] === 'Archived') {
                        echo "<div class='alert alert-danger'>Your account has been archived and cannot access the website.</div>";
                    } else {
                        // Verify the password
                        if (password_verify($pass, $user["password"])) {
                            $_SESSION['employee_id'] = $user['employee_id'];
            
                            // Get the employee's role and permissions using role_id
                            $role_id = $user['role_id']; // Assuming this is in your employees table
            
                            // Fetch role name
                            $role_query = "SELECT role_name FROM roles WHERE role_id = ?";
                            $role_stmt = mysqli_prepare($conn, $role_query);
                            mysqli_stmt_bind_param($role_stmt, 'i', $role_id);
                            mysqli_stmt_execute($role_stmt);
                            $role_result = mysqli_stmt_get_result($role_stmt);
                            $role_data = mysqli_fetch_assoc($role_result);
                            $_SESSION['role_name'] = $role_data['role_name'];
            
                            // Fetch permissions associated with the role
                            $permission_query = "SELECT * FROM permissions WHERE role_id = ?";
                            $permission_stmt = mysqli_prepare($conn, $permission_query);
                            mysqli_stmt_bind_param($permission_stmt, 'i', $role_id);
                            mysqli_stmt_execute($permission_stmt);
                            $permission_result = mysqli_stmt_get_result($permission_stmt);
                            $permissions = mysqli_fetch_assoc($permission_result);
            
                            // Store permissions in session variables
                            $_SESSION['can_view_own_data'] = $permissions['can_view_own_data'];
                            $_SESSION['can_view_team_data'] = $permissions['can_view_team_data'];
                            $_SESSION['can_edit_data'] = $permissions['can_edit_data'];
                            $_SESSION['can_manage_roles'] = $permissions['can_manage_roles'];
            
                            // Redirect based on role or permissions
                            if ($_SESSION['role_name'] === 'super  admin' || $_SESSION['can_manage_roles']) {
                                header("Location: index.php");
                                exit();
                            } elseif ($_SESSION['role_name'] === 'admin' || $_SESSION['can_view_team_data']) {
                                header("Location: index.php");
                                exit();
                            } elseif ($_SESSION['role_name'] === 'employee' || $_SESSION['can_view_own_data']) {
                                header("Location: employee_dashboard.php");
                                exit();
                            } else {
                                echo "<div class='alert alert-danger'>Unauthorized access!</div>";
                            }
                            exit;
                        } else {
                            echo "<div class='alert alert-danger'>Password does not match!</div>";
                        }
                    }
                } else {
                    echo "<div class='alert alert-danger'>Email does not exist!</div>";
                }
            }
            ?>
            <form action="login.php" class="mt-4" method="post">
                <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" class="form-control" name="email" aria-describedby="emailHelp" placeholder="Enter email">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                  </div>
                  <div class="form-group mb-0">
                    <label for="exampleInputPassword1">Password</label>
                    <div class="position-relative">
                      <input type="password" class="form-control" name="password" placeholder="Password" id="password">
                      <img src="./assets/images/eye.png" style="position:absolute; width:21px; cursor:pointer; right:10px; top:9px;" id="eye">
                    </div>
                
                  </div>
                  <div class="">
                    <p class="py-2 m-0">Have you <a href="/forgot_password.php">forgot your password</a>?</p>
                </div>
                  <div class="form-btn">
                  <button type="submit" value="Login" name="login" id="login-btn" class="btn btn-primary">Login</button>
                  </div>
                  <div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let eye = document.querySelector('#eye');
        let password = document.querySelector('#password');
        let seePassword = false;
        eye.addEventListener('click',function(){
            seePassword = !seePassword;
            if(seePassword){
                password.type = 'text';
            }else{
                password.type = 'password';
            }
           
        });
    </script>
</body>
</html>
