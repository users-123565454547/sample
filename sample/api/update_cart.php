<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // This would handle cart updates if needed
    // For now, we'll just return success
    echo json_encode(['success' => true]);
}
?>