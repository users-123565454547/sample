<?php
require_once '../includes/functions.php';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d') . '.xls"');

$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$salesReport = getSalesReport($startDate, $endDate);

echo "Sales Report from $startDate to $endDate\n\n";
echo "Date\tTransactions\tSubtotal\tTax\tDiscount\tTotal Sales\n";

foreach ($salesReport as $day) {
    echo $day['date'] . "\t";
    echo $day['transaction_count'] . "\t";
    echo number_format($day['subtotal'], 2) . "\t";
    echo number_format($day['tax'], 2) . "\t";
    echo number_format($day['discount'], 2) . "\t";
    echo number_format($day['total_sales'], 2) . "\n";
}
?>