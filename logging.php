<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
  header("Location: login.php");
  exit();
}

$activePage = "logging";

include './layout/header.php';
include 'database.php';

$reports = "SELECT l.*, e.firstname, e.lastname 
            FROM logs l
            LEFT JOIN employees e ON e.employee_id = l.employee_id
            ORDER BY l.timestamp DESC;
          ";
$reports_results = mysqli_query($conn, $reports) or die("Error");

?>

<div class="d-flex">
  <?php include './layout/sidebar.php'; ?>
  <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
    <h2>Admin Logs</h2>
    <div class="row">
      <div class="col-12 table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Employee</th>
              <th>Title</th>
              <th>Report</th>
              <th>Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <?php
            while ($row_reports = mysqli_fetch_assoc($reports_results)) {
              echo "<tr>";
              echo "  <th>" . $row_reports['id'] . "</th>";
              echo "  <th>" . $row_reports['firstname'] . " " . $row_reports['lastname'] . "</th>";
              echo "  <th>" . $row_reports['title'] . "</th>";
              echo "  <th>" . $row_reports['report'] . "</th>";
              echo "  <th>" . $row_reports['timestamp'] . "</th>";
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
