<?php
// db.php

$host = 'mysql.hostinger.com';       // Usually localhost for local servers
$user = 'u852669780_argonsales';            // Default for XAMPP/WAMP
$password = '1973@Box2';            // Default is empty unless you set one
$database = 'u852669780_argonsales'; // Replace with your actual database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8mb4");
?>
