<?php
require_once '../includes/header.php';
requireLogin();

// Get dashboard stats
$conn = getDBConnection();
$today = date('Y-m-d');

// Today's sales
$stmt = $conn->prepare("SELECT COUNT(*) as count, COALESCE(SUM(total), 0) as total FROM transactions WHERE DATE(transaction_date) = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$todaySales = $stmt->get_result()->fetch_assoc();

// Low stock count
$lowStockResult = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock <= reorder_level AND is_active = 1");
$lowStockCount = $lowStockResult->fetch_assoc();

// Total products
$productResult = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
$productCount = $productResult->fetch_assoc();

$stmt->close();
$conn->close();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-600">Today's Sales</h2>
                <p class="text-2xl font-bold"><?php echo formatCurrency($todaySales['total']); ?></p>
                <p class="text-sm text-gray-500"><?php echo $todaySales['count']; ?> transactions</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-600">Low Stock Items</h2>
                <p class="text-2xl font-bold"><?php echo $lowStockCount['count']; ?></p>
                <p class="text-sm text-gray-500">Need restocking</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-boxes text-xl"></i>
            </div>
            <div class="ml-4">
                <h2 class="text-lg font-semibold text-gray-600">Total Products</h2>
                <p class="text-2xl font-bold"><?php echo $productCount['count']; ?></p>
                <p class="text-sm text-gray-500">Active in inventory</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-4">
            <?php if (hasRole('cashier') || hasRole('admin')): ?>
            <a href="pos.php" class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700 transition">
                <i class="fas fa-cash-register text-2xl mb-2"></i>
                <p>Point of Sale</p>
            </a>
            <?php endif; ?>
            
            <?php if (hasRole('manager') || hasRole('admin')): ?>
            <a href="products.php" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700 transition">
                <i class="fas fa-boxes text-2xl mb-2"></i>
                <p>Manage Products</p>
            </a>
            <a href="restock.php" class="bg-yellow-600 text-white p-4 rounded-lg text-center hover:bg-yellow-700 transition">
                <i class="fas fa-truck-loading text-2xl mb-2"></i>
                <p>Restock Inventory</p>
            </a>
            <a href="reports.php" class="bg-purple-600 text-white p-4 rounded-lg text-center hover:bg-purple-700 transition">
                <i class="fas fa-chart-bar text-2xl mb-2"></i>
                <p>View Reports</p>
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4">Low Stock Alerts</h2>
        <?php
        $lowStockProducts = getLowStockProducts();
        if (count($lowStockProducts) > 0): 
        ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Product</th>
                            <th class="px-4 py-2 text-left">Current Stock</th>
                            <th class="px-4 py-2 text-left">Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowStockProducts as $product): ?>
                        <tr class="border-t">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?php echo $product['stock'] == 0 ? 'bg-red-100 text-red-800' : 
                                           ($product['stock'] <= 2 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-2"><?php echo $product['reorder_level']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500">No low stock alerts at this time.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>