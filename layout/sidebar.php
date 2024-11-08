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

<aside class="expand" id="sidebar">
    <div class="arrow-container shadow-sm" id="arrow">></div>
    <div class="sidebar-title pl-3 pt-3">
        <p class="text-dark" style="font-size: 20px;font-weight:700;">Navigation</p>
    </div>
    <ul class="sidebar-nav">
        <?php if ($role_name === 'super admin' || $role_name === 'admin'): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="index.php" style="<?= ($activePage === 'dashboard') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Dashboard</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($role_name === 'employee'): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="employee_dashboard.php" style="<?= ($activePage === 'employee_dashboard') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Dashboard</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($can_view_own_data): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="employee-profile.php" style="<?= ($activePage === 'employee_profile') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Profile</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($can_manage_roles): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="users.php" style="<?= ($activePage === 'user_controller') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>User Controller</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($can_view_team_data || $role_name === 'super admin'): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="employees.php" style="<?= ($activePage === 'employees') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Employees</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($role_name === 'super admin' || $role_name === 'admin'): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="dtr.php" style="<?= ($activePage === 'dtr') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Daily Time Record</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($role_name === 'super admin' || $role_name === 'admin'): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link has-dropdown collapsed" id="collapse1" data-bs-toggle="collapse" href="#" style="<?= ($activePage === 'hr-leaves-page') || ($activePage === 'my_leaves') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Leave Credits</span>
                </a>
                <ul class="sidebar-dropdown list-unstyled collapse" id="collapsetest1" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="hr-leaves-page.php">Employee Leaves</a>
                    </li>
                    <?php if ($can_view_own_data || $role_name === 'employee'): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="leave-page.php">My Leaves</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>
        <?php if ($role_name === 'employee'): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="leave-page.php" style="<?= ($activePage === 'my_leaves') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Leaves</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if ($can_edit_data || $role_name === 'super admin'): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link has-dropdown collapsed" id="collapse2" data-bs-toggle="collapse" href="#" style="<?= ($activePage === 'payroll') || ($activePage === 'generate_payroll_site') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Payroll</span>
                </a>
                <ul class="sidebar-dropdown list-unstyled collapse" id="collapsetest2" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="payroll.php">Employee Payroll</a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="generate_payroll_site.php">Payroll Summary</a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if ($can_view_own_data): ?>
            <li class="sidebar-item" style="">
                <a class="sidebar-link" href="payslip_page.php" style="<?= ($activePage === 'payslip') ? 'background-color:rgba(0, 123, 255, 0.5);border-left: 3px solid #3b7ddd;' : ''; ?>">
                    <span>Payslip</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</aside>

