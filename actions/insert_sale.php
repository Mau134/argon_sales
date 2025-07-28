<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $productId = $_POST['product_id'];
    $manualSellingPrice = floatval($_POST['price']);
    $quantitySold = intval($_POST['quantity_sold']);

    // Fetch system values
    $stmt = $conn->prepare("SELECT quantity, selling_price, ordering_price FROM inventory WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($stock, $systemSellingPrice, $orderingPrice);
    $stmt->fetch();
    $stmt->close();

    // ❌ If stock is 0, deny sale
    if ($stock <= 0) {
        header("Location: ../pages/sales.php?error=out_of_stock");
        exit();
    }

    // ❌ If trying to sell more than in stock, deny
    if ($stock < $quantitySold) {
        header("Location: ../pages/sales.php?error=not_enough_stock");
        exit();
    }

    // ❌ If price entered is too high
    if ($manualSellingPrice > $systemSellingPrice) {
        header("Location: ../pages/sales.php?error=price_too_high");
        exit();
    }

    // ✅ Proceed with sale
    $totalAmount = $manualSellingPrice * $quantitySold;

    $stmt = $conn->prepare("INSERT INTO sales (product_id, total_amount, quantity_sold, selling_price, ordering_price, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiddi", $productId, $totalAmount, $quantitySold, $manualSellingPrice, $orderingPrice);
    $stmt->execute();
    $stmt->close();

    // ✅ Update stock (no auto delete)
    $newStock = $stock - $quantitySold;
    $stmt = $conn->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $newStock, $productId);
    $stmt->execute();
    $stmt->close();

    header("Location: ../pages/sales.php?success=1");
    exit();
}
?>
