<?php
include 'database.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_number = $_POST['employee_number'];
    $date = $_POST['date'];  // 'YYYY-MM-DD' format
    $time = $_POST['time'];
    $action = $_POST['attendance_action']; // 'time_in' or 'time_out'

    $response = array(); // Array to store response messages

    // Extract day, month, and year from the given date
    $day = date('d', strtotime($date));
    $month = date('m', strtotime($date));
    $year = date('Y', strtotime($date));

    // Step 1: Fetch employee_id using employee_number
    $employee_query = "SELECT employee_id FROM employees WHERE employee_number = ?";
    $stmt = $conn->prepare($employee_query);
    $stmt->bind_param('s', $employee_number);
    $stmt->execute();
    $employee_result = $stmt->get_result();

    if ($employee_result->num_rows > 0) {
        $employee = $employee_result->fetch_assoc();
        $employee_id = $employee['employee_id'];

        // Step 2: Check if there's already a DTR record for this employee and date
        $check_query = "SELECT * FROM dtr WHERE employee_id = ? AND date = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('ss', $employee_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($action == 'time_in' && empty($row['time_in'])) {
                // Record time-in if not already set
                $update_query = "UPDATE dtr SET time_in = ? WHERE employee_id = ? AND date = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param('sss', $time, $employee_id, $date);

                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Time-in recorded successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to record time-in: ' . $stmt->error;
                }
            } elseif ($action == 'time_out' && empty($row['time_out'])) {
                // Update time-out and calculate total hours
                $update_query = "
                    UPDATE dtr 
                    SET time_out = ?, 
                        total_hrs = ROUND(TIMESTAMPDIFF(MINUTE, time_in, ?) / 60, 2) 
                    WHERE employee_id = ? AND date = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param('ssss', $time, $time, $employee_id, $date);

                if ($stmt->execute()) {
                    // Calculate overtime hours (if more than 8 hours)
                    $ot_query = "
                        UPDATE dtr 
                        SET other_ot = GREATEST(0, ROUND(TIMESTAMPDIFF(MINUTE, time_in, time_out) / 60 - 8, 2)) 
                        WHERE employee_id = ? AND date = ?";
                    $stmt = $conn->prepare($ot_query);
                    $stmt->bind_param('ss', $employee_id, $date);
                    $stmt->execute();

                    $response['status'] = 'success';
                    $response['message'] = 'Time-out recorded successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to record time-out: ' . $stmt->error;
                }
            } else {
                $response['status'] = 'warning';
                $response['message'] = 'Attendance already recorded.';
            }
        } else {
            // No existing record, insert a new one
            if ($action == 'time_in') {
                $insert_query = "
                    INSERT INTO dtr (employee_id, date, day, month, year, time_in) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param('ssssss', $employee_id, $date, $day, $month, $year, $time);

                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Time-in recorded successfully.';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Failed to record time-in: ' . $stmt->error;
                }
            } else {
                $response['status'] = 'warning';
                $response['message'] = 'Please time-in before time-out.';
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Employee number not found.';
    }

    // Return the response as JSON
    echo json_encode($response);
}
?>
