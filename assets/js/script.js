/**
 * Smart Dine - Main JavaScript File
 * 
 * This file contains all the core JavaScript functionality for the Smart Dine application:
 * - Image handling and fallbacks
 * - Cart operations (add, remove, update count)
 * - Product grid layout
 * - Featured products loading
 * 
 * All functions use vanilla JavaScript (no jQuery) for better performance
 */

// ============================================================================
// IMAGE HANDLING CONSTANTS AND FUNCTIONS
// ============================================================================

/**
 * Base path for food images
 * All product images are stored in this directory
 */
const FOOD_IMAGE_BASE = "assets/images/food pics/";

/**
 * Placeholder image (SVG) shown when actual image is missing or broken
 * This is an inline SVG data URL to avoid additional HTTP requests
 */
const PLACEHOLDER_IMAGE =
    "data:image/svg+xml;utf8," +
    encodeURIComponent(
        `<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300">
            <rect width="100%" height="100%" fill="#f2f2f2"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#999" font-family="Arial" font-size="20">
                No Image
            </text>
        </svg>`
    );

/**
 * Build a complete URL for a food image
 * 
 * @param {string} imageName - The filename of the image
 * @returns {string} Complete image URL or placeholder if invalid
 */
function buildFoodImageUrl(imageName) {
    // Return placeholder if image name is empty or invalid
    if (!imageName || String(imageName).trim() === "") return PLACEHOLDER_IMAGE;
    
    // Build and return the complete image URL
    return FOOD_IMAGE_BASE + encodeURIComponent(String(imageName).trim());
}

// ============================================================================
// PAGE INITIALIZATION
// ============================================================================

/**
 * Initialize the page when DOM is fully loaded
 * This ensures all HTML elements are available before we try to manipulate them
 */
document.addEventListener("DOMContentLoaded", function () {
    // Load featured products on homepage
    loadFeaturedProducts();

    // Update cart count badge in navigation
    updateCartCount();

    // Apply fallback images for any broken/missing images
    applyMissingImageFallbacks();

    // Force product listings into a 3-column grid layout
    applyThreeColumnGrid();

    // Bind add-to-cart buttons rendered with data attributes.
    bindAddToCartButtons();
});

// ============================================================================
// FEATURED PRODUCTS
// ============================================================================

/**
 * Load and display featured products on the homepage
 * This function dynamically creates product cards for featured items
 */
function loadFeaturedProducts() {
    // Array of featured products (currently empty - can be populated from database)
    const featuredProducts = [
        // Example: { name: "Pizza", image: "pizza.jpg", link: "pages/categories/main-courses.php" }
    ];

    // Find the featured products container
    const featuredSection = document.getElementById("featured-products");
    
    if (featuredSection) {
        // Create a card for each featured product
        featuredProducts.forEach(product => {
            const productDiv = document.createElement("div");
            productDiv.className = "menu-item";
            const imageUrl = buildFoodImageUrl(product.image);

            // Create product card with image background
            productDiv.innerHTML = `
                <a href="${product.link}" class="menu-card" style="background-image: url('${imageUrl}'); background-size: cover; background-position: center; display: block; height: 150px; border-radius: 8px; text-decoration: none; color: white; display: flex; align-items: center; justify-content: center; position: relative;">
                    <div class="menu-overlay">
                        <h3>${product.name}</h3>
                    </div>
                </a>
            `;
            featuredSection.appendChild(productDiv);
        });
    }
}

// ============================================================================
// IMAGE FALLBACK HANDLING
// ============================================================================

/**
 * Apply fallback images to all <img> tags with missing or broken sources
 * This prevents broken image icons from showing on the page
 */
function applyMissingImageFallbacks() {
    // Loop through all images on the page
    document.querySelectorAll("img").forEach((img) => {
        const src = (img.getAttribute("src") || "").trim();

        // Check if source is empty or contains invalid values like "undefined" or "null"
        if (!src || /(?:undefined|null)\b/i.test(src)) {
            img.src = PLACEHOLDER_IMAGE;
        }

        // Set up error handler for images that fail to load
        img.onerror = function () {
            this.onerror = null; // Prevent infinite loop if placeholder also fails
            this.src = PLACEHOLDER_IMAGE;
        };
    });
}

