# SmartDine - New Features Documentation

## Overview
SmartDine is a multi-restaurant food ordering platform with comprehensive order management, user profiles, and admin capabilities.

## New Features Added

### 1. **User Profile Management** (`profile.php`)
- View account information (username, role, member since)
- Update email address
- Change password with current password verification
- Secure password hashing with bcrypt

### 2. **Order History** (`orders.php`)
- View all past orders with status tracking
- Order status badges (pending, processing, completed, cancelled)
- Quick order summary (date, amount, items count)
- Link to detailed order view

### 3. **Order Details** (`order_details.php`)
- Complete order information
- Customer and delivery details
- Itemized list of products ordered
- Order status and payment information
- Total calculation with breakdown

### 4. **Admin Dashboard** (`admin/index.php`)
- Statistics overview:
  - Total orders
  - Total users
  - Total products
  - Total revenue
- Recent orders list
- Quick access to management pages

### 5. **Order Management** (`admin/manage_orders.php`)
- View all orders in the system
- Update order status (pending → processing → completed/cancelled)
- Filter and search orders
- View detailed order information

### 6. **Product Management** (`admin/manage_products.php`)
- Add new products with:
  - Name, price, image
  - Category (appetizer, main-course, dessert, beverage)
  - Restaurant assignment
  - Optional description
- Delete existing products
- View all products in organized table

### 7. **Enhanced Checkout** (`checkout.php`)
- Database-backed order creation
- Transaction support for data integrity
- Order items tracking
- Automatic cart clearing after successful order
- Order confirmation with order ID

### 8. **Database Schema Updates** (`data/smartdine_updated.sql`)
- **orders table**: Stores order information
  - Customer details
  - Delivery information
  - Payment method
  - Order status
  - Timestamps
- **order_items table**: Stores individual items per order
  - Product details
  - Quantity
  - Restaurant information
- **Updated cart table**: Added restaurant field and foreign keys

### 9. **Products JSON File** (`data/products.json`)
- 30 sample products across multiple restaurants
- Properly formatted with IDs
- Matches image filenames in assets folder

### 10. **Logout Functionality** (`logout.php`)
- Secure session destruction
- Redirect to landing page

### 11. **Navigation Improvements**
- Added Profile and Orders links to navigation
- Admin Panel link for admin users
- Consistent navigation across all pages
- Restaurant-specific navigation preservation

### 12. **Bug Fixes**
- Fixed missing script.js reference in desserts.php
- Fixed restaurant parameter in category pages
- Fixed checkout.php truncation issue
- Added restaurant parameter to all cart operations
- Consistent addToCart function calls across all pages

## File Structure

```
smartdine/
├── admin/
│   ├── index.php              # Admin dashboard
│   ├── manage_orders.php      # Order management
│   ├── manage_products.php    # Product management
│   └── order_detail.php       # Admin order details view
├── api/
│   ├── add_to_cart.php        # Add items to cart
│   ├── get_cart_count.php     # Get cart item count
│   ├── remove_from_cart.php   # Remove items from cart
│   └── update_cart_quantity.php # Update item quantities
├── data/
│   ├── products.json          # Product data (NEW)
│   ├── smartdine.sql          # Original schema
│   └── smartdine_updated.sql  # Updated schema with orders (NEW)
├── profile.php                # User profile page (NEW)
├── orders.php                 # Order history page (NEW)
├── order_details.php          # Order details page (NEW)
├── logout.php                 # Logout functionality (NEW)
└── ... (other existing files)
```

## Database Setup

### Option 1: Fresh Installation
Run the updated SQL file:
```sql
source data/smartdine_updated.sql
```

### Option 2: Update Existing Database
```sql
-- Add orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    delivery_address TEXT NOT NULL,
    delivery_city VARCHAR(100) NOT NULL,
    delivery_zip VARCHAR(20) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'credit_card',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add order_items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price VARCHAR(50) NOT NULL,
    quantity INT DEFAULT 1,
    restaurant VARCHAR(100) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Update cart table
ALTER TABLE cart ADD COLUMN restaurant VARCHAR(100) NOT NULL AFTER quantity;
```

## User Roles

### Regular User
- Browse restaurants and products
- Add items to cart
- Place orders
- View order history
- Manage profile

### Admin User
- All user capabilities
- Access admin dashboard
- Manage all orders (update status)
- Add/delete products
- View system statistics
- View all user orders

## Demo Credentials

### Users
- Username: `demo1` to `demo4`
- Password: `password`

### Admin
- Username: `admin`
- Password: `admin123`

## Order Status Flow

1. **Pending** - Order just placed
2. **Processing** - Order being prepared
3. **Completed** - Order delivered
4. **Cancelled** - Order cancelled

## Security Features

- Password hashing with bcrypt
- Session-based authentication
- Role-based access control
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars()
- CSRF protection recommended for production

## Future Enhancements (Recommended)

1. **Email Notifications**
   - Order confirmation emails
   - Status update notifications
   - Password reset emails

2. **Payment Integration**
   - Real payment gateway (Stripe, PayPal, etc.)
   - Payment verification
   - Receipt generation

3. **Advanced Features**
   - Product reviews and ratings
   - Wishlist/favorites
   - Order tracking with real-time updates
   - Delivery time estimation
   - Multiple delivery addresses
   - Promo codes and discounts

4. **Security Enhancements**
   - CSRF tokens
   - Rate limiting
   - Two-factor authentication
   - Password reset functionality

5. **UI/UX Improvements**
   - Responsive design optimization
   - Loading indicators
   - Better error handling
   - Image upload for products
   - Product search with filters

## Notes

- All prices are in UGX (Ugandan Shillings)
- Cart is session-based (clears on logout)
- Images should be placed in `assets/images/food pics/`
- Admin panel is accessible only to users with role='admin'
