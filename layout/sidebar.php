<?php
include('database.php');

// Check if employee_id exists in session
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

$activePage = $activePage ?? ''; 
?>

<div class="sidebar-container">
    <div class="arrow-container shadow-sm" id="arrow">></div>
    <div class="sidebar">
        <div class="sidebar-section">
            <h6 class="sidebar-title pl-3">Navigation</h6>
            <ul class="sidebar-ul p-0">
                <?php if ($role_name === 'super admin' || $role_name === 'admin'): ?>
                    <li class="sidebar-link <?= ($activePage === 'dashboard') ? 'active-link' : ''; ?>">
                        <a href="index.php">Dashboard</a></li>
                <?php endif; ?>
                
                <?php if ($role_name === 'employee'): ?>
                    <li class="sidebar-link <?= ($activePage === 'employee_dashboard') ? 'active-link' : ''; ?>">
                        <a href="employee_dashboard.php">Dashboard</a></li>
                <?php endif; ?>
                
                <?php if ($can_view_own_data): ?>
                    <li class="sidebar-link <?= ($activePage === 'employee_profile') ? 'active-link' : ''; ?>">
                    <a href="employee-profile.php">Profile</a></li>
                <?php endif; ?>

                <?php if ($can_manage_roles): ?>
                    <li class="sidebar-link <?= ($activePage === 'user_controller') ? 'active-link' : ''; ?>">
                        <a href="users.php">User Controller</a></li>
                <?php endif; ?>

                <?php if ($can_view_team_data || $role_name === 'super admin'): ?>
                    <li class="sidebar-link <?= ($activePage === 'employees') ? 'active-link' : ''; ?>">
                        <a href="employees.php">Employees</a></li>
                <?php endif; ?>

                <?php if ($role_name === 'super admin' || $role_name === 'admin'): ?>
                    <li class="sidebar-link <?= ($activePage === 'dtr') ? 'active-link' : ''; ?>">
                        <a href="dtr.php">Daily Time Record</a></li>
                <?php endif; ?>

                <?php if ($role_name === 'super admin' || $role_name === 'admin'): ?>
                            <li>
                            <div class="dropdown-link sidebar-link <?= ($activePage === 'hr-leaves-page') ? 'active-link' : ''; ?>" id="leaves-dropdown-link" data-display="false" style="cursor: pointer;">
                                <a href="hr-leaves-page.php">Leave Credits</a>
                                <span id="chevron-container">
                                    <img src="assets/images/chevron-right.svg" alt="">
                                </span>
                            </div>
                            <?php if ($can_view_own_data || $role_name === 'employee'): ?>
                            <ul class="dropdown sidebar-link <?= ($activePage === 'leave-page') ? 'active-link' : ''; ?>" id="leaves-dropdown" >
                                <li class="<?= ($activePage === 'my_leaves') ? 'active-link' : ''; ?>" style="cursor: pointer; padding: 10px 0;">
                                    <a href="leave-page.php">My Leaves</a>
                                </li>
                            </ul>
                            <?php endif; ?>
                        </li>
                <?php endif; ?>
                            <?php if ($role_name === 'employee'): ?>
                            <ul class="sidebar-link <?= ($activePage === 'leave-page') ? 'active-link' : ''; ?>" id="leaves-dropdown" >
                                <li class="<?= ($activePage === 'my_leaves') ? 'active-link' : ''; ?>" style="cursor: pointer; padding: 10px 0;">
                                    <a href="leave-page.php">Leaves</a>
                                </li>
                            </ul>
                            <?php endif; ?>
                <?php if ($can_edit_data || $role_name === 'super admin'): ?>
                    <li>
                    <div class="dropdown-link sidebar-link <?= ($activePage === 'payroll') ? 'active-link' : ''; ?>" id="payroll-dropdown-link" data-display="false" style="cursor: pointer;">
                        <a href="payroll.php">Payroll</a>
                        <span id="chevron-container">
                            <img src="assets/images/chevron-right.svg" alt="">
                        </span>
                    </div>
                    <ul class="dropdown sidebar-link <?= ($activePage === 'generate_payroll_site') ? 'active-link' : ''; ?>" >
                        <li style="cursor: pointer; padding: 4px 0;">
                            <a href="generate_payroll_site.php">Payroll Summary</a>
                        </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if ($can_view_own_data): ?>
                    <li class="sidebar-link <?= ($activePage === 'payslip') ? 'active-link' : ''; ?>">
                        <a href="payslip_page.php">Payslip</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>