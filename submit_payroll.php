<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_number = $_POST['employee_number'];

    // Step 1: Retrieve the employee_id using the employee_number
    $query = "SELECT employee_id FROM employees WHERE employee_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $employee_number);
    $stmt->execute();
    $stmt->bind_result($employee_id);
    $stmt->fetch();
    $stmt->close();

    // Step 2: Retrieve the payroll data from the POST request
    $basic_salary = floatval($_POST['basic_salary']);
    $allowance = floatval($_POST['allowance']);
    $monthly = floatval($_POST['monthly']);
    $daily = floatval($_POST['daily']);
    $hourly = floatval($_POST['hourly']);
    $total_hrs = floatval($_POST['total_hrs']);
    $other_ot = floatval($_POST['other_ot']);
    $salary = floatval($_POST['salary']);
    $specialHoliday = floatval($_POST['special_holiday']);
    $specialLeave = floatval($_POST['special_leave']);
    $incentives = floatval($_POST['incentives']);
    $gross = floatval($_POST['gross']);
    $less_cont = floatval($_POST['less_cont']);
    $cash_adv = floatval($_POST['cash_adv']);
    
    // Calculate total deduction and netpay
    $total_deduc = $less_cont + $cash_adv;
    $netpay = $gross - $total_deduc;

    // Step 3: Insert payroll data into the `payroll` table, including the employee_id
    $query = "INSERT INTO payroll (employee_id, employee_number, basic_salary, allowance, monthly, daily, hourly, total_hrs, other_ot, salary, special_holiday, special_leave, incentives, gross, less_cont, cash_adv, total_deduc, netpay) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssssssssssssss", $employee_id, $employee_number, $basic_salary, $allowance, $monthly, $daily, $hourly, $total_hrs, $other_ot, $salary, $specialHoliday, $specialLeave, $incentives, $gross, $less_cont, $cash_adv, $total_deduc, $netpay);

    if ($stmt->execute()) {
        header("Location: payroll.php?status=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: payroll.php?status=error");
}
?>
