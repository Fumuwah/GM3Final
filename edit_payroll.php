<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payrollId = $_POST['payroll_id'];
    $cashAdv = $_POST['cash_adv'];
    $allowance = $_POST['allowance'];
    $otherDeduc = $_POST['other_deduc'];

    // Step 1: Get the current values from the database
    $selectQuery = "SELECT cash_adv, allowance, other_deduc FROM payroll WHERE payroll_id = :payroll_id";
    $stmt = $pdo->prepare($selectQuery);
    $stmt->bindParam(':payroll_id', $payrollId);
    $stmt->execute();
    $currentValues = $stmt->fetch(PDO::FETCH_ASSOC);

    // Step 2: Check if the values have changed
    $updateFields = [];
    $updateParams = [
        ':payroll_id' => $payrollId
    ];

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

    // Step 3: If any fields are different, perform the update
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
