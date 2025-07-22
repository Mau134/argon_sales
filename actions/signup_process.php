<?php
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'];
    $password = $_POST['password'];

    // Validate role
    if (!in_array($role, ['boss', 'shopkeeper'])) {
        die("Invalid role selected.");
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Check for existing user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "An account with this email already exists.";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, role, password_hash, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $role, $passwordHash);

        if ($stmt->execute()) {
            header("Location: ../login.php?registered=true");
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../signup.php");
    exit();
}
?>
