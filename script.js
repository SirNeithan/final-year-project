// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    // Load featured products on homepage
    loadFeaturedProducts();
    
    // Update cart count when page loads
    updateCartCount();
});

// Function to load featured products
function loadFeaturedProducts() {
    const featuredProducts = [
        
    ];

    const featuredSection = document.getElementById("featured-products");
    if (featuredSection) {
        featuredProducts.forEach(product => {
            const productDiv = document.createElement("div");
            productDiv.className = "menu-item";
            productDiv.innerHTML = `
                <a href="${product.link}" class="menu-card" style="background-image: url('images/${product.image}'); background-size: cover; background-position: center; display: block; height: 150px; border-radius: 8px; text-decoration: none; color: white; display: flex; align-items: center; justify-content: center; position: relative;">
                    <div class="menu-overlay">
                        <h3>${product.name}</h3>
                    </div>
                </a>
            `;
            featuredSection.appendChild(productDiv);
        });
    }
}

// Function to add a product to the cart
function addToCart(productId, productName, productPrice, restaurant) {
    // Send an AJAX request to add the product to the cart
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add_to_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert(`${productName} added to cart!`);
                updateCartCount(); // Update the cart count displayed on the page
            } else {
                alert("Failed to add product to cart.");
            }
        }
    };

    // Send the product ID and restaurant to the server
    xhr.send('product_id=' + productId + '&restaurant=' + encodeURIComponent(restaurant));
}

// Function to update the cart count displayed on the page
function updateCartCount() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "get_cart_count.php", true);

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const cartCountElement = document.getElementById("cart-count");
                if (cartCountElement) {
                    cartCountElement.textContent = response.count;
                }
            }
        }
    };

    xhr.send();
}

// Function to remove a product from the cart
function removeFromCart(productId, restaurant) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "remove_from_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert("Product removed from cart.");
                location.reload(); // Refresh the page to update the cart
            } else {
                alert("Failed to remove product from cart.");
            }
        }
    };

    // Send the product ID and restaurant to the server
    xhr.send('product_id=' + productId + '&restaurant=' + encodeURIComponent(restaurant));
}