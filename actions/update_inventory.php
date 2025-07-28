<?php
require_once '../includes/db.php';

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$selling_price = $_POST['selling_price'];
$ordering_price = $_POST['ordering_price'];
$add_qty = $_POST['add_quantity'] ?? 0;
$reduce_qty = $_POST['reduce_quantity'] ?? 0;

// Fetch current quantity
$query = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
$query->bind_param("i", $product_id);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$current_qty = $row['quantity'];

$new_qty = $current_qty + intval($add_qty) - intval($reduce_qty);

if ($new_qty <= 0) {
    // Quantity is zero or less: delete the item
    $delete = $conn->prepare("DELETE FROM inventory WHERE id = ?");
    $delete->bind_param("i", $product_id);
    $delete->execute();
} else {
    // Update item
    $stmt = $conn->prepare("UPDATE inventory SET product_name=?, selling_price=?, ordering_price=?, quantity=?, updated_at=NOW() WHERE id=?");
    $stmt->bind_param("sddii", $product_name, $selling_price, $ordering_price, $new_qty, $product_id);
    $stmt->execute();
}

header("Location: ../pages/inventory.php");
exit;
