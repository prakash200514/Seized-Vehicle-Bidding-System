<?php
$servername = "localhost";
$username = "root";
$password = "password";

// Create connection
$conn = @mysqli_connect($servername, $username, $password);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS police_bidding";
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully.<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

mysqli_select_db($conn, "police_bidding");

// Create Users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
)";
mysqli_query($conn, $sql);

// First drop dependent tables and vehicles (dev mode reset for vehicles)
mysqli_query($conn, "DROP TABLE IF EXISTS auction_result, bids, vehicles");

// Create Vehicles table
$sql = "CREATE TABLE IF NOT EXISTS vehicles (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_name VARCHAR(150) NOT NULL,
    model VARCHAR(100) NOT NULL,
    description TEXT,
    registration_no VARCHAR(50),
    insurance_status ENUM('valid', 'expired', 'none') DEFAULT 'none',
    rc_book_status ENUM('available', 'missing') DEFAULT 'missing',
    country VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    base_price DECIMAL(10,2) NOT NULL,
    auction_start DATETIME NOT NULL,
    auction_end DATETIME NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    status ENUM('upcoming', 'active', 'closed', 'approved') NOT NULL DEFAULT 'upcoming'
)";
mysqli_query($conn, $sql);

// Create Bids table
$sql = "CREATE TABLE IF NOT EXISTS bids (
    bid_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    bid_amount DECIMAL(10,2) NOT NULL,
    bid_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id) ON DELETE CASCADE
)";
mysqli_query($conn, $sql);

// Create Auction Result table
$sql = "CREATE TABLE IF NOT EXISTS auction_result (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    winner_user_id INT NOT NULL,
    final_price DECIMAL(10,2) NOT NULL,
    approved_by_admin BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id) ON DELETE CASCADE,
    FOREIGN KEY (winner_user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";
mysqli_query($conn, $sql);

// Seed Admin User
$admin_pw = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (name, email, password, role) 
        SELECT 'Police Admin', 'admin@police.gov', '$admin_pw', 'admin' 
        WHERE NOT EXISTS (SELECT email FROM users WHERE email = 'admin@police.gov')";
mysqli_query($conn, $sql);

echo "<h3>Setup completed successfully!</h3>";
echo "<a href='index.php'>Go to Homepage</a> | <a href='login.php'>Go to Login (Admin: admin@police.gov / admin123)</a>";
?>