// ============================================================================
// CART OPERATIONS
// ============================================================================

/**
 * Add a product to the shopping cart
 * 
 * @param {number} productId - The ID of the product to add
 * @param {string} productName - The name of the product (for confirmation message)
 * @param {string} productPrice - The price of the product
 * @param {string} restaurant - The restaurant name (defaults to 'Smart Dine')
 */
function addToCart(productId, productName, productPrice, restaurant = 'Smart Dine') {
    const base = window.BASE_PATH || '/';
    const url = base + 'api/add_to_cart.php';
    const xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showNotification(productName + ' added to cart!');
                        updateCartCount();
                    } else {
                        showNotification(response.message || "Already in cart.");
                    }
                } catch (e) {
                    console.error("Cart parse error:", e, xhr.responseText);
                    alert("Cart error — check console. URL tried: " + url);
                }
            } else {
                console.error("Cart HTTP error:", xhr.status, url);
                alert("Cart request failed (HTTP " + xhr.status + "). URL: " + url);
            }
        }
    };

    xhr.send('product_id=' + productId + '&restaurant=' + encodeURIComponent(restaurant));
}

/**
 * Update the cart count badge in the navigation
 * This function is called after adding/removing items from cart
 */
function updateCartCount() {
    const base = window.BASE_PATH || '/';
    const xhr = new XMLHttpRequest();
    xhr.open("GET", base + "api/get_cart_count.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                // Update the cart count badge
                const cartCountElement = document.getElementById("cart-count");
                if (cartCountElement) {
                    cartCountElement.textContent = response.count;
                }
            }
        }
    };
    xhr.send();
}

/**
 * Bind click handling for add-to-cart buttons.
 * Using data attributes avoids fragile inline JavaScript in HTML attributes.
 */
function bindAddToCartButtons() {
    document.addEventListener("click", function (event) {
        const button = event.target.closest("[data-add-to-cart]");
        if (!button) return;

        event.preventDefault();

        const productId = parseInt(button.dataset.productId || "0", 10);
        const productName = button.dataset.productName || "Item";
        const productPrice = button.dataset.productPrice || "";
        const restaurant = button.dataset.restaurant || "Smart Dine";

        if (!productId) {
            console.error("Add to cart aborted: missing product id", button.dataset);
            return;
        }

        addToCart(productId, productName, productPrice, restaurant);
    });
}

/**
 * Remove a product from the shopping cart
 * 
 * @param {number} productId - The ID of the product to remove
 * @param {string} restaurant - The restaurant name
 */
function removeFromCart(productId, restaurant) {
    const base = window.BASE_PATH || '/';
    const xhr = new XMLHttpRequest();
    xhr.open("POST", base + 'api/remove_from_cart.php', true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) location.reload();
            else alert("Failed to remove product from cart.");
        }
    };
    xhr.send('product_id=' + productId + '&restaurant=' + encodeURIComponent(restaurant));
}

// ============================================================================
// LAYOUT FUNCTIONS
// ============================================================================

/**
 * Apply a 3-column grid layout to product listings
 * This ensures consistent layout across all product pages
 */
function applyThreeColumnGrid() {
    const candidateContainers = new Set();

    // Find known product listing containers by ID or class
    document.querySelectorAll(
        "#featured-products, .items-grid, .menu-grid, .products-grid, .menu-items, .product-list"
    ).forEach((el) => candidateContainers.add(el));

    // Also find containers by looking at parent elements of product cards
    document.querySelectorAll(".menu-item, .product-item, .product-card, .food-item")
        .forEach((item) => {
            if (item.parentElement) candidateContainers.add(item.parentElement);
        });

    // Apply 3-column grid class to all identified containers
    candidateContainers.forEach((container) => {
        container.classList.add("items-grid-3x3");
    });
}

/**
 * Show a brief toast notification message
 * 
 * @param {string} message - The message to display
 */
function showNotification(message) {
    const el = document.getElementById('notification');
    if (!el) return;
    el.textContent = message;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 3000);
}


