<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name']);
    $category = trim($_POST['category']);
    $quantity = intval($_POST['quantity']);
    $selling_price = floatval($_POST['selling_price']);
    $ordering_price = floatval($_POST['ordering_price']);

    // Check if the product with the same name and category exists
    $stmt = $conn->prepare("SELECT id, quantity FROM inventory WHERE product_name = ? AND category = ?");
    $stmt->bind_param("ss", $product_name, $category);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Product exists - update quantity and prices
        $stmt->bind_result($existing_id, $existing_quantity);
        $stmt->fetch();
        $new_quantity = $existing_quantity + $quantity;

        $update = $conn->prepare("UPDATE inventory SET quantity = ?, selling_price = ?, ordering_price = ?, updated_at = NOW() WHERE id = ?");
        $update->bind_param("dddi", $new_quantity, $selling_price, $ordering_price, $existing_id);
        $update->execute();
        $update->close();
    } else {
        // Product doesn't exist - insert new row
        $insert = $conn->prepare("INSERT INTO inventory (product_name, category, quantity, selling_price, ordering_price, updated_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $insert->bind_param("ssidd", $product_name, $category, $quantity, $selling_price, $ordering_price);
        $insert->execute();
        $insert->close();
    }

    $stmt->close();
    header("Location: ../pages/inventory.php?msg=Stock updated successfully");
    exit();
}
?>
