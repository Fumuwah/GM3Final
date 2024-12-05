<?php
require './database.php';

$employee_id = $_SESSION['employee_id'] ?? null;

if (!$employee_id) {
    die('User is not logged in.');
}

// Fetch role and permissions data for the logged-in user
$query = "SELECT r.role_name, 
                p.can_view_own_data, p.can_view_team_data, 
                p.can_edit_data, p.can_manage_roles,
                pt.project_name
        FROM employees e 
        JOIN roles r ON e.role_id = r.role_id 
        LEFT JOIN permissions p ON r.role_id = p.permission_id 
        LEFT JOIN projects pt ON e.project_name = pt.project_name
        WHERE e.employee_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$employee_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User data not found.');
}

$role_name = strtolower($user['role_name'] ?? '');
$project_name = $user['project_name'] ?? '';

// Construct the query to fetch unread notifications
$notification_query = "
    SELECT n.*, e.project_name
    FROM notification n
    JOIN employees e ON n.employee_id = e.employee_id
    LEFT JOIN projects p ON e.project_name = p.project_name
    WHERE n.is_read = 0
";

// Add conditions based on the user's role
if ($role_name == 'super admin') {
    $notification_query .= " AND n.message != 'Your leave request has been approved.'"; // Exclude approved leave requests for Super Admin
} elseif ($role_name == 'hr admin') {
    $notification_query .= " AND (n.message != 'Your leave request has been approved.' OR n.employee_id = $employee_id)"; // HR Admin sees their own approved requests
} elseif ($role_name == 'admin') {
    $notification_query .= " AND (n.employee_id = $employee_id OR e.project_name = '$project_name')"; // Admin sees approved leave requests of employees under the same project
} elseif ($role_name == 'employee') {
    $notification_query .= " AND n.employee_id = $employee_id"; // Employee sees their own leave requests
}

$notification_query .= " ORDER BY n.timestamp DESC";

$notification_prep = mysqli_prepare($conn, $notification_query);
mysqli_stmt_execute($notification_prep);
$resultnotification = mysqli_stmt_get_result($notification_prep);
$notifs = mysqli_fetch_all($resultnotification, MYSQLI_ASSOC);

// Count unread notifications based on the same conditions
$unread_query = "
    SELECT COUNT(*) AS unread_count 
    FROM notification n
    JOIN employees e ON n.employee_id = e.employee_id
    LEFT JOIN projects p ON e.project_name = p.project_name
    WHERE n.is_read = 0
";

if ($role_name == 'super admin') {
    $unread_query .= " AND n.message != 'Your leave request has been approved.'"; // Exclude approved leave requests for Super Admin
} elseif ($role_name == 'hr admin') {
    $unread_query .= " AND (n.message != 'Your leave request has been approved.' OR n.employee_id = $employee_id)"; // HR Admin sees their own approved requests
} elseif ($role_name == 'admin') {
    $unread_query .= " AND (n.employee_id = $employee_id OR e.project_name = '$project_name')"; // Admin sees approved leave requests of employees under the same project
} elseif ($role_name == 'employee') {
    $unread_query .= " AND n.employee_id = $employee_id"; // Employee sees their own leave requests
}

