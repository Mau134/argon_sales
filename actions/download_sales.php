<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_' . $from . '_to_' . $to . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Product Name', 'Total Quantity Sold', 'Total Revenue (MWK)', 'Total Cost (MWK)', 'Profit (MWK)']);
$sql = "SELECT 
            i.product_name,
            SUM(s.quantity_sold) AS total_sold,
            SUM(s.quantity_sold * i.selling_price) AS total_revenue,
            SUM(s.quantity_sold * i.ordering_price) AS total_cost,
            SUM((i.selling_price - i.ordering_price) * s.quantity_sold) AS profit
        FROM sales s
        JOIN inventory i ON s.product_id = i.id
        WHERE s.sale_date BETWEEN ? AND ?
        GROUP BY s.product_id";



    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    $result = $stmt->get_result();

    $grandProfit = 0;

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['product_name'],
            $row['total_sold'],
            number_format($row['total_revenue'], 2),
            number_format($row['total_cost'], 2),
            number_format($row['profit'], 2)
        ]);
        $grandProfit += $row['profit'];
    }
    fputcsv($output, []); // Empty line for spacing
    fputcsv($output, ['Total Profit (MWK)', '', '', '', number_format($grandProfit, 2)]);

    fclose($output);

    $_SESSION['total_profit'] = $grandProfit;
    exit;
}
?>
