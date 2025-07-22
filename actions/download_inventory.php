<?php
include '../includes/db.php';

// Validate date inputs
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

if (!$from || !$to) {
    die("Missing 'from' or 'to' date.");
}

$from .= " 00:00:00";
$to .= " 23:59:59";

// Fetch filtered inventory data
$stmt = $conn->prepare("SELECT product_name, category, quantity, selling_price, ordering_price, updated_at FROM inventory WHERE updated_at BETWEEN ? AND ? ORDER BY updated_at DESC");
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="inventory_' . date('Y-m-d') . '.csv"');

// Output CSV content
$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, ['Product Name', 'Category', 'Quantity', 'Selling Price', 'Ordering Price', 'Updated At']);

// Data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
