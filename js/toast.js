class Toast {
    constructor() {
        this.container = this.createContainer();
    }

    createContainer() {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    show(title, message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon">
                    <i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation'}"></i>
                </div>
                <div class="toast-message">
                    <div class="toast-title">${title}</div>
                    <div class="toast-body">${message}</div>
                </div>
            </div>
            <button class="toast-close">&times;</button>
        `;

        this.container.appendChild(toast);
        
        // Force reflow
        toast.offsetHeight;
        
        // Add show class
        toast.classList.add('show');

        // Close button functionality
        toast.querySelector('.toast-close').addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        });

        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }
}

// Initialize toast
const toast = new Toast();

// Update addToCart function
function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in nav
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.cartCount;
            }
            
            // Show success notification
            toast.show(
                'Added to Cart',
                `${data.productName} has been added to your cart.`
            );
        } else {
            // Show error notification
            toast.show('Error', data.message, 'error');
        }
    })
    .catch(error => {
        toast.show('Error', 'Something went wrong. Please try again.', 'error');
    });
}