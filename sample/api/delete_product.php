<?php
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $user = getCurrentUser();
    
    if (!$user || ($user['role'] !== 'manager' && $user['role'] !== 'admin')) {
        header("Location: ../pages/products.php?error=Unauthorized");
        exit;
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        header("Location: ../pages/products.php?message=Product deleted successfully");
    } else {
        header("Location: ../pages/products.php?error=Error deleting product");
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../pages/products.php");
}
?>