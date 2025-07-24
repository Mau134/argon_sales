<?php
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = $_POST['product_id'];
    $product_name = trim($_POST['product_name']);
    $selling_price = floatval($_POST['selling_price']);
    $ordering_price = floatval($_POST['ordering_price']);
    $add_qty = isset($_POST['add_quantity']) ? intval($_POST['add_quantity']) : 0;
    $reduce_qty = isset($_POST['reduce_quantity']) ? intval($_POST['reduce_quantity']) : 0;

    // Get current quantity
    $sql = "SELECT quantity FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($current_qty);
    $stmt->fetch();
    $stmt->close();

    // Calculate new quantity
    $new_qty = $current_qty + $add_qty - $reduce_qty;
    if ($new_qty < 0) $new_qty = 0; // Prevent negative stock

    // Update inventory
    $update_sql = "UPDATE inventory 
                   SET product_name = ?, selling_price = ?, ordering_price = ?, quantity = ?, updated_at = NOW() 
                   WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sddii", $product_name, $selling_price, $ordering_price, $new_qty, $product_id);
    
    if ($update_stmt->execute()) {
        header("Location: ../pages/inventory.php?success=1");
        exit();
    } else {
        echo "❌ Error updating inventory: " . $conn->error;
    }

    $update_stmt->close();
    $conn->close();
}
?>
