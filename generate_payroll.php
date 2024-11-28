<?php
session_start();
include 'database.php'; // Include your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fromDay = $input['fromDay'] ?? null;
    $toDay = $input['toDay'] ?? null;
    $employeeStatus = $input['employeeStatus'] ?? null;

    if (!$fromDay || !$toDay || !$employeeStatus) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    // Calculate payroll
    try {
        $payroll_period = date('m', strtotime($fromDay)) . '/' . date('d', strtotime($fromDay)) . '-' . date('d', strtotime($toDay)) . '/' . date('y', strtotime($toDay));

        $query = "SELECT * FROM employees WHERE employee_status = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$employeeStatus]);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($employees as $employee) {
            $employee_id = $employee['employee_id'];
            $daily = $employee['daily_salary'];
            $hourly = $daily / 8;
            $allowance = $employee['allowance'] ?? 0;
            $monthly = ($employeeStatus === 'Contractual') ? 0 : $employee['basic_salary'];

            $dtrQuery = "SELECT SUM(total_hrs) AS total_hrs, SUM(other_ot) AS other_ot 
                         FROM dtr 
                         WHERE employee_id = ? 
                         AND date BETWEEN ? AND ?";
            $dtrStmt = $pdo->prepare($dtrQuery);
            $dtrStmt->execute([$employee_id, $fromDay, $toDay]);
            $dtrData = $dtrStmt->fetch(PDO::FETCH_ASSOC);

            $total_hrs = $dtrData['total_hrs'] ?? 0;
            $other_ot = $dtrData['other_ot'] ?? 0;
            $totalhrs = $total_hrs + $other_ot;

            $gross = ($totalhrs * $hourly) + $allowance;

            $withhold_tax = $employee['withhold_tax'] ?? 0;
            $sss_con = $employee['sss_con'] ?? 0;
            $philhealth_con = $employee['philhealth_con'] ?? 0;
            $pag_ibig_con = $employee['pag_ibig_con'] ?? 0;
            $cash_adv = $employee['cash_adv'] ?? 0;
            $other_deduc = $employee['other_deduc'] ?? 0;

            $total_deduc = $withhold_tax + $sss_con + $philhealth_con + $pag_ibig_con + $cash_adv + $other_deduc;
            $netpay = $gross - $total_deduc;

            $insertQuery = "INSERT INTO payroll (employee_id, allowance, monthly, daily, hourly, total_hrs, other_ot, gross, total_deduc, other_deduc, cash_adv, netpay, totalHrs, payroll_period) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([$employee_id, $allowance, $monthly, $daily, $hourly, $total_hrs, $other_ot, $gross, $total_deduc, $other_deduc, $cash_adv, $netpay, $totalhrs, $payroll_period]);
        }

        echo json_encode(['success' => true, 'message' => 'Payroll calculated successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
