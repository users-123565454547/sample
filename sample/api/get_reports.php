<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    
    $salesReport = getSalesReport($startDate, $endDate);
    
    echo json_encode([
        'success' => true,
        'data' => $salesReport,
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);
}
?>