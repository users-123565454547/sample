class POSSystem {
    constructor() {
        this.cart = [];
        this.taxRate = 0.08; // 8%
        this.discount = 0;
        
        this.initializeEventListeners();
    }
    
    initializeEventListeners() {
        // Search functionality
        document.getElementById('searchBtn').addEventListener('click', () => this.searchProducts());
        document.getElementById('searchInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.searchProducts();
        });
        
        // Manual product entry
        document.getElementById('manualAddBtn').addEventListener('click', () => this.addManualProduct());
        
        // Cart actions
        document.getElementById('applyDiscountBtn').addEventListener('click', () => this.applyDiscount());
        document.getElementById('checkoutBtn').addEventListener('click', () => this.openPaymentModal());
        document.getElementById('clearCartBtn').addEventListener('click', () => this.clearCart());
        
        // Payment modal
        document.getElementById('amountReceived').addEventListener('input', () => this.calculateChange());
        document.getElementById('cancelPaymentBtn').addEventListener('click', () => this.closePaymentModal());
        document.getElementById('completeSaleBtn').addEventListener('click', () => this.completeSale());
    }
    
    async searchProducts() {
        const query = document.getElementById('searchInput').value.trim();
        if (!query) return;
        
        try {
            const response = await fetch(`../api/search_products.php?query=${encodeURIComponent(query)}`);
            const products = await response.json();
            this.displaySearchResults(products);
        } catch (error) {
            console.error('Search error:', error);
        }
    }
    
    displaySearchResults(products) {
        const resultsContainer = document.getElementById('searchResults');
        
        if (products.length === 0) {
            resultsContainer.innerHTML = '<p class="text-gray-500 text-center col-span-3 py-8">No products found</p>';
            return;
        }
        
        resultsContainer.innerHTML = products.map(product => `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition cursor-pointer" 
                 onclick="pos.addToCart(${product.id}, '${product.name}', ${product.price}, ${product.stock})">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-lg">${product.name}</h3>
                    <span class="text-green-600 font-bold">$${parseFloat(product.price).toFixed(2)}</span>
                </div>
                <p class="text-gray-600 text-sm mb-2">${product.sku}</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Stock: ${product.stock}</span>
                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                        Add to Cart
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    addToCart(productId, productName, price, stock) {
        if (stock <= 0) {
            alert('Product out of stock!');
            return;
        }
        
        const existingItem = this.cart.find(item => item.id === productId);
        
        if (existingItem) {
            if (existingItem.quantity >= stock) {
                alert('Not enough stock available!');
                return;
            }
            existingItem.quantity++;
            existingItem.subtotal = existingItem.quantity * existingItem.price;
        } else {
            this.cart.push({
                id: productId,
                name: productName,
                price: parseFloat(price),
                quantity: 1,
                subtotal: parseFloat(price)
            });
        }
        
        this.updateCartDisplay();
    }
    
    addManualProduct() {
        const sku = document.getElementById('manualSku').value.trim();
        const price = parseFloat(document.getElementById('manualPrice').value);
        
        if (!sku || isNaN(price) || price <= 0) {
            alert('Please enter valid product ID and price');
            return;
        }
        
        this.cart.push({
            id: 0,
            name: `Manual Item (${sku})`,
            price: price,
            quantity: 1,
            subtotal: price,
            isManual: true
        });
        
        document.getElementById('manualSku').value = '';
        document.getElementById('manualPrice').value = '';
        this.updateCartDisplay();
    }
    
    updateCartDisplay() {
        const cartContainer = document.getElementById('cartItems');
        
        if (this.cart.length === 0) {
            cartContainer.innerHTML = '<p class="text-gray-500 text-center py-8">Cart is empty</p>';
        } else {
            cartContainer.innerHTML = this.cart.map((item, index) => `
                <div class="flex justify-between items-center border-b pb-3 mb-3">
                    <div class="flex-1">
                        <h4 class="font-semibold">${item.name}</h4>
                        <div class="flex items-center space-x-2 mt-1">
                            <button onclick="pos.updateQuantity(${index}, -1)" class="bg-gray-200 w-6 h-6 rounded flex items-center justify-center hover:bg-gray-300">
                                -
                            </button>
                            <span class="w-8 text-center">${item.quantity}</span>
                            <button onclick="pos.updateQuantity(${index}, 1)" class="bg-gray-200 w-6 h-6 rounded flex items-center justify-center hover:bg-gray-300">
                                +
                            </button>
                            <span class="text-gray-600 ml-2">@ $${item.price.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold">$${item.subtotal.toFixed(2)}</div>
                        <button onclick="pos.removeFromCart(${index})" class="text-red-600 hover:text-red-800 text-sm mt-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }
        
        this.calculateTotals();
    }
    
    updateQuantity(index, change) {
        const item = this.cart[index];
        const newQuantity = item.quantity + change;
        
        if (newQuantity <= 0) {
            this.removeFromCart(index);
            return;
        }
        
        // For non-manual items, check stock (we'd need to fetch current stock)
        if (!item.isManual && newQuantity > item.stock) {
            alert('Not enough stock available!');
            return;
        }
        
        item.quantity = newQuantity;
        item.subtotal = item.quantity * item.price;
        this.updateCartDisplay();
    }
    
    removeFromCart(index) {
        this.cart.splice(index, 1);
        this.updateCartDisplay();
    }
    
    calculateTotals() {
        const subtotal = this.cart.reduce((sum, item) => sum + item.subtotal, 0);
        const tax = subtotal * this.taxRate;
        const total = subtotal + tax - this.discount;
        
        document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
        document.getElementById('discount').textContent = `$${this.discount.toFixed(2)}`;
        document.getElementById('total').textContent = `$${total.toFixed(2)}`;
    }
    
    applyDiscount() {
        const discountAmount = prompt('Enter discount amount ($):');
        if (discountAmount !== null) {
            const amount = parseFloat(discountAmount);
            if (!isNaN(amount) && amount >= 0) {
                const subtotal = this.cart.reduce((sum, item) => sum + item.subtotal, 0);
                this.discount = Math.min(amount, subtotal);
                this.updateCartDisplay();
            } else {
                alert('Please enter a valid discount amount');
            }
        }
    }
    
    openPaymentModal() {
        if (this.cart.length === 0) {
            alert('Cart is empty!');
            return;
        }
        
        const total = this.cart.reduce((sum, item) => sum + item.subtotal, 0) * (1 + this.taxRate) - this.discount;
        document.getElementById('modalTotal').textContent = `$${total.toFixed(2)}`;
        document.getElementById('paymentModal').classList.remove('hidden');
        document.getElementById('paymentModal').classList.add('flex');
        document.getElementById('amountReceived').value = '';
        document.getElementById('changeAmount').value = '';
    }
    
    closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentModal').classList.remove('flex');
    }
    
    calculateChange() {
        const total = parseFloat(document.getElementById('modalTotal').textContent.replace('$', ''));
        const amountReceived = parseFloat(document.getElementById('amountReceived').value) || 0;
        const change = amountReceived - total;
        
        document.getElementById('changeAmount').value = change >= 0 ? `$${change.toFixed(2)}` : 'Insufficient';
    }
    
    async completeSale() {
        const total = parseFloat(document.getElementById('modalTotal').textContent.replace('$', ''));
        const amountReceived = parseFloat(document.getElementById('amountReceived').value) || 0;
        const paymentMethod = document.getElementById('paymentMethod').value;
        
        if (amountReceived < total) {
            alert('Amount received is less than total amount!');
            return;
        }
        
        try {
            const response = await fetch('../api/checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart: this.cart,
                    subtotal: this.cart.reduce((sum, item) => sum + item.subtotal, 0),
                    tax: this.cart.reduce((sum, item) => sum + item.subtotal, 0) * this.taxRate,
                    discount: this.discount,
                    total: total,
                    paymentMethod: paymentMethod,
                    amountReceived: amountReceived
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Sale completed successfully! Transaction ID: ' + result.transaction_code);
                this.clearCart();
                this.closePaymentModal();
            } else {
                alert('Error completing sale: ' + result.message);
            }
        } catch (error) {
            console.error('Checkout error:', error);
            alert('Error completing sale');
        }
    }
    
    clearCart() {
        this.cart = [];
        this.discount = 0;
        this.updateCartDisplay();
    }
}

// Initialize POS system when page loads
const pos = new POSSystem();