<?php
function handleAttendance($employee_id, $action, $conn) {
    // Get current date and time
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');
    $current_day = date('d');
    $current_month = date('m');
    $current_year = date('Y');

    // Check if a record already exists for the employee for today
    $query = "SELECT * FROM dtr WHERE employee_id = ? AND date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $employee_id, $current_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();

    if ($action === 'time_in') {
        if ($attendance) {
            if ($attendance['time_in']) {
                return "You have already clocked in for today.";
            }
        } else {
            // Insert new attendance record with time_in
            $query = "INSERT INTO dtr (employee_id, day, month, year, date, time_in) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiisss", $employee_id, $current_day, $current_month, $current_year, $current_date, $current_time);
            $stmt->execute();
            return "Time-In recorded successfully.";
        }
    } elseif ($action === 'time_out') {
        if ($attendance) {
            if ($attendance['time_out']) {
                return "You have already clocked out for today.";
            } elseif (!$attendance['time_in']) {
                return "You need to clock in before clocking out.";
            } else {
                // Calculate total hours and overtime
                $time_in = new DateTime($attendance['time_in']);
                $time_out = new DateTime($current_time);
                $interval = $time_in->diff($time_out);
                $hours_worked = $interval->h + ($interval->i / 60); // Total hours worked

                $other_ot = max(0, $hours_worked - 8); // Overtime if hours exceed 8

                // Update attendance record with time_out, total_hrs, and other_ot
                $query = "UPDATE dtr 
                          SET time_out = ?, total_hrs = ?, other_ot = ? 
                          WHERE dtr_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sdii", $current_time, $hours_worked, $other_ot, $attendance['dtr_id']);
                $stmt->execute();
                return "Time-Out recorded successfully.";
            }
        } else {
            return "No attendance record found for today. Please clock in first.";
        }
    }
    return "Invalid action.";
}
?>
