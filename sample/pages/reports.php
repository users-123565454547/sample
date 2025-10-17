<?php
require_once '../includes/header.php';
requireRole('manager');

// Default date range (last 30 days)
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$salesReport = getSalesReport($startDate, $endDate);
$totalSales = array_sum(array_column($salesReport, 'total_sales'));
$totalTransactions = array_sum(array_column($salesReport, 'transaction_count'));
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Sales Reports</h1>

    <!-- Date Range Filter -->
    <form method="GET" action="" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 mb-2" for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-gray-700 mb-2" for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Generate Report
                </button>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800">Total Sales</h3>
            <p class="text-2xl font-bold text-blue-600"><?php echo formatCurrency($totalSales); ?></p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-green-800">Total Transactions</h3>
            <p class="text-2xl font-bold text-green-600"><?php echo $totalTransactions; ?></p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-purple-800">Average Transaction</h3>
            <p class="text-2xl font-bold text-purple-600">
                <?php echo $totalTransactions > 0 ? formatCurrency($totalSales / $totalTransactions) : '$0.00'; ?>
            </p>
        </div>
    </div>

    <!-- Sales Report Table -->
    <h2 class="text-xl font-bold mb-4">Daily Sales</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Transactions</th>
                    <th class="px-4 py-3 text-left">Subtotal</th>
                    <th class="px-4 py-3 text-left">Tax</th>
                    <th class="px-4 py-3 text-left">Discount</th>
                    <th class="px-4 py-3 text-left">Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesReport as $day): ?>
                <tr class="border-t">
                    <td class="px-4 py-3"><?php echo $day['date']; ?></td>
                    <td class="px-4 py-3"><?php echo $day['transaction_count']; ?></td>
                    <td class="px-4 py-3"><?php echo formatCurrency($day['subtotal']); ?></td>
                    <td class="px-4 py-3"><?php echo formatCurrency($day['tax']); ?></td>
                    <td class="px-4 py-3"><?php echo formatCurrency($day['discount']); ?></td>
                    <td class="px-4 py-3 font-semibold"><?php echo formatCurrency($day['total_sales']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Export Button -->
    <div class="mt-6">
        <a href="../api/export_report.php?start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
            <i class="fas fa-download"></i> Export to Excel
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>