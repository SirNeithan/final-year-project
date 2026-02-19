const FOOD_IMAGE_BASE = "assets/images/food pics/";
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

function buildFoodImageUrl(imageName) {
    if (!imageName || String(imageName).trim() === "") return PLACEHOLDER_IMAGE;
    return FOOD_IMAGE_BASE + encodeURIComponent(String(imageName).trim());
}

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
    // Load featured products on homepage
    loadFeaturedProducts();

    // Update cart count when page loads
    updateCartCount();

    // Apply fallback for missing/broken images on all pages
    applyMissingImageFallbacks();

    // Force item layouts into a 3-column grid
    applyThreeColumnGrid();
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
            const imageUrl = buildFoodImageUrl(product.image);

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

function applyMissingImageFallbacks() {
    document.querySelectorAll("img").forEach((img) => {
        const src = (img.getAttribute("src") || "").trim();

        // Empty or obviously invalid source
        if (!src || /(?:undefined|null)\b/i.test(src)) {
            img.src = PLACEHOLDER_IMAGE;
        }

        // Broken URL fallback
        img.onerror = function () {
            this.onerror = null;
            this.src = PLACEHOLDER_IMAGE;
        };
    });
}

// Function to add a product to the cart
function addToCart(productId, productName, productPrice, restaurant = 'Smart Dine') {
    // Send an AJAX request to add the product to the cart
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "api/add_to_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert(`${productName} added to cart!`);
                    updateCartCount();
                } else {
                    alert(response.message || "Failed to add product to cart.");
                }
            } catch (e) {
                console.error("Error parsing JSON:", e);
            }
        }
    };

    xhr.send('product_id=' + productId + '&restaurant=' + encodeURIComponent(restaurant));
}

// Function to update the cart count displayed on the page
function updateCartCount() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "api/get_cart_count.php", true);

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
    xhr.open("POST", "api/remove_from_cart.php", true);
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

function applyThreeColumnGrid() {
    const candidateContainers = new Set();

    // Known/likely listing containers
    document.querySelectorAll(
        "#featured-products, .items-grid, .menu-grid, .products-grid, .menu-items, .product-list"
    ).forEach((el) => candidateContainers.add(el));

    // Infer container from item cards
    document.querySelectorAll(".menu-item, .product-item, .product-card, .food-item")
        .forEach((item) => {
            if (item.parentElement) candidateContainers.add(item.parentElement);
        });

    candidateContainers.forEach((container) => {
        container.classList.add("items-grid-3x3");
    });
}


