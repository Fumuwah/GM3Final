<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromDay = mysqli_real_escape_string($conn, $_POST['fromDay']);
    $toDay = mysqli_real_escape_string($conn, $_POST['toDay']);
    $employee_number = mysqli_real_escape_string($conn, $_POST['employee_number']);
    $withhold_tax = mysqli_real_escape_string($conn, $_POST['withhold_tax']);
    $sss_con = mysqli_real_escape_string($conn, $_POST['sss_con']);
    $philhealth_con = mysqli_real_escape_string($conn, $_POST['philhealth_con']);
    $pag_ibig_con = mysqli_real_escape_string($conn, $_POST['pag-ibig_con']);
    $other_deduc = mysqli_real_escape_string($conn, $_POST['other_deduc']);
    $allowance = mysqli_real_escape_string($conn, $_POST['allowance']);
    $monthly = mysqli_real_escape_string($conn, $_POST['monthly']);
    $daily = mysqli_real_escape_string($conn, $_POST['daily']);
    $hourly = mysqli_real_escape_string($conn, $_POST['hourly']);
    $total_hrs = mysqli_real_escape_string($conn, $_POST['total_hrs']);
    $other_ot = mysqli_real_escape_string($conn, $_POST['other_ot']);
    $totalhrs = mysqli_real_escape_string($conn, $_POST['totalhrs']);
    $special_holiday = mysqli_real_escape_string($conn, $_POST['special_holiday']);
    $special_leave = mysqli_real_escape_string($conn, $_POST['special_leave']);
    $gross = mysqli_real_escape_string($conn, $_POST['gross']);
    $cash_adv = mysqli_real_escape_string($conn, $_POST['cash_adv']);
    $total_deduc = mysqli_real_escape_string($conn, $_POST['total_deduc']);
    $netpay = mysqli_real_escape_string($conn, $_POST['netpay']);

    $result = mysqli_query($conn, "SELECT employee_id FROM employees WHERE employee_number = '$employee_number'");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $employee_id = $row['employee_id'];
    } else {
        die("Employee not found with the given employee number.");
    }

    $payroll_period = date('m', strtotime($fromDay)) . '/' . date('d', strtotime($fromDay)) . '-' . date('d', strtotime($toDay)) . '/' . date('y', strtotime($toDay));
    $xmonth = date('m', strtotime($fromDay));
    $xfromto =  date('d', strtotime($fromDay)) . '-' . date('d', strtotime($toDay));
    $xyear = date('y', strtotime($toDay));

    $query = "
            INSERT INTO payroll (
                payroll_period, employee_id,
                withhold_tax, sss_con, philhealth_con, pag_ibig_con, other_deduc,
                allowance, monthly, daily, hourly,
                total_hrs, other_ot, totalhrs, special_holiday, special_leave,
                gross, cash_adv, total_deduc, netpay, months, days, year
            ) VALUES (
                '$payroll_period', '$employee_id',
                '$withhold_tax', '$sss_con', '$philhealth_con', '$pag_ibig_con', '$other_deduc',
                '$allowance', '$monthly', '$daily', '$hourly',
                '$total_hrs', '$other_ot', '$totalhrs', '$special_holiday', '$special_leave',
                '$gross', '$cash_adv', '$total_deduc', '$netpay', '$xmonth', '$xfromto', '$xyear'
            )";

    if (mysqli_query($conn, $query)) {
        echo "Payroll data submitted successfully.";
        header("Location: payroll.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

mysqli_close($conn);
