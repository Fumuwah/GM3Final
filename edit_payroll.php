<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payrollId = $_POST['payroll_id'];
    $cashAdv = $_POST['cash_adv'];
    $allowance = $_POST['allowance'];
    $otherDeduc = $_POST['other_deduc'];

    // Step 1: Get the current values from the payroll table, and also join with employees table to get daily_salary
    $selectQuery = "
        SELECT 
            payroll.cash_adv, 
            payroll.allowance, 
            payroll.other_deduc, 
            payroll.total_hrs, 
            payroll.gross, 
            payroll.total_deduc, 
            payroll.netpay, 
            employees.daily_salary
        FROM payroll 
        JOIN employees ON payroll.employee_id = employees.employee_id 
        WHERE payroll.payroll_id = :payroll_id
    ";

    $stmt = $pdo->prepare($selectQuery);
    $stmt->bindParam(':payroll_id', $payrollId);
    $stmt->execute();
    $currentValues = $stmt->fetch(PDO::FETCH_ASSOC);

    // Step 2: Calculate the new values for gross, total_deduc, and netpay
    $gross = $currentValues['total_hrs'] * ($currentValues['daily_salary'] / 8) + $allowance;
    $totalDeduc = $otherDeduc + $cashAdv;
    $netpay = $gross - $totalDeduc;

    // Debug: Log the calculated values
    error_log("Gross: " . $gross);
    error_log("Total Deduct: " . $totalDeduc);
    error_log("Netpay: " . $netpay);

    // Step 3: Check if the values have changed
    $updateFields = [];
    $updateParams = [
        ':payroll_id' => $payrollId
    ];

    // Check if cash_adv, allowance, or other_deduc have changed
    if ($cashAdv != $currentValues['cash_adv']) {
        $updateFields[] = 'cash_adv = :cash_adv';
        $updateParams[':cash_adv'] = $cashAdv;
    }
    if ($allowance != $currentValues['allowance']) {
        $updateFields[] = 'allowance = :allowance';
        $updateParams[':allowance'] = $allowance;
    }
    if ($otherDeduc != $currentValues['other_deduc']) {
        $updateFields[] = 'other_deduc = :other_deduc';
        $updateParams[':other_deduc'] = $otherDeduc;
    }

    // Step 4: Add update fields for gross, total_deduc, and netpay if any values have changed
    if ($gross != $currentValues['gross']) {
        $updateFields[] = 'gross = :gross';
        $updateParams[':gross'] = $gross;
    }
    if ($totalDeduc != $currentValues['total_deduc']) {
        $updateFields[] = 'total_deduc = :total_deduc';
        $updateParams[':total_deduc'] = $totalDeduc;
    }
    if ($netpay != $currentValues['netpay']) {
        $updateFields[] = 'netpay = :netpay';
        $updateParams[':netpay'] = $netpay;
    }

    // Step 5: If any fields are different, perform the update
    if (!empty($updateFields)) {
        $updateQuery = "UPDATE payroll SET " . implode(', ', $updateFields) . " WHERE payroll_id = :payroll_id";
        $stmt = $pdo->prepare($updateQuery);

        if ($stmt->execute($updateParams)) {
            // Redirect back to the payroll page with a success message
            header("Location: generate_payroll_site.php?success=1");
            exit();
        } else {
            // Handle error
            echo "Error updating payroll.";
        }
    } else {
        // If no values have changed, you can handle this case (optional)
        echo "No changes detected. No update made.";
    }
}
?>
