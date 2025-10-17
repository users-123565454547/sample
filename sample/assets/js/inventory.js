// JavaScript for inventory management features
class InventoryManager {
    constructor() {
        this.initializeEventListeners();
    }
    
    initializeEventListeners() {
        // Quick restock functionality
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-restock')) {
                const productId = e.target.dataset.productId;
                const productName = e.target.dataset.productName;
                this.quickRestock(productId, productName);
            }
        });
    }
    
    quickRestock(productId, productName) {
        document.getElementById('product_id').value = productId;
        document.getElementById('quantity').focus();
        
        // Scroll to the restock form
        const restockForm = document.getElementById('restock-form');
        if (restockForm) {
            restockForm.scrollIntoView({ 
                behavior: 'smooth' 
            });
        }
    }
    
    async updateProductStock(productId, newStock, reason = '') {
        try {
            const response = await fetch('../api/update_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    new_stock: newStock,
                    reason: reason
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                return true;
            } else {
                console.error('Stock update failed:', result.message);
                return false;
            }
        } catch (error) {
            console.error('Error updating stock:', error);
            return false;
        }
    }
}

// Initialize inventory manager when page loads
document.addEventListener('DOMContentLoaded', function() {
    const inventoryManager = new InventoryManager();
});