$result_unread = mysqli_query($conn, $unread_query);
if ($result_unread) {
    $unread_count = mysqli_fetch_assoc($result_unread)['unread_count'];
} else {
    $unread_count = 0;
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
        }

        /* Style to make the notification dropdown scrollable and wider */
        #notification-dropdown .custom-dropdown-right {
            max-height: 300px; /* Adjust this value based on your needs */
            overflow-y: auto;  /* Enable vertical scrolling */
            width: 400px;      /* Adjust the width to make the dropdown a little longer */
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
                // Check if there are any notifications for everyone
                if (empty($notifs)) {
                    echo '<div class="p-3 text-center" style="color: #888; font-style: italic;">';
                    echo 'No notifications available.';
                    echo '</div>';
                } else {
                    // Loop through notifications if available
                    foreach ($notifs as $notif) {
                        // Announcement notification (visible for Super Admin, HR Admin, Admin, and Employee)

                        if ($notif['request_type'] == "announcement") {
                            // Set the redirect based on role
                            $redirect = '';
                            if ($_SESSION['role_name'] == 'Super Admin' || $_SESSION['role_name'] == 'HR Admin' || $_SESSION['role_name'] == 'Admin') {
                                $redirect = 'index.php'; // Admin and HR roles redirect to index.php
                            } elseif ($_SESSION['role_name'] == 'Employee') {
                                $redirect = 'employee_dashboard.php'; // Employee role redirects to employee_dashboard.php
                            }
                            echo '<div class="d-flex p-3 border-bottom dropdown-list">';
                            echo '<div class="pr-3" style="flex:0 0 91%;">';
                            echo '<p class="font-weight-bold p-0 m-0">' . $notif['message'] . '</p>';
                            echo '<p class="p-0 m-0">' . $notif['timestamp'] . '</p>';
                            echo '</div>';
                            echo '<div style="flex:0 0 30px;">';
                            echo '<a href="' . $redirect . '" class="mark-read" data-id="' . $notif['id'] . '" onclick="markAsRead(' . $notif['id'] . ')">';
                            echo '<img src="assets/images/arrow-right.svg" style="width:30px; cursor:pointer;" alt="">';
                            echo '</a>';
                            echo '</div>';
                            echo '</div>';
                        } 
                        // Leave request notifications
                        elseif ($notif['request_type'] == "leave_request") {
                            // Hierarchy of visibility based on role
                            if ($_SESSION['role_name'] == 'Super Admin') {
                                if ($_SESSION['role_name'] == 'Super Admin' && $notif['message'] == 'Your leave request has been approved.') {
                                    continue; // Skip this notification for Super Admin
                                }
                                // Super Admin sees all leave requests
                                $redirect = 'hr-leaves-page.php';
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
                            } elseif ($_SESSION['role_name'] == 'HR Admin') {
                                // HR Admin sees leave requests from Admin and Employee
                                if ($_SESSION['role_name'] == 'HR Admin' && $notif['message'] == 'Your leave request has been approved.' && $notif['employee_id'] != $employee_id) {
                                    continue;
                                }
                                $redirect = 'hr-leaves-page.php';
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
                            } elseif ($_SESSION['role_name'] == 'Admin') {
                                // Admin only sees leave requests from Employee
                                if ($_SESSION['role_name'] == 'Admin' && $notif['message'] == 'Your leave request has been approved.' && ($notif['employee_id'] != $employee_id || $notif['project_name'] != $_SESSION['project_name'])) {
                                    continue;
                                }
                                if ($notif['employee_id'] == $employee_id) {
                                    $redirect = 'hr-leaves-page.php';
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
                            } elseif ($_SESSION['role_name'] == 'Employee') {
                                // Employee only sees their own leave requests
                                if ($_SESSION['role_name'] == 'Employee' && $notif['employee_id'] != $employee_id) {
                                    continue;
                                }
                                if ($notif['employee_id'] == $employee_id) {
                                    $redirect = 'employee_dashboard.php';
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
                            }
                        } 
                        // For other types of notifications (e.g., profile change, etc.)
                        else {
                            $redirect = 'profile-change-requests.php?';
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
                    }
                }
                ?>
            </div>
</li>

        </ul>
    </nav>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const notificationBadge = document.querySelector("#notification-icon span");

        document.querySelectorAll('.mark-read').forEach(arrow => {
            arrow.addEventListener('click', function () {
                const notificationId = this.getAttribute('data-id');

                // Send the request to update 'is_read' status
                fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: notificationId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the notification item
                        this.closest('.dropdown-list').remove();

                        // Update unread count
                        if (notificationBadge) {
                            let unreadCount = parseInt(notificationBadge.textContent, 10);
                            unreadCount--;
                            if (unreadCount <= 0) {
                                notificationBadge.remove();
                            } else {
                                notificationBadge.textContent = unreadCount;
                            }
                        }
                    } else {
                        console.error('Failed to mark notification as read');
                    }
                })
                .catch(err => console.error('Error:', err));
            });
        });
    });
    </script>

    <?php
    // Check if the request to mark a notification as read is made
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id'])) {
            $notificationId = $data['id'];

            // Update the notification's is_read status to true
            $update_query = "UPDATE notification SET is_read = 1 WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("i", $notificationId);
            $stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Notification marked as read.']);
        }
        exit;
    }
    ?>
</body>
</html>
