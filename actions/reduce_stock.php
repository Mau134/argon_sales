<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = intval($_POST['product_id']);
    $reduceQuantity = intval($_POST['reduce_quantity']);

    // Get current stock
    $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($currentQuantity);
    $stmt->fetch();
    $stmt->close();

    if ($currentQuantity === null) {
        die("Product not found.");
    }

    if ($reduceQuantity > $currentQuantity) {
        die("Cannot reduce more than available stock.");
    }

    // Reduce stock
    $newQuantity = $currentQuantity - $reduceQuantity;

    $updateStmt = $conn->prepare("UPDATE inventory SET quantity = ?, updated_at = NOW() WHERE id = ?");
    $updateStmt->bind_param("ii", $newQuantity, $productId);
    if ($updateStmt->execute()) {
        header("Location: ../pages/inventory.php?msg=Stock reduced successfully");
        exit();
    } else {
        die("Error updating stock: " . $conn->error);
    }
}
?>
