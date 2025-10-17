<?php
require_once '../includes/header.php';
requireLogin();
if (!hasRole('cashier') && !hasRole('admin')) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Search and Selection -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Product Search</h2>
            <div class="flex space-x-4 mb-4">
                <input type="text" id="searchInput" placeholder="Search by name, SKU, or barcode..." 
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button id="searchBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>
            
            <div id="searchResults" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2">
                <!-- Search results will appear here -->
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Quick Product Entry</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" id="manualSku" placeholder="Product ID/SKU" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" id="manualPrice" placeholder="Price" step="0.01"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button id="manualAddBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus"></i> Add to Cart
                </button>
            </div>
        </div>
    </div>
    
    <!-- Shopping Cart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Shopping Cart</h2>
        
        <div id="cartItems" class="mb-4 max-h-96 overflow-y-auto">
            <!-- Cart items will appear here -->
            <p class="text-gray-500 text-center py-8">Cart is empty</p>
        </div>
        
        <div class="border-t pt-4">
            <div class="flex justify-between mb-2">
                <span>Subtotal:</span>
                <span id="subtotal">$0.00</span>
            </div>
            <div class="flex justify-between mb-2">
                <span>Tax (8%):</span>
                <span id="tax">$0.00</span>
            </div>
            <div class="flex justify-between mb-2">
                <span>Discount:</span>
                <span id="discount">$0.00</span>
            </div>
            <div class="flex justify-between font-bold text-lg border-t pt-2 mb-4">
                <span>Total:</span>
                <span id="total">$0.00</span>
            </div>
            
            <div class="space-y-3">
                <button id="applyDiscountBtn" class="w-full bg-yellow-600 text-white py-2 rounded-lg hover:bg-yellow-700 transition">
                    Apply Discount
                </button>
                <button id="checkoutBtn" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Checkout
                </button>
                <button id="clearCartBtn" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                    Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 class="text-xl font-bold mb-4">Payment</h3>
        
        <div class="mb-4">
            <div class="flex justify-between mb-2">
                <span>Total Amount:</span>
                <span id="modalTotal">$0.00</span>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Payment Method</label>
                <select id="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="cash">Cash</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="debit_card">Debit Card</option>
                    <option value="mobile_payment">Mobile Payment</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Amount Received</label>
                <input type="number" id="amountReceived" step="0.01" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Change</label>
                <input type="text" id="changeAmount" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
            </div>
        </div>
        
        <div class="flex space-x-3">
            <button id="completeSaleBtn" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                Complete Sale
            </button>
            <button id="cancelPaymentBtn" class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<script src="../assets/js/pos.js"></script>

<?php include '../includes/footer.php'; ?>