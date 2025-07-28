<?php
session_start();
require_once '../includes/db.php'; // Contains db connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id, name, role, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $name, $role, $password_hash);
            $stmt->fetch();

           if (password_verify($password, $password_hash)) {
    $_SESSION["user_id"] = $id;
    $_SESSION["user_name"] = $name;
    $_SESSION["user_role"] = $role;

    // Redirect based on role
    if ($role === "boss") {
        header("Location: ../dashboard.php");
    } elseif ($role === "shopkeeper") {
        header("Location: ../pages/sales.php");
    } else {
        echo "Unauthorized role.";
    }
    exit();

            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "No account found with that email.";
        }
    } else {
        echo "Something went wrong. Please try again later.";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>
