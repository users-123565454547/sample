<?php
// functions.php - General utility functions

// Include database connection but don't redefine the function
require_once __DIR__ . '/../config/database.php';

// Make sure formatCurrency is always available
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('generateTransactionCode')) {
    function generateTransactionCode() {
        return 'TXN' . date('YmdHis') . rand(100, 999);
    }
}

if (!function_exists('getProduct')) {
    function getProduct($id) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $product;
    }
}

if (!function_exists('searchProducts')) {
    function searchProducts($query) {
        $conn = getDBConnection();
        $search = "%$query%";
        $stmt = $conn->prepare("
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?) AND p.is_active = 1
        ");
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $products;
    }
}

if (!function_exists('updateProductStock')) {
    function updateProductStock($productId, $newStock, $userId, $changeType, $changeQuantity, $reason = '', $referenceId = null) {
        $conn = getDBConnection();
        
        // Get current stock
        $product = getProduct($productId);
        $previousStock = $product['stock'];
        
        // Update product stock
        $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $newStock, $productId);
        $stmt->execute();
        
        // Log inventory change
        $stmt = $conn->prepare("
            INSERT INTO inventory_logs (product_id, change_type, change_quantity, previous_stock, new_stock, reason, user_id, reference_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isiiisii", $productId, $changeType, $changeQuantity, $previousStock, $newStock, $reason, $userId, $referenceId);
        $stmt->execute();
        
        $stmt->close();
        $conn->close();
    }
}

if (!function_exists('getLowStockProducts')) {
    function getLowStockProducts() {
        $conn = getDBConnection();
        $result = $conn->query("
            SELECT * FROM products 
            WHERE stock <= reorder_level AND is_active = 1
            ORDER BY stock ASC
        ");
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();
        return $products;
    }
}

if (!function_exists('getSalesReport')) {
    function getSalesReport($startDate, $endDate) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("
            SELECT 
                DATE(transaction_date) as date,
                COUNT(*) as transaction_count,
                COALESCE(SUM(total), 0) as total_sales,
                COALESCE(SUM(subtotal), 0) as subtotal,
                COALESCE(SUM(tax), 0) as tax,
                COALESCE(SUM(discount), 0) as discount
            FROM transactions 
            WHERE DATE(transaction_date) BETWEEN ? AND ?
            GROUP BY DATE(transaction_date)
            ORDER BY date DESC
        ");
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $report = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $report;
    }
}
?>