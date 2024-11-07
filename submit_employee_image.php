<?php
session_start();
require 'database.php';

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];

if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $allowed = ['jpg', 'jpeg', 'png'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowed)) {
        echo "Error: Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
    } elseif ($fileSize > 5000000) {
        echo "Error: File is too large. Maximum allowed size is 5MB.";
    } else {
        $newFileName = "employee_$employee_id." . $fileExt;
        $fileDestination = "uploads/$newFileName";

        if (move_uploaded_file($fileTmpName, $fileDestination)) {
            $query = "UPDATE employees SET image_path = ? WHERE employee_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $fileDestination, $employee_id);
            $stmt->execute();

            header("Location: employee-profile.php?upload=success");
            exit();
        } else {
            echo "There was an error moving the uploaded file.";
        }
    }
} else {
    $uploadErrors = [
        1 => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
        2 => "The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.",
        3 => "The uploaded file was only partially uploaded.",
        4 => "No file was uploaded.",
        6 => "Missing a temporary folder.",
        7 => "Failed to write file to disk.",
        8 => "A PHP extension stopped the file upload."
    ];

    $errorCode = $_FILES['file']['error'];
    echo isset($uploadErrors[$errorCode]) ? $uploadErrors[$errorCode] : "Unknown error uploading file.";
}
?>
