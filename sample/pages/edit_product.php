<?php
require_once '../includes/header.php';
requireRole('manager');

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);
$conn = getDBConnection();
$product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
$conn->close();

if (!$product) {
    header("Location: products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $sku = $_POST['sku'];
    $barcode = $_POST['barcode'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $cost = $_POST['cost'];
    $reorder_level = $_POST['reorder_level'];

    $conn = getDBConnection();
    $stmt = $conn->prepare("
        UPDATE products 
        SET name = ?, description = ?, sku = ?, barcode = ?, category_id = ?, price = ?, cost = ?, reorder_level = ? 
        WHERE id = ?
    ");
    $stmt->bind_param(
        "ssssiddii", 
        $name, $description, $sku, $barcode, $category_id, $price, $cost, $reorder_level, $product_id
    );

    if ($stmt->execute()) {
        header("Location: products.php?message=Product updated successfully");
        exit();
    } else {
        $error = "Error updating product: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Edit Product</h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-gray-700 mb-2" for="name">Product Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-700 mb-2" for="description">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="sku">SKU</label>
                <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="barcode">Barcode</label>
                <input type="text" id="barcode" name="barcode" value="<?php echo htmlspecialchars($product['barcode']); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="category_id">Category</label>
                <select id="category_id" name="category_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $product['price']; ?>" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="cost">Cost</label>
                <input type="number" id="cost" name="cost" step="0.01" value="<?php echo $product['cost']; ?>" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 mb-2" for="reorder_level">Reorder Level</label>
                <input type="number" id="reorder_level" name="reorder_level" value="<?php echo $product['reorder_level']; ?>" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="products.php" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Update Product</button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>