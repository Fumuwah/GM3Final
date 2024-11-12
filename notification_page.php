<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$activePage = "notification";

include './layout/header.php';
include 'database.php';


$xsql = "SELECT * FROM notification ORDER BY id DESC;
          ";
$xnotifs = mysqli_query($conn, $xsql) or die("Error");


?>

<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <h2>Notifications</h2>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Message</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        while ($row_xnotifs = mysqli_fetch_assoc($xnotifs)) {
                            $xredirect =  $row_xnotifs['request_type'] == "leave_request" ? 'hr-leaves-page.php' : 'profile-change-requests.php?';
                            echo "<tr>";
                            echo "  <th>" . $row_xnotifs['message'] . "</th>";
                            echo '<th>';
                            echo '<a href="' . $xredirect . '">';
                            echo '<img src="assets/images/arrow-right.svg" style="width:30px; cursor:pointer;" alt="">';
                            echo '</a></th>';
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include './layout/script.php'; ?>
<?php include './layout/footer.php'; ?>