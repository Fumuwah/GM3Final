<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "u327394152_gm3builders";

// MySQLi connection
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

if (!$conn) {
    die("MySQLi Connection failed: " . mysqli_connect_error());
}

try {
    // Correct PDO connection with database name and credentials
    $pdo = new PDO("mysql:host=$hostName;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
