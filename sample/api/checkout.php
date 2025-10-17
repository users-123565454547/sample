<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user = getCurrentUser();
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    
    $conn = getDBConnection();
    $conn->begin_transaction();
    
    try {
        // Create transaction
        $transactionCode = generateTransactionCode();
        $stmt = $conn->prepare("
            INSERT INTO transactions (transaction_code, user_id, subtotal, tax, discount, total, payment_method, amount_received, change_amount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $changeAmount = $input['amountReceived'] - $input['total'];
        $stmt->bind_param(
            "sidddddsd", 
            $transactionCode, 
            $user['id'],
            $input['subtotal'],
            $input['tax'],
            $input['discount'],
            $input['total'],
            $input['paymentMethod'],
            $input['amountReceived'],
            $changeAmount
        );
        $stmt->execute();
        $transactionId = $conn->insert_id;
        $stmt->close();
        
        // Add transaction items and update inventory
        foreach ($input['cart'] as $item) {
            if (!$item['isManual']) {
                // Add transaction item
                $stmt = $conn->prepare("
                    INSERT INTO transaction_items (transaction_id, product_id, quantity, price, subtotal) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "iiidd", 
                    $transactionId, 
                    $item['id'],
                    $item['quantity'],
                    $item['price'],
                    $item['subtotal']
                );
                $stmt->execute();
                $stmt->close();
                
                // Update product stock
                $product = getProduct($item['id']);
                $newStock = $product['stock'] - $item['quantity'];
                
                updateProductStock(
                    $item['id'], 
                    $newStock, 
                    $user['id'], 
                    'sale', 
                    -$item['quantity'], 
                    'Sale - Transaction ' . $transactionCode,
                    $transactionId
                );
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'transaction_code' => $transactionCode]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    $conn->close();
}
?>