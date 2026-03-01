# Smart Dine - Code Documentation

This document provides an overview of all files in the Smart Dine project and their purposes.

## Project Structure

```
smartdine/
├── api/                    # Backend API endpoints
├── assets/                 # Static files (CSS, JS, images)
├── admin/                  # Admin panel pages
├── data/                   # Database files and SQL scripts
├── docs/                   # Documentation
├── includes/               # Reusable PHP components
├── pages/                  # User-facing pages
├── index.php               # Landing page
└── home.php                # Main homepage after login
```

## API Files (`/api/`)

### `add_to_cart.php`
- **Purpose**: Adds products to the shopping cart
- **Method**: POST
- **Parameters**: `product_id`, `restaurant`
- **Returns**: JSON success/failure response
- **How it works**: Stores cart items in PHP session

### `remove_from_cart.php`
- **Purpose**: Removes products from the shopping cart
- **Method**: POST
- **Parameters**: `product_id`, `restaurant`
- **Returns**: JSON success/failure response
- **How it works**: Removes item from session cart array

### `update_cart_quantity.php`
- **Purpose**: Updates the quantity of a cart item
- **Method**: POST
- **Parameters**: `product_id`, `quantity`, `restaurant`
- **Returns**: JSON success/failure response
- **How it works**: Modifies quantity in session cart array

### `get_cart_count.php`
- **Purpose**: Returns the number of items in cart
- **Method**: GET
- **Returns**: JSON with cart count
- **How it works**: Counts items in session cart array
- **Used by**: Navigation badge to show cart item count

### `process_payment.php`
- **Purpose**: Simulates payment processing (educational only)
- **Method**: POST
- **Parameters**: `card_number`, `expiry`, `cvv`, `amount`
- **Returns**: JSON with transaction ID or error
- **How it works**: 
  - Validates card using Luhn algorithm
  - Validates expiry date
  - Validates CVV format
  - Generates simulated transaction ID
  - 90% success rate for realism

### `create_order.php`
- **Purpose**: Creates an order in the database after payment
- **Method**: POST
- **Parameters**: Customer info, delivery address, transaction_id
- **Returns**: JSON with order ID
- **How it works**:
  1. Fetches cart items from session
  2. Creates order record in `orders` table
  3. Creates order items in `order_items` table
  4. Clears cart from session
  5. Uses database transactions for data integrity

## Include Files (`/includes/`)

### `connect.php`
- **Purpose**: Establishes database connection
- **Technology**: PDO (PHP Data Objects)
- **Configuration**: 
  - Host: localhost
  - Database: smartdine
  - Username: root
  - Password: (empty for local development)
- **Security**: Uses PDO with prepared statements to prevent SQL injection

### `header.php`
- **Purpose**: Common header for all pages
- **Contains**:
  - HTML head with meta tags
  - Google Fonts (Poppins)
  - Navigation menu
  - User info display
  - Cart counter badge
  - Responsive CSS
- **Features**:
  - Sticky navigation
  - Dynamic base path calculation
  - Admin badge for admin users
  - Mobile-responsive design

### `footer.php`
- **Purpose**: Common footer for all pages
- **Contains**:
  - Contact information
  - JavaScript includes
  - Notification function
  - Cart count updater

## Assets

### `assets/js/script.js`
- **Purpose**: Main JavaScript functionality
- **Functions**:
  - `addToCart()` - Adds product to cart via AJAX
  - `removeFromCart()` - Removes product from cart
  - `updateCartCount()` - Updates cart badge
  - `buildFoodImageUrl()` - Builds image URLs
  - `applyMissingImageFallbacks()` - Handles broken images
  - `applyThreeColumnGrid()` - Applies grid layout
  - `loadFeaturedProducts()` - Loads featured items
- **Technology**: Vanilla JavaScript (no jQuery)

### `assets/css/style.css`
- **Purpose**: Global styles for the application
- **Features**:
  - Purple gradient theme
  - Card-based layouts
  - Responsive design
  - Animations and transitions
  - Glass morphism effects

### `assets/images/food pics/`
- **Purpose**: Storage for all product images
- **Format**: JPG images
- **Count**: 65 food images
- **Naming**: Descriptive names (e.g., "Cheeseburger.jpg")

## Database Files (`/data/`)

### `smartdine_updated.sql`
- **Purpose**: Complete database schema
- **Tables**:
  - `products` - Product catalog
  - `users` - User accounts
  - `cart` - Shopping cart (database version)
  - `orders` - Order records
  - `order_items` - Individual items in orders
- **Includes**: Demo users and admin account

### `add_transaction_id.sql`
- **Purpose**: Migration to add transaction tracking
- **Adds**: `transaction_id` column to `orders` table
- **Usage**: Run after initial database setup

### `products.json`
- **Purpose**: JSON backup of product data
- **Used as**: Fallback if database is unavailable

## Page Files

### Authentication (`/pages/auth/`)

#### `login.php`
- **Purpose**: User login page
- **Features**: Username/password authentication
- **Security**: Password verification with PHP's password_verify()

#### `register.php`
- **Purpose**: New user registration
- **Features**: Creates new user account
- **Security**: Password hashing with password_hash()

#### `logout.php`
- **Purpose**: Logs user out
- **How it works**: Destroys session and redirects to login

### Categories (`/pages/categories/`)

#### `appetizers.php`, `main-courses.php`, `desserts.php`, `beverages.php`
- **Purpose**: Display products by category
- **Features**:
  - Filters products by category
  - Shows product cards with images
  - Add to cart buttons
  - Restaurant filtering
