<?php
include 'database.php';

if(isset($_POST['submit'])){
    $id = $_POST['edit_id'];
    $timein = $_POST['edit_timein'];
    $timeout = $_POST['edit_timeout'];

    $sql = "UPDATE dtr SET time_in = '$timein',time_out = '$timeout' WHERE dtr_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header('location:dtr.php');
}

?>