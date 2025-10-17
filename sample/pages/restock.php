<?php
require_once '../includes/header.php';
requireRole('manager');

$conn = getDBConnection();
$products = $conn->query("SELECT * FROM products WHERE is_active = 1 ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$conn->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);
    $cost = $_POST['cost'] ?? null;

    $product = getProduct($product_id);
    $newStock = $product['stock'] + $quantity;

    updateProductStock(
        $product_id, 
        $newStock, 
        $_SESSION['user_id'], 
        'restock', 
        $quantity, 
        'Manual restock',
        null
    );

    // If cost is provided, update the product cost
    if ($cost !== null) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE products SET cost = ? WHERE id = ?");
        $stmt->bind_param("di", $cost, $product_id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    header("Location: restock.php?message=Product restocked successfully");
    exit();
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Restock Products</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="mb-8" id="restock-form">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 mb-2" for="product_id">Product</label>
                <select id="product_id" name="product_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Product</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?> (Current: <?php echo $product['stock']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="quantity">Quantity to Add</label>
                <input type="number" id="quantity" name="quantity" min="1" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="cost">New Cost (Optional)</label>
                <input type="number" id="cost" name="cost" step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Restock
                </button>
            </div>
        </div>
    </form>

    <h2 class="text-xl font-bold mb-4">Low Stock Alerts</h2>
    <?php
    $lowStockProducts = getLowStockProducts();
    if (count($lowStockProducts) > 0): 
    ?>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-left">Current Stock</th>
                        <th class="px-4 py-3 text-left">Reorder Level</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockProducts as $product): ?>
                    <tr class="border-t">
                        <td class="px-4 py-3"><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <?php echo $product['stock']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3"><?php echo $product['reorder_level']; ?></td>
                        <td class="px-4 py-3">
                            <button class="quick-restock text-blue-600 hover:text-blue-800" 
                                    data-product-id="<?php echo $product['id']; ?>" 
                                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>">
                                Quick Restock
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500">No low stock alerts at this time.</p>
    <?php endif; ?>
</div>

<!-- Include the inventory management JavaScript -->
<script src="../assets/js/inventory.js"></script>

<?php include '../includes/footer.php'; ?>