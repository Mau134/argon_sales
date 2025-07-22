<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productId = $_POST['product_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity_sold'];
    $totalAmount = $price * $quantity;

    // Step 1: Check current inventory
    $inventoryCheck = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
    $inventoryCheck->bind_param("i", $productId);
    $inventoryCheck->execute();
    $inventoryCheck->bind_result($currentStock);
    $inventoryCheck->fetch();
    $inventoryCheck->close();

    if ($currentStock === null) {
        // Product not found
        die("Invalid product ID.");
    }

    if ($currentStock <= 0) {
        // Out of stock
        header("Location: ../pages/sales.php?error=out_of_stock");
        exit();
    }

    if ($quantity > $currentStock) {
        // Not enough stock
        header("Location: ../pages/sales.php?error=not_enough_stock");
        exit();
    }

    // Step 2: Insert sale
    $stmt = $conn->prepare("INSERT INTO sales (product_id, quantity_sold, total_amount, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iid", $productId, $quantity, $totalAmount);

    if ($stmt->execute()) {
        // Step 3: Update inventory
        $update = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
        $update->bind_param("ii", $quantity, $productId);
        $update->execute();
        $update->close();

        header("Location: ../pages/sales.php?success=1");
        exit();
    } else {
        echo "Error recording sale: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
