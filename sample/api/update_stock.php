<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user = getCurrentUser();
    
    if (!$user || ($user['role'] !== 'manager' && $user['role'] !== 'admin')) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $productId = $input['product_id'];
    $newStock = $input['new_stock'];
    $reason = $input['reason'] ?? '';
    
    try {
        $product = getProduct($productId);
        $previousStock = $product['stock'];
        $changeQuantity = $newStock - $previousStock;
        $changeType = $changeQuantity > 0 ? 'restock' : 'adjustment';
        
        updateProductStock(
            $productId, 
            $newStock, 
            $user['id'], 
            $changeType, 
            $changeQuantity, 
            $reason,
            null
        );
        
        echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>