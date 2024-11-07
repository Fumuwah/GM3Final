<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "gm3builders";

// MySQLi connection (for compatibility)
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong with MySQLi connection!");
}

try {
    // Ensure the database name is specified in the PDO connection
    $pdo = new PDO("mysql:host=localhost;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: Display available databases (for debugging)
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
