<?php
require_once '../includes/header.php';
requireRole('manager');

// Get all products
$conn = getDBConnection();
$result = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.is_active = 1
    ORDER BY p.name
");
$products = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Product Management</h1>
        <a href="add_product.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Price</th>
                    <th class="px-4 py-3 text-left">Cost</th>
                    <th class="px-4 py-3 text-left">Stock</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-semibold"><?php echo htmlspecialchars($product['name']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($product['description']); ?></div>
                    </td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($product['sku']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($product['category_name']); ?></td>
                    <td class="px-4 py-3 font-semibold"><?php echo formatCurrency($product['price']); ?></td>
                    <td class="px-4 py-3"><?php echo formatCurrency($product['cost']); ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            <?php echo $product['stock'] == 0 ? 'bg-red-100 text-red-800' : 
                                   ($product['stock'] <= $product['reorder_level'] ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'); ?>">
                            <?php echo $product['stock']; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                               class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="../api/delete_product.php?id=<?php echo $product['id']; ?>" 
                               class="text-red-600 hover:text-red-800" 
                               onclick="return confirm('Are you sure you want to delete this product?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>