<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "police_bidding";

mysqli_report(MYSQLI_REPORT_OFF);

$conn = @mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    // If the database doesn't exist, just suppress the error and let db_setup.php handle it if they visit it.
    if (strpos($_SERVER['SCRIPT_NAME'], 'db_setup.php') === false) {
        die("Database connection failed. Please run db_setup.php to initialize the database. Error: " . mysqli_connect_error());
    }
}
?>