- **Data source**: Database or JSON fallback

### User Pages (`/pages/user/`)

#### `cart.php`
- **Purpose**: Shopping cart page
- **Features**:
  - View cart items
  - Update quantities
  - Remove items
  - Proceed to checkout
  - Calculate totals

#### `checkout.php`
- **Purpose**: Checkout and payment page
- **Features**:
  - Delivery information form
  - Credit card payment form
  - Card validation (Luhn algorithm)
  - Real-time input formatting
  - Payment processing
  - Order creation
  - Success confirmation

#### `profile.php`
- **Purpose**: User profile management
- **Features**:
  - View user information
  - Update profile details
  - Change password

#### `search.php`
- **Purpose**: Product search functionality
- **Features**:
  - Search by product name
  - Filter by category
  - Filter by restaurant
  - Display search results

### Orders (`/pages/orders/`)

#### `orders.php`
- **Purpose**: View order history
- **Features**:
  - List all user orders
  - Show order status
  - Order details link
  - Sorted by date (newest first)

#### `order_details.php`
- **Purpose**: View specific order details
- **Features**:
  - Order items list
  - Delivery information
  - Payment information
  - Transaction ID
  - Order status

## Admin Files (`/admin/`)

### `index.php`
- **Purpose**: Admin dashboard
- **Access**: Admin users only
- **Features**: Links to all admin functions

### `manage_products.php`
- **Purpose**: Product management
- **Features**:
  - Add new products
  - Edit existing products
  - Delete products
  - Upload product images

### `manage_orders.php`
- **Purpose**: Order management
- **Features**:
  - View all orders
  - Update order status
  - View order details
  - Filter by status

### `order_detail.php`
- **Purpose**: Detailed order view for admin
- **Features**:
  - Complete order information
  - Customer details
  - Order items
  - Status update

### `manage_users.php`
- **Purpose**: User management
- **Features**:
  - View all users
  - Edit user roles
  - Delete users
  - View user activity

## Root Files

### `index.php`
- **Purpose**: Landing/welcome page
- **Features**: Login/register links, app introduction

### `home.php`
- **Purpose**: Main homepage after login
- **Features**:
  - Restaurant selection
  - Featured products
  - Category navigation
  - Welcome message

### `setup.php`
- **Purpose**: Initial database setup
- **Features**: Creates tables and inserts demo data

## Key Technologies Used

### Backend
- **PHP 7.4+**: Server-side scripting
- **MySQL**: Database management
- **PDO**: Database abstraction layer
- **Sessions**: User state management

### Frontend
- **HTML5**: Page structure
- **CSS3**: Styling and animations
- **JavaScript (ES6)**: Client-side functionality
- **AJAX**: Asynchronous server communication

### Security Features
- **Password Hashing**: bcrypt via password_hash()
- **Prepared Statements**: SQL injection prevention
- **Input Validation**: Client and server-side
- **Session Management**: Secure user authentication
- **XSS Prevention**: htmlspecialchars() for output

## Data Flow

### Adding to Cart
1. User clicks "Add to Cart" button
2. JavaScript calls `addToCart()` function
3. AJAX POST request to `api/add_to_cart.php`
4. Server adds item to session cart
5. Server returns success response
6. JavaScript updates cart count badge
7. User sees confirmation message

### Checkout Process
1. User views cart and clicks "Checkout"
2. User fills delivery and payment forms
3. JavaScript validates card details
4. AJAX POST to `api/process_payment.php`
5. Server validates card (Luhn algorithm)
6. Server generates transaction ID
7. AJAX POST to `api/create_order.php`
8. Server creates order in database
9. Server clears cart from session
10. User redirected to success page

### Order Management
1. Admin views orders in `manage_orders.php`
2. Admin clicks order to view details
3. Admin can update order status
4. Status update saved to database
5. User sees updated status in their orders

## Common Patterns

### Database Queries
```php
// Prepared statement pattern
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
```

### AJAX Requests
```javascript
// XMLHttpRequest pattern
const xhr = new XMLHttpRequest();
xhr.open("POST", "api/endpoint.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.onreadystatechange = function() {
    if (xhr.readyState === 4 && xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        // Handle response
    }
};
xhr.send("param=value");
```

### Session Management
```php
// Start session at top of file
session_start();

// Store data
$_SESSION['user_id'] = $userId;

// Retrieve data
$userId = $_SESSION['user_id'] ?? null;

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```

## Error Handling

### PHP
- Try-catch blocks for database operations
- PDO exception mode enabled
- Graceful fallbacks (database → JSON)
- User-friendly error messages

### JavaScript
- Try-catch for JSON parsing
- Console.error for debugging
- Alert messages for user feedback
- Image fallbacks for broken images

## Performance Optimizations

- **Session-based cart**: Faster than database queries
- **Prepared statements**: Reusable queries
- **Image fallbacks**: Prevents broken image icons
- **Minimal dependencies**: No heavy frameworks
- **CSS animations**: Hardware-accelerated
- **Lazy loading**: Images load as needed

## Future Enhancements

Potential improvements for the project:
- Real payment gateway integration (Stripe, PayPal)
- Email notifications for orders
- SMS notifications
- Product reviews and ratings
- Wishlist functionality
- Order tracking
- Coupon/discount codes
- Multi-language support
- Dark mode
- Progressive Web App (PWA)
