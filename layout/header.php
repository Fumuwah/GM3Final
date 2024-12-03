<?php
require './database.php';

// Fetch notifications for the dropdown
$notification_query = "SELECT * FROM notification ORDER BY timestamp DESC";
$notification_prep = mysqli_prepare($conn, $notification_query);
mysqli_stmt_execute($notification_prep);
$resultnotification = mysqli_stmt_get_result($notification_prep);
$notifs = mysqli_fetch_all($resultnotification, MYSQLI_ASSOC);

// Count unread notifications
$unread_query = "SELECT COUNT(*) AS unread_count FROM notification WHERE is_read = 0";
$result_unread = mysqli_query($conn, $unread_query);

if ($result_unread) {
    $unread_count = mysqli_fetch_assoc($result_unread)['unread_count'];
} else {
    $unread_count = 0;
}

$employee_id = $_SESSION['employee_id'] ?? null;

if (!$employee_id) {
    die('User is not logged in.');
}

$query = "SELECT r.role_name, 
                p.can_view_own_data, p.can_view_team_data, 
                p.can_edit_data, p.can_manage_roles 
        FROM employees e 
        JOIN roles r ON e.role_id = r.role_id 
        LEFT JOIN permissions p ON r.role_id = p.permission_id 
        WHERE e.employee_id = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$employee_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User data not found.');
}

$role_name = strtolower($user['role_name'] ?? '');
$can_view_own_data = $user['can_view_own_data'] ?? false;
$can_view_team_data = $user['can_view_team_data'] ?? false;
$can_edit_data = $user['can_edit_data'] ?? false;
$can_manage_roles = $user['can_manage_roles'] ?? false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GM3 Builder Home Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }

        /* Style to make the notification dropdown scrollable */
        #notification-dropdown .custom-dropdown-right {
            max-height: 300px; /* Adjust this value based on your needs */
            overflow-y: auto;  /* Enable vertical scrolling */
            width: 300px;      /* Optional: Set the width to match your design */
        }
        .card.custom-border {
            border: 1px solid #c7c7c5; /* Change to your desired color and thickness */
        }
        .custom-border {
            border: 1px solid #c7c7c5;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light flex-column flex-lg-row align-items-start">
        <div class="d-flex align-items-center nav-phone justify-content-between">
            <?php if ($can_view_team_data): ?>
                <a class="navbar-brand font-weight-bold bg-blue" href="index.php">
                    <img src="assets/images/gm3-logo-small.png" width="30" height="30" class="d-inline-block align-top" alt="">
                    GM3 Builders
                </a>
            <?php endif; ?>
            <?php if ($role_name === 'employee'): ?>
                <a class="navbar-brand font-weight-bold bg-blue" href="employee_dashboard.php">
                    <img src="assets/images/gm3-logo-small.png" width="30" height="30" class="d-inline-block align-top" alt="">
                    GM3 Builders
                </a>
            <?php endif; ?>
        </div>
        <ul class="navbar-nav flex-row flex-column flex-lg-row">
            <li class="nav-item active mx-3 d-flex align-items-center">
                <?php if ($can_view_team_data): ?>
                    <img src="assets/images/home.png" class="header-icons mr-2" href="index.php"> <a class="nav-link" href="index.php">Home</a>
                <?php endif; ?>
                <?php if ($role_name === 'employee'): ?>
                    <img src="assets/images/home.png" class="header-icons mr-2" href="employee_dashboard.php"> <a class="nav-link" href="employee_dashboard.php">Home</a>
                <?php endif; ?>
            </li>
            <li class="nav-item mx-3 d-flex align-items-center">
                <img src="assets/images/about.png" class="header-icons mr-1"> <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item mx-3" id="notification-icon">
                <a class="nav-link" href="#">
                    <div style="position: relative;">
                        <img src="assets/images/notification.png" class="header-icons" alt="">
                        <?php if ($unread_count > 0): ?>
                            <span style="position: absolute; top: -5px; right: -10px; background: red; color: white; font-size: 12px; border-radius: 50%; padding: 2px 6px;">
                                <?php echo $unread_count; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </a>
            </li>
            <li class="nav-item mx-3">
                <img src="assets/images/account.png" alt="" style="width:39px;" id="account-icon">
            </li>
            <li class="pos-relative" id="logout-dropdown">
                <div class="custom-dropdown-right" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
            <li class="pos-relative" id="notification-dropdown">
                <div class="custom-dropdown-right" aria-labelledby="navbarDropdownMenuLink">
                    <?php
                    foreach ($notifs as $notif) {
                        $redirect =  $notif['request_type'] == "leave_request" ? 'hr-leaves-page.php' : 'profile-change-requests.php?';
                        echo '<div class="d-flex p-3 border-bottom dropdown-list">';
                        echo '<div class="pr-3" style="flex:0 0 91%;">';
                        echo '<p class="font-weight-bold p-0 m-0">' . $notif['message'] . '</p>';
                        echo '<p class="p-0 m-0">' . $notif['timestamp'] . '</p>';
                        echo '</div>';
                        echo '<div style="flex:0 0 30px;">';
                        echo '<a href="' . $redirect . '" class="mark-read" data-id="' . $notif['id'] . '">';
                        echo '<img src="assets/images/arrow-right.svg" style="width:30px; cursor:pointer;" alt="">';
                        echo '</a>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </li>
        </ul>
    </nav>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const notificationBadge = document.querySelector("#notification-icon span");

        function updateNotificationCount() {
            fetch("check_notifications.php")
                .then(response => response.json())
                .then(data => {
                    if (data.unread_count > 0) {
                        if (!notificationBadge) {
                            const newBadge = document.createElement("span");
                            newBadge.style.position = "absolute";
                            newBadge.style.top = "-5px";
                            newBadge.style.right = "-10px";
                            newBadge.style.background = "red";
                            newBadge.style.color = "white";
                            newBadge.style.fontSize = "12px";
                            newBadge.style.borderRadius = "50%";
                            newBadge.style.padding = "2px 6px";
                            newBadge.textContent = data.unread_count;
                            document.querySelector("#notification-icon div").appendChild(newBadge);
                        } else {
                            notificationBadge.textContent = data.unread_count;
                        }
                    } else if (notificationBadge) {
                        notificationBadge.remove();
                    }
                })
                .catch(err => console.error("Error updating notifications:", err));
        }

        setInterval(updateNotificationCount, 5000);
        updateNotificationCount(); 

        document.querySelectorAll('.mark-read').forEach(arrow => {
            arrow.addEventListener('click', function () {
                const notificationId = this.getAttribute('data-id');

                fetch('mark_notification_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: notificationId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.dropdown-list').style.display = 'none';
                        updateNotificationCount();
                    }
                })
                .catch(error => console.error('Error marking notification as read:', error));
            });
        });
    });
    </script>
</body>
</html>
