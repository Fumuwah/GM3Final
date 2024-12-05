<?php
session_start();
include 'database.php'; // Include your database connection script

// Enable detailed error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON payload from the client
    $input = json_decode(file_get_contents('php://input'), true);

    $fromDay = $input['fromDay'] ?? null;
    $toDay = $input['toDay'] ?? null;
    $employeeStatus = $input['employeeStatus'] ?? null;
    $roleName = $input['roleName'] ?? null;
    $projectName = $input['projectName'] ?? null; // Assuming project name is passed in input

    if (!$fromDay || !$toDay || !$employeeStatus || !$roleName) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    try {
        // Format payroll period components
        $month = date('m', strtotime($fromDay));
        $year = date('Y', strtotime($toDay));
        $days = date('d', strtotime($fromDay)) . '-' . date('d', strtotime($toDay));

        // Adjust the query based on the selected role and project name (if provided)
        $query = "SELECT e.* 
                  FROM employees e
                  JOIN roles r ON e.role_id = r.role_id
                  WHERE r.role_name = ? AND e.employee_status = ?";

        // If project name is provided, add it to the query
        if ($projectName) {
            $query .= " AND e.project_name = ?";
        }

        // Prepare the query
        $stmt = $pdo->prepare($query);
        if ($projectName) {
            $stmt->execute([$roleName, $employeeStatus, $projectName]);
        } else {
            $stmt->execute([$roleName, $employeeStatus]);
        }

        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Number of employees fetched: " . count($employees));

        if (empty($employees)) {
            echo json_encode(['success' => false, 'message' => 'No employees found for the given role, status, and project.']);
            exit;
        }

        foreach ($employees as $employee) {
            $employee_id = $employee['employee_id'];
            error_log("Processing employee_id: $employee_id");

            $daily = $employee['daily_salary'];
            $hourly = $daily / 8;
            $allowance = $employee['allowance'] ?? 0;
            $monthly = ($employeeStatus === 'Contractual') ? 0 : $employee['basic_salary'];

            // Fetch DTR data
            $dtrQuery = "SELECT SUM(total_hrs) AS total_hrs, SUM(other_ot) AS other_ot 
                         FROM dtr 
                         WHERE employee_id = ? 
                         AND date BETWEEN ? AND ?";
            $dtrStmt = $pdo->prepare($dtrQuery);
            $dtrStmt->execute([$employee_id, $fromDay, $toDay]);
            $dtrData = $dtrStmt->fetch(PDO::FETCH_ASSOC);

            error_log("DTR data for employee_id $employee_id: " . json_encode($dtrData));

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

            // Insert payroll record
            try {
                $insertQuery = "INSERT INTO payroll (employee_id, allowance, monthly, daily, hourly, total_hrs, other_ot, gross, total_deduc, other_deduc, cash_adv, netpay, totalHrs, payroll_period, days, month, year) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $pdo->prepare($insertQuery);
                $insertStmt->execute([ 
                    $employee_id, $allowance, $monthly, $daily, $hourly, $total_hrs, $other_ot, $gross, $total_deduc, $other_deduc, $cash_adv, $netpay,
                    $totalhrs, "$month/$days/$year", $days, $month, $year
                ]);
                error_log("Payroll inserted successfully for employee_id $employee_id");
            } catch (Exception $e) {
                error_log("Error inserting payroll for employee_id $employee_id: " . $e->getMessage());
                throw $e;
            }
        }

        echo json_encode(['success' => true, 'message' => 'Payroll calculated successfully.']);
    } catch (Exception $e) {
        error_log("Exception caught in generate_payroll.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'An error occurred while processing payroll.']);
    }
}
?>
