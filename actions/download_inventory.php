<?php
require_once '../includes/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="current_inventory.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Output the column headers
fputcsv($output, [
    'Product Name',
    'Category',
    'Quantity in Stock',
    'Selling Price (MWK)',
    'Ordering Price (MWK)',
    'Stock Value (MWK)'
]);

// Fetch inventory where quantity > 0
$sql = "SELECT product_name, category, quantity, selling_price, ordering_price 
        FROM inventory 
        WHERE quantity > 0 
        ORDER BY product_name ASC";

$result = $conn->query($sql);

// Initialize total value
$totalInventoryValue = 0;

// Write data rows
while ($row = $result->fetch_assoc()) {
    // ✅ Use selling price to calculate stock value
    $stockValue = $row['quantity'] * $row['selling_price'];
    $totalInventoryValue += $stockValue;

    fputcsv($output, [
        $row['product_name'],
        $row['category'],
        $row['quantity'],
        number_format($row['selling_price'], 2),
        number_format($row['ordering_price'], 2),
        number_format($stockValue, 2)
    ]);
}

// Add empty row before total
fputcsv($output, []);
// Output total row
fputcsv($output, ['', '', '', '', 'Total Inventory Value:', number_format($totalInventoryValue, 2)]);

fclose($output);
exit;
