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
    <title>Reset Password</title>
</head>
<body>
    <?php echo $display; ?>
    <?php if (isset($resetRequest)): ?>
        <form action="" method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php endif; ?>
</body>
</html>
