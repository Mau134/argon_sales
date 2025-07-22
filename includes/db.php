<?php
// db.php

$host = 'localhost';       // Usually localhost for local servers
$user = 'root';            // Default for XAMPP/WAMP
$password = '';            // Default is empty unless you set one
$database = 'argonsales'; // Replace with your actual database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8mb4");
?>
