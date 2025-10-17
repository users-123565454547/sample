<?php
// Remove session_start() from here since it's already in auth.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php'; // Add this line to include functions
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php if (isLoggedIn()): ?>
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold">POS System</h1>
                    <div class="flex space-x-2">
                        <a href="dashboard.php" class="px-3 py-2 rounded hover:bg-blue-700 transition">Dashboard</a>
                        <?php if (hasRole('cashier') || hasRole('admin')): ?>
                        <a href="pos.php" class="px-3 py-2 rounded hover:bg-blue-700 transition">POS</a>
                        <?php endif; ?>
                        <?php if (hasRole('manager') || hasRole('admin')): ?>
                        <a href="products.php" class="px-3 py-2 rounded hover:bg-blue-700 transition">Products</a>
                        <a href="restock.php" class="px-3 py-2 rounded hover:bg-blue-700 transition">Restock</a>
                        <a href="reports.php" class="px-3 py-2 rounded hover:bg-blue-700 transition">Reports</a>
                        <?php endif; ?>
                        <?php if (hasRole('admin')): ?>
                        <a href="users.php" class="px-3 py-2 rounded hover:bg-blue-700 transition">Users</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span>Welcome, <?php echo htmlspecialchars($currentUser['full_name'] ?? 'User'); ?> (<?php echo ucfirst($currentUser['role'] ?? 'unknown'); ?>)</span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    <main class="container mx-auto px-4 py-6